<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ProcessesQuestionData
{
    private $constants = [
        'g' => 9.8,
        'pi' => M_PI,
    ];

    protected function generateRandomValues($content, $rumus, $question)
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
        if (isset($question->random_ranges) && is_array($question->random_ranges)) {
            foreach ($question->random_ranges as $range) {
                if (empty($range)) continue;
                
                $variabel = trim($range['variabel'] ?? '');
                if ($variabel && isset($range['min_value']) && isset($range['max_value'])) {
                    $randomRanges['%' . $variabel . '%'] = [
                        'min_value' => floatval($range['min_value']),
                        'max_value' => floatval($range['max_value']),
                        'type' => strtolower(trim($range['type'] ?? 'integer')),
                    ];
                    Log::info('Added random range', [
                        'variabel' => $variabel,
                        'range' => $randomRanges['%' . $variabel . '%']
                    ]);
                }
            }
        }

        foreach ($placeholders as $placeholder) {
            if (preg_match('/%variabelhasil\d+%/', $placeholder)) {
                continue;
            }

            $cleanPlaceholder = trim($placeholder, '%');
            $matchingRange = null;
            
            foreach ($randomRanges as $rangeKey => $range) {
                if (trim($rangeKey, '%') === $cleanPlaceholder) {
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
                    $randomValues[$placeholder] = round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), 2);
                    Log::info('Generated decimal value', [
                        'placeholder' => $placeholder,
                        'clean_placeholder' => $cleanPlaceholder,
                        'min' => $min,
                        'max' => $max,
                        'value' => $randomValues[$placeholder],
                        'type' => $type
                    ]);
                } else {
                    $randomValues[$placeholder] = rand((int)$min, (int)$max);
                    Log::info('Generated integer value', [
                        'placeholder' => $placeholder,
                        'clean_placeholder' => $cleanPlaceholder,
                        'min' => $min,
                        'max' => $max,
                        'value' => $randomValues[$placeholder],
                        'type' => $type
                    ]);
                }
            } else {
                $randomValues[$placeholder] = rand(1, 100);
                Log::warning('No range defined for placeholder', [
                    'placeholder' => $placeholder,
                    'clean_placeholder' => $cleanPlaceholder,
                    'value' => $randomValues[$placeholder]
                ]);
            }
        }
        return $randomValues;
    }

    protected function replacePlaceholders($content, $values)
    {
        foreach ($values as $placeholder => $value) {
            $content = str_replace($placeholder, (string)$value, $content);
        }
        return $content;
    }

    protected function processRumus($rumus, $randomValues, $question)
    {
        if (empty($rumus) || !is_array($rumus)) {
            Log::warning('Rumus is empty or not an array', ['rumus' => $rumus]);
            return [];
        }

        $rumusValues = [];
        $precision = $question->precision ?? 6; // Default presisi 6 digit

        foreach ($rumus as $index => $item) {
            if (is_array($item)) {
                $key = array_key_first($item);
                if ($key && isset($item[$key])) {
                    $expression = $item[$key];
                    Log::info('Processing rumus expression', [
                        'original_expression' => $expression,
                        'index' => $index
                    ]);

                    // Ganti placeholder dengan nilai acak
                    foreach ($randomValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        Log::info('After replacing random value', [
                            'placeholder' => $placeholder,
                            'value' => $value,
                            'expression' => $expression,
                        ]);
                    }

                    // Ganti placeholder hasil rumus sebelumnya
                    foreach ($rumusValues as $placeholder => $value) {
                        $expression = str_replace($placeholder, (string)$value, $expression);
                        Log::info('After replacing previous result', [
                            'placeholder' => $placeholder,
                            'value' => $value,
                            'expression' => $expression,
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

                    // Perbaikan format ekspresi
                    $expression = $this->formatExpression($expression);
                    Log::info('After formatting expression', ['expression' => $expression]);

                    // Evaluasi ekspresi
                    $result = $this->evaluateExpression($expression);
                    Log::info('After evaluating expression', [
                        'expression' => $expression,
                        'result' => $result
                    ]);

                    if ($result === 'Error') {
                        Log::error('Evaluation failed for expression', ['expression' => $expression]);
                        return [];
                    }

                    // Format hasil dengan presisi yang sesuai
                    if (is_numeric($result)) {
                        // Jika hasil sangat kecil atau sangat besar, gunakan notasi ilmiah
                    if (abs($result) > 1000000 || (abs($result) > 0 && abs($result) < 0.000001)) {
                            $result = sprintf('%.6e', $result);
                    } else {
                            // Untuk nilai normal, gunakan presisi yang ditentukan
                        $result = round($result, $precision);
                        }
                    }

                    $rumusValues['%variabelhasil' . ($index + 1) . '%'] = $result;
                    Log::info('Final result for rumus', [
                        'index' => $index,
                        'result' => $result,
                        'precision' => $precision
                    ]);
                }
            }
        }
        return $rumusValues;
    }

    protected function formatExpression($expression)
    {
        // Hapus spasi
        $expression = preg_replace('/\s+/', '', $expression);
        
        // Perbaiki format angka desimal
        $expression = str_replace(',', '.', $expression);
            $expression = preg_replace('/^\.([0-9]+)/', '0.$1', $expression);
            $expression = preg_replace('/\(\.([0-9]+)/', '(0.$1', $expression);
            $expression = preg_replace('/([+\-*\/])\.([0-9]+)/', '$10.$2', $expression);
        
        // Perbaiki perkalian implisit (contoh: 2(3) menjadi 2*(3))
        $expression = preg_replace('/([0-9\.]+)\(([0-9\.]+)\)/', '$1*($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\(([^)]+)\)/', '$1*($2)', $expression);
        
        // Perbaiki tanda kurung yang berlebihan
        $expression = preg_replace('/\(\(([^()]+)\)\)/', '($1)', $expression);
        
        // Tambahkan tanda kurung untuk memastikan urutan operasi yang benar
        $expression = $this->addParenthesesForOrder($expression);
        
        Log::info('Formatted expression', [
            'original' => $expression,
            'formatted' => $expression
        ]);
        
        return $expression;
    }

    protected function addParenthesesForOrder($expression)
    {
        // Tambahkan tanda kurung untuk operasi pangkat
        $expression = preg_replace('/([0-9\.]+)\*\*([0-9\.]+)/', '($1)**($2)', $expression);
        
        // Tambahkan tanda kurung untuk operasi perkalian dan pembagian
        $expression = preg_replace('/([0-9\.]+)\*([0-9\.]+)/', '($1)*($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\/([0-9\.]+)/', '($1)/($2)', $expression);
        
        // Tambahkan tanda kurung untuk operasi penjumlahan dan pengurangan
        $expression = preg_replace('/([0-9\.]+)\+([0-9\.]+)/', '($1)+($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\-([0-9\.]+)/', '($1)-($2)', $expression);
        
        return $expression;
    }

    protected function evaluateExpression($expression)
    {
        try {
            Log::info('Evaluating expression', ['expression' => $expression]);

            // Validasi ekspresi - izinkan fungsi trigonometri dan fungsi matematika
            if (!preg_match('/^[0-9\.\s+\-*\/()a-z]+$/', $expression)) {
                throw new \Exception('Invalid expression format');
            }

            // Konversi derajat ke radian untuk fungsi trigonometri
            $expression = preg_replace_callback('/sin\(([^)]+)\)/', function($matches) {
                return 'sin(deg2rad(' . $matches[1] . '))';
            }, $expression);
            
            $expression = preg_replace_callback('/cos\(([^)]+)\)/', function($matches) {
                return 'cos(deg2rad(' . $matches[1] . '))';
            }, $expression);
            
            $expression = preg_replace_callback('/tan\(([^)]+)\)/', function($matches) {
                return 'tan(deg2rad(' . $matches[1] . '))';
            }, $expression);

            // Evaluasi ekspresi
            $result = eval('return ' . $expression . ';');

            if (!is_numeric($result)) {
                throw new \Exception('Expression did not evaluate to a number');
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error evaluating expression', [
                'expression' => $expression,
                'error' => $e->getMessage()
            ]);
            return 'Error';
        }
    }

    protected function replacePlaceholdersInAnswers($answersJson, $randomValues, $rumusValues)
    {
        if (empty($answersJson)) {
            return [];
        }

        $answers = is_array($answersJson) ? $answersJson : json_decode($answersJson, true);

        if (empty($answers)) {
            return [];
        }

        foreach ($answers as & $answer) {
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