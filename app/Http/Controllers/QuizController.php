<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\UserQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\ProcessesQuestionData;

class QuizController extends Controller
{
    use ProcessesQuestionData;

    private $constants = [
        'g' => 9.8,
        'pi' => M_PI,
    ];

    public function startQuiz(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $userAttempts = UserQuiz::where('user_id', auth()->id())
            ->where('quiz_id', $quizId)
            ->count();

        if ($userAttempts >= $quiz->attempt_limit) {
            return redirect()->route('quiz.list')->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        $userQuiz = UserQuiz::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quizId,
            'started_at' => now(),
        ]);

        $questions = $quiz->questions->all();
        shuffle($questions);

        $questions = collect($questions)->map(function ($question) use ($userQuiz) {
            $randomValues = $this->generateRandomValues($question->content, $question->rumus, $question);
            $questionText = $this->replacePlaceholders($question->content, $randomValues);
            $rumusValues = $this->processRumus($question->rumus, $randomValues, $question);
            $answers = $this->replacePlaceholdersInAnswers($question->answers, $randomValues, $rumusValues);
            shuffle($answers);

            // Log::info('Final answers array: ' . json_encode($answers));

            return $userQuiz->userQuestions()->create([
                'question_id' => $question->id,
                'question_text' => $questionText,
                'random_values' => json_encode($randomValues),
                'answers' => json_encode($answers),
                'image_path' => $question->image_path,
            ]);
        });

        return redirect()->route('quiz.question', ['userQuizId' => $userQuiz->id, 'questionIndex' => 0]);
    }

    private function generateRandomValues($content, $rumus, $question)
    {
        $randomValues = [];

        preg_match_all('/%[a-zA-Z][a-zA-Z0-9_]*%/', $content, $contentMatches);
        $placeholders = $contentMatches[0];

        if (!empty($rumus) && is_array($rumus)) {
            foreach ($rumus as $item) {
                if (is_array($item)) {
                    $key = array_key_first($item);
                    if ($key && isset($item[$key])) {
                        preg_match_all('/%[a-zA-Z][a-zA-Z0-9_]*%/', $item[$key], $rumusMatches);
                        $placeholders = array_merge($placeholders, $rumusMatches[0]);
                    }
                }
            }
        }

        $placeholders = array_unique($placeholders);

        $randomRanges = [];
        if ($question->random_ranges) {
            $ranges = explode(';', $question->random_ranges);
            foreach ($ranges as $range) {
                if (empty($range)) continue;
                $parts = explode('|', $range);
                if (count($parts) === 4) {
                    $variabel = trim(str_replace('%', '', $parts[0]));
                    $randomRanges['%' . $variabel . '%'] = [
                        'min_value' => floatval($parts[1]),
                        'max_value' => floatval($parts[2]),
                        'type' => trim($parts[3]),
                    ];
                    // Log::info('Added random range in QuizController', [
                    //     'variabel' => $variabel,
                    //     'range' => $randomRanges['%' . $variabel . '%'],
                    //     'original_parts' => $parts
                    // ]);
                }
            }
        }

        foreach ($placeholders as $placeholder) {
            if (preg_match('/%variabelhasil\d+%/', $placeholder)) {
                continue;
            }

            $cleanPlaceholder = trim(str_replace('%', '', $placeholder));
            $matchingRange = null;
            
            foreach ($randomRanges as $rangeKey => $range) {
                $cleanRangeKey = trim(str_replace('%', '', $rangeKey));
                if ($cleanRangeKey === $cleanPlaceholder) {
                    $matchingRange = $range;
                    break;
                }
            }

            if ($matchingRange) {
                $min = floatval($matchingRange['min_value']);
                $max = floatval($matchingRange['max_value']);
                $type = strtolower(trim($matchingRange['type']));

                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }

                if ($type === 'decimal') {
                    $randomValues[$placeholder] = round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), 6);
                    // Log::info('Generated decimal value in QuizController', [
                    //     'placeholder' => $placeholder,
                    //     'clean_placeholder' => $cleanPlaceholder,
                    //     'min' => $min,
                    //     'max' => $max,
                    //     'value' => $randomValues[$placeholder],
                    //     'type' => $type
                    // ]);
                } else {
                    $randomValues[$placeholder] = rand((int)$min, (int)$max);
                    // Log::info('Generated integer value in QuizController', [
                    //     'placeholder' => $placeholder,
                    //     'clean_placeholder' => $cleanPlaceholder,
                    //     'min' => $min,
                    //     'max' => $max,
                    //     'value' => $randomValues[$placeholder],
                    //     'type' => $type
                    // ]);
                }
            } else {
                $randomValues[$placeholder] = rand(1, 100);
                // Log::warning('No range defined for placeholder in QuizController', [
                //     'placeholder' => $placeholder,
                //     'clean_placeholder' => $cleanPlaceholder,
                //     'value' => $randomValues[$placeholder],
                //     'available_ranges' => array_keys($randomRanges)
                // ]);
            }
        }

        // Log::info('Generated Random Values in QuizController', ['randomValues' => $randomValues]);
        return $randomValues;
    }

    private function replacePlaceholders($content, $randomValues)
    {
        foreach ($randomValues as $placeholder => $value) {
            $content = str_replace($placeholder, (string)$value, $content);
        }

        return $content;
    }

    private function processRumus($rumus, $randomValues, $question)
    {
        if (empty($rumus) || !is_array($rumus)) {
            // Log::warning('Rumus is empty or not an array', ['rumus' => $rumus]);
            return [];
        }

        $rumusValues = [];
        $precision = isset($question->precision) ? max($question->precision, 6) : 6; // Minimal 6 digit

        foreach ($rumus as $index => $item) {
            if (is_array($item)) {
                $key = array_key_first($item);
                if ($key && isset($item[$key])) {
                    $expression = $item[$key];
                    // Log::info('Processing rumus expression', [
                    //     'original_expression' => $expression,
                    //     'index' => $index
                    // ]);

                    foreach ($randomValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        // Log::info('After replacing random value', [
                        //     'placeholder' => $placeholder,
                        //     'value' => $value,
                        //     'expression' => $expression,
                        // ]);
                    }

                    foreach ($rumusValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        // Log::info('After replacing previous result', [
                        //     'placeholder' => $placeholder,
                        //     'value' => $value,
                        //     'expression' => $expression,
                        // ]);
                    }

                    foreach ($this->constants as $constant => $value) {
                        $expression = preg_replace('/\b' . preg_quote($constant, '/') . '\b/', (string)$value, $expression);
                        // Log::info('After replacing constant', [
                        //     'constant' => $constant,
                        //     'value' => $value,
                        //     'expression' => $expression,
                        // ]);
                    }

                    $expression = $this->formatExpression($expression);
                    // Log::info('After formatting expression', ['expression' => $expression]);

                    $result = $this->evaluateExpression($expression);
                    // Log::info('After evaluating expression', [
                    //     'expression' => $expression,
                    //     'result' => $result
                    // ]);

                    if ($result === 'Error') {
                        // Log::error('Evaluation failed for expression', ['expression' => $expression]);
                        return [];
                    }

                    if (is_numeric($result)) {
                        if (abs($result) > 1000000 || (abs($result) > 0 && abs($result) < 0.000001)) {
                            $result = sprintf('%.6e', $result);
                        } else {
                            $result = round($result, $precision);
                        }
                    }

                    $rumusValues['%variabelhasil' . ($index + 1) . '%'] = $result;
                    // Log::info('Final result for rumus', [
                    //     'index' => $index,
                    //     'result' => $result,
                    //     'precision' => $precision
                    // ]);
                }
            }
        }
        return $rumusValues;
    }

    private function formatExpression($expression)
    {
            $expression = preg_replace('/\s+/', '', $expression);
            
        $expression = str_replace(',', '.', $expression);
            $expression = preg_replace('/^\.([0-9]+)/', '0.$1', $expression);
            $expression = preg_replace('/\(\.([0-9]+)/', '(0.$1', $expression);
            $expression = preg_replace('/([+\-*\/])\.([0-9]+)/', '$10.$2', $expression);
            
        $expression = preg_replace('/([0-9\.]+)\(([0-9\.]+)\)/', '$1*($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\(([^)]+)\)/', '$1*($2)', $expression);
        
        $expression = preg_replace('/\(\(([^()]+)\)\)/', '($1)', $expression);
        
        $expression = $this->addParenthesesForOrder($expression);
        
        // Log::info('Formatted expression', [
        //     'original' => $expression,
        //     'formatted' => $expression
        // ]);
        
        return $expression;
    }

    private function addParenthesesForOrder($expression)
    {
        $expression = preg_replace('/([0-9\.]+)\*\*([0-9\.]+)/', '($1)**($2)', $expression);
        
        $expression = preg_replace('/([0-9\.]+)\*([0-9\.]+)/', '($1)*($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\/([0-9\.]+)/', '($1)/($2)', $expression);
        
        $expression = preg_replace('/([0-9\.]+)\+([0-9\.]+)/', '($1)+($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\-([0-9\.]+)/', '($1)-($2)', $expression);
        
        return $expression;
    }

    private function evaluateExpression($expression)
    {
        try {
            // Log::info('Evaluating expression', ['expression' => $expression]);

            if (!preg_match('/^[0-9\.\s+\-*\/()a-z]+$/', $expression)) {
                throw new \Exception('Invalid expression format');
            }

            $expression = preg_replace_callback('/sin\(([^)]+)\)/', function($matches) {
                return 'sin(deg2rad(' . $matches[1] . '))';
            }, $expression);
            
            $expression = preg_replace_callback('/cos\(([^)]+)\)/', function($matches) {
                return 'cos(deg2rad(' . $matches[1] . '))';
            }, $expression);
            
            $expression = preg_replace_callback('/tan\(([^)]+)\)/', function($matches) {
                return 'tan(deg2rad(' . $matches[1] . '))';
            }, $expression);

            $expression = preg_replace_callback('/log\(([^)]+)\)/', function($matches) {
                return 'log10(' . $matches[1] . ')';
            }, $expression);

            $expression = preg_replace_callback('/ln\(([^)]+)\)/', function($matches) {
                return 'log(' . $matches[1] . ')';
            }, $expression);

            $result = eval('return ' . $expression . ';');

            if (!is_numeric($result)) {
                throw new \Exception('Expression did not evaluate to a number');
            }

            return $result;
        } catch (\Exception $e) {
            // Log::error('Error evaluating expression', [
            //     'expression' => $expression,
            //     'error' => $e->getMessage()
            // ]);
            return 'Error';
        }
    }

    private function replacePlaceholdersInAnswers($answersJson, $randomValues, $rumusValues)
    {
        if (empty($answersJson)) {
            // Log::warning('answersJson is empty or null');
            return [];
        }

        $answers = is_array($answersJson) ? $answersJson : json_decode($answersJson, true);

        if (empty($answers)) {
            // Log::warning('answers is empty or null after json_decode');
            return [];
        }

        foreach ($answers as &$answer) {
            $content = $answer['content'];

            foreach ($randomValues as $placeholder => $value) {
                $content = str_replace($placeholder, (string)$value, $content);
            }

            foreach ($rumusValues as $placeholder => $value) {
                if (strpos($content, $placeholder) !== false) {
                    $content = str_replace($placeholder, (string)$value, $content);
                    // Log::info('Replaced Variabelhasil in Answer', [
                    //     'placeholder' => $placeholder,
                    //     'replaced_with' => $value,
                    //     'new_content' => $content,
                    // ]);
                }
            }

            $answer['content'] = $content;
        }

        return $answers;
    }
}