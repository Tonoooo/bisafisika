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

            Log::info('Final answers array: ' . json_encode($answers));

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
                    } else {
                        Log::warning('Invalid rumus item format', ['item' => $item]);
                    }
                } else {
                    Log::warning('Rumus item is not an array', ['item' => $item]);
                }
            }
        } else {
            if (!empty($rumus)) {
                Log::warning('Rumus is not an array', ['rumus' => $rumus]);
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
                    Log::warning('Min value exceeds max value for ' . $placeholder . ', swapping values.');
                    [$min, $max] = [$max, $min];
                }

                if ($type === 'decimal') {
                    $randomValues[$placeholder] = round($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);
                } else {
                    $randomValues[$placeholder] = rand((int)$min, (int)$max);
                }
            } else {
                $randomValues[$placeholder] = rand(1, 100);
                Log::warning('No range defined for placeholder ' . $placeholder . ', using default 1-100');
            }
        }

        Log::info('Generated Random Values', ['randomValues' => $randomValues]);

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
            Log::warning('Rumus is empty or not an array', ['rumus' => $rumus]);
            return [];
        }

        $rumusValues = [];
        $precision = $question->precision ?? 3; // Default presisi 3 desimal

        foreach ($rumus as $index => $item) {
            if (is_array($item)) {
                $key = array_key_first($item);
                if ($key && isset($item[$key])) {
                    $expression = $item[$key];

                    // Ganti placeholder %randomnumberX% atau variabel acak lainnya dengan nilai acak
                    foreach ($randomValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        Log::info('After replacing placeholder', [
                            'placeholder' => $placeholder,
                            'value' => $value,
                            'expression' => $expression,
                        ]);
                    }

                    // Ganti placeholder %variabelhasilX% dengan hasil rumus sebelumnya
                    foreach ($rumusValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        Log::info('Replaced Variabelhasil in Rumus', [
                            'placeholder' => $placeholder,
                            'replaced_with' => $value,
                            'new_expression' => $expression,
                        ]);
                    }

                    // Ganti konstanta
                    foreach ($this->constants as $constant => $value) {
                        $expression = str_replace($constant, (string)$value, $expression);
                        Log::info('After replacing constant', [
                            'constant' => $constant,
                            'value' => $value,
                            'expression' => $expression,
                        ]);
                    }

                    // Perbaiki tanda kurung yang tidak seimbang
                    $openParens = substr_count($expression, '(');
                    $closeParens = substr_count($expression, ')');
                    if ($openParens > $closeParens) {
                        $expression .= str_repeat(')', $openParens - $closeParens);
                        Log::warning('Added missing closing parentheses', ['original' => $item[$key], 'corrected' => $expression]);
                    }

                    // Ganti ** dengan pow(base, exponent) untuk mendukung pangkat umum
                    $expression = preg_replace('/([0-9\.]+)\*\*([-\d\.]+)/', 'pow($1, $2)', $expression);
                    Log::info('After converting ** to pow', ['expression' => $expression]);

                    // Ganti koma dengan titik untuk desimal
                    $expression = str_replace(',', '.', $expression);

                    // Pastikan ekspresi valid sebelum evaluasi
                    $expression = str_replace('/100', '/100.0', $expression); // Hindari pembagian integer
                    $expression = preg_replace('/\s+/', '', $expression); // Hapus spasi

                    // Konversi fungsi trigonometri
                    if (preg_match_all('/(sin|cos|tan|sqrt)\s*\(\s*([0-9\.]+)\s*\)/', $expression, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $function = $match[1];
                            $arg = trim($match[2]);
                            if ($function === 'sqrt') {
                                $funcResult = sqrt($arg);
                            } else {
                                $radianArg = $arg * M_PI / 180;
                                $funcResult = call_user_func($function, $radianArg);
                            }
                            $oldPattern = "$function($arg)";
                            $expression = str_replace($oldPattern, $funcResult, $expression);
                            Log::info('Evaluated function', [
                                'function' => $function,
                                'argument' => $arg,
                                'result' => $funcResult,
                                'new_expression' => $expression,
                            ]);
                        }
                    }

                    // Sanitasi string setelah konversi fungsi
                    $expression = preg_replace('/[^0-9\.\+\-\*\/\(\)]/', '', $expression);
                    Log::info('Sanitized expression', ['expression' => $expression]);

                    // Evaluasi ekspresi
                    $result = $this->evaluateExpression($expression);

                    if ($result === 'Error') {
                        Log::error('Evaluation failed for expression', ['expression' => $expression]);
                        return [];
                    }

                    // Log hasil mentah
                    Log::info('Raw result before formatting', ['result' => $result]);

                    // Format sesuai presisi
                    if (abs($result) > 1000000 || (abs($result) > 0 && abs($result) < 0.000001)) {
                        $result = sprintf('%.2e', $result);
                        Log::info('Formatted to scientific notation', ['original' => $result, 'formatted' => $result]);
                    } else {
                        $result = round($result, $precision);
                        Log::info('Formatted to decimal notation', ['original' => $result, 'formatted' => $result]);
                    }

                    // Simpan hasil
                    $rumusValues['%variabelhasil' . ($index + 1) . '%'] = $result;

                    Log::info('Processed Rumus', [
                        'key' => $key,
                        'original_expression' => $item[$key],
                        'processed_expression' => $expression,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('Invalid rumus item format', ['item' => $item]);
                }
            } else {
                Log::warning('Rumus item is not an array', ['item' => $item]);
            }
        }

        return $rumusValues;
    }

    private function evaluateExpression($expression)
    {
        try {
            Log::info('Expression before processing', ['expression' => $expression]);

            // Validasi tanda kurung
            $openParens = substr_count($expression, '(');
            $closeParens = substr_count($expression, ')');
            if ($openParens !== $closeParens) {
                throw new \Exception('Unbalanced parentheses in expression: ' . $expression);
            }

            // Validasi karakter yang diizinkan
            if (!preg_match('/^[0-9\.\s+\-*\/()]+$/i', $expression)) {
                preg_match_all('/[^0-9\.\s+\-*\/()]/i', $expression, $matches);
                throw new \Exception('Invalid characters in expression: ' . implode(', ', $matches[0]));
            }

            Log::info('Evaluating Expression', ['expression' => $expression]);

            $result = eval('return ' . $expression . ';');

            if (!is_numeric($result)) {
                throw new \Exception('Expression did not evaluate to a number');
            }

            return round($result, 6);
        } catch (\Exception $e) {
            Log::error('Error evaluating expression', [
                'expression' => $expression,
                'error' => $e->getMessage(),
            ]);
            return 'Error';
        }
    }

    private function replacePlaceholdersInAnswers($answersJson, $randomValues, $rumusValues)
    {
        if (empty($answersJson)) {
            Log::warning('answersJson is empty or null');
            return [];
        }

        $answers = is_array($answersJson) ? $answersJson : json_decode($answersJson, true);

        if (empty($answers)) {
            Log::warning('answers is empty or null after json_decode');
            return [];
        }

        foreach ($answers as &$answer) {
            $content = $answer['content'];

            // Ganti %randomnumberX% atau variabel acak lainnya
            foreach ($randomValues as $placeholder => $value) {
                $content = str_replace($placeholder, (string)$value, $content);
            }

            // Ganti %variabelhasilX% satu per satu untuk menghindari penggantian berulang
            foreach ($rumusValues as $placeholder => $value) {
                if (strpos($content, $placeholder) !== false) {
                    $content = str_replace($placeholder, (string)$value, $content);
                    Log::info('Replaced Variabelhasil in Answer', [
                        'placeholder' => $placeholder,
                        'replaced_with' => $value,
                        'new_content' => $content,
                    ]);
                }
            }

            $answer['content'] = $content;
        }

        return $answers;
    }
}