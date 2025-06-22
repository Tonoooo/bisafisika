<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\UserQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
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
                if (empty($range)) {
                    continue;
                }
                $parts = explode('|', $range);
                if (count($parts) === 4) {
                    $randomRanges[$parts[0]] = [
                        'min_value' => floatval($parts[1]),
                        'max_value' => floatval($parts[2]),
                        'type' => $parts[3],
                    ];
                }
            }
        }

        foreach ($placeholders as $placeholder) {
            if (preg_match('/%variabelhasil\d+%/', $placeholder)) {
                continue;
            }

            if (isset($randomRanges[$placeholder])) {
                $min = floatval($randomRanges[$placeholder]['min_value']);
                $max = floatval($randomRanges[$placeholder]['max_value']);
                $type = $randomRanges[$placeholder]['type'] ?? 'integer';

                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }

                if ($type === 'decimal') {
                    $randomValues[$placeholder] = round($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);
                } else {
                    $randomValues[$placeholder] = rand((int)$min, (int)$max);
                }
            } else {
                $randomValues[$placeholder] = rand(1, 100);
            }
        }


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
            return [];
        }

        $rumusValues = [];
        $precision = $question->precision ?? 3; 

        foreach ($rumus as $index => $item) {
            if (is_array($item)) {
                $key = array_key_first($item);
                if ($key && isset($item[$key])) {
                    $expression = $item[$key];

                    foreach ($randomValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        
                    }

                    foreach ($rumusValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        
                    }

                    foreach ($this->constants as $constant => $value) {
                        $expression = str_replace($constant, (string)$value, $expression);
                        
                    }

                    $openParens = substr_count($expression, '(');
                    $closeParens = substr_count($expression, ')');
                    if ($openParens > $closeParens) {
                        $expression .= str_repeat(')', $openParens - $closeParens);
                    }

                    $expression = str_replace(',', '.', $expression);

                    $expression = preg_replace('/\s+/', '', $expression);
                    
                    $expression = preg_replace('/^\.([0-9]+)/', '0.$1', $expression);
                    
                    $expression = preg_replace('/\(\.([0-9]+)/', '(0.$1', $expression);
                    
                    $expression = preg_replace('/([+\-*\/])\.([0-9]+)/', '$10.$2', $expression);
                    
                    $expression = preg_replace('/([0-9]+)\.([0-9]+)\.([0-9]+)/', '$1.$2$3', $expression);

                    while (preg_match('/(sin|cos|tan|sqrt)\(([0-9\.\*\/\+\-]+)\)/', $expression, $matches)) {
                        $function = $matches[1];
                        $arg = $matches[2];
                        
                        $argValue = eval('return ' . $arg . ';');
                        
                        if ($function !== 'sqrt' && strpos($arg, 'pi/180') !== false) {
                            $argValue = $argValue * M_PI / 180;
                        }
                        
                        if ($function === 'sqrt') {
                            if ($argValue < 0) {
                                $result = sqrt(abs($argValue));
                                
                            } else {
                                $result = sqrt($argValue);
                            }
                        } else {
                            $result = call_user_func($function, $argValue);
                        }
                        
                        $expression = str_replace($matches[0], $result, $expression);
                        
                        
                    }

                    while (preg_match('/([0-9\.]+)\*\*([0-9\.]+)/', $expression, $matches)) {
                        $base = floatval($matches[1]);
                        $exponent = floatval($matches[2]);
                        $result = pow($base, $exponent);
                        $expression = str_replace($matches[0], $result, $expression);
                        
                    }


                    if (!preg_match('/^[0-9\.\s+\-*\/()]+$/', $expression)) {
                        throw new \Exception('Invalid expression format after processing');
                    }

                    $result = $this->evaluateExpression($expression);

                    if ($result === 'Error') {
                        return [];
                    }


                    if (abs($result) > 1000000 || (abs($result) > 0 && abs($result) < 0.000001)) {
                        $result = sprintf('%.2e', $result);
                    } else {
                        $result = round($result, $precision);
                    }

                    $rumusValues['%variabelhasil' . ($index + 1) . '%'] = $result;

                    
                } 
            } 
        }

        return $rumusValues;
    }

    private function evaluateExpression($expression)
    {
        try {

            $openParens = substr_count($expression, '(');
            $closeParens = substr_count($expression, ')');
            if ($openParens !== $closeParens) {
                throw new \Exception('Unbalanced parentheses in expression: ' . $expression);
            }

            if (!preg_match('/^[0-9\.\s+\-*\/()]+$/i', $expression)) {
                preg_match_all('/[^0-9\.\s+\-*\/()]/i', $expression, $matches);
                throw new \Exception('Invalid characters in expression: ' . implode(', ', $matches[0]));
            }

            $expression = preg_replace('/\s+/', '', $expression);
            
            $expression = preg_replace('/^\.([0-9]+)/', '0.$1', $expression);
            
            $expression = preg_replace('/\(\.([0-9]+)/', '(0.$1', $expression);
            
            $expression = preg_replace('/([+\-*\/])\.([0-9]+)/', '$10.$2', $expression);
            
            $expression = preg_replace('/([0-9]+)\.([0-9]+)\.([0-9]+)/', '$1.$2$3', $expression);

            while (preg_match('/(sin|cos|tan|sqrt)\(([0-9\.\*\/\+\-]+)\)/', $expression, $matches)) {
                $function = $matches[1];
                $arg = $matches[2];
                
                $argValue = eval('return ' . $arg . ';');
                
                if ($function !== 'sqrt' && strpos($arg, 'pi/180') !== false) {
                    $argValue = $argValue * M_PI / 180;
                }
                
                if ($function === 'sqrt') {
                    if ($argValue < 0) {
                        $result = sqrt(abs($argValue));
                    } else {
                        $result = sqrt($argValue);
                    }
                } else {
                    $result = call_user_func($function, $argValue);
                }
                
                $expression = str_replace($matches[0], $result, $expression);
                
            }

            while (preg_match('/([0-9\.]+)\*\*([0-9\.]+)/', $expression, $matches)) {
                $base = floatval($matches[1]);
                $exponent = floatval($matches[2]);
                $result = pow($base, $exponent);
                $expression = str_replace($matches[0], $result, $expression);
                
            }


            if (!preg_match('/^[0-9\.\s+\-*\/()]+$/', $expression)) {
                throw new \Exception('Invalid expression format after processing');
            }

            $result = eval('return ' . $expression . ';');

            if (!is_numeric($result)) {
                throw new \Exception('Expression did not evaluate to a number');
            }

            return round($result, 6);
        } catch (\Exception $e) {
            
            return 'Error';
        }
    }

    private function replacePlaceholdersInAnswers($answersJson, $randomValues, $rumusValues)
    {
        if (empty($answersJson)) {
            return [];
        }

        $answers = is_array($answersJson) ? $answersJson : json_decode($answersJson, true);

        if (empty($answers)) {
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
                    
                }
            }

            $answer['content'] = $content;
        }

        return $answers;
    }
}