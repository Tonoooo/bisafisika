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
                    // Log::info('Added random range', [
                    //     'variabel' => $variabel,
                    //     'range' => $randomRanges['%' . $variabel . '%']
                    // ]);
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
                    $randomValues[$placeholder] = round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), 6);
                    // Log::info('Generated decimal value', [
                    //     'placeholder' => $placeholder,
                    //     'clean_placeholder' => $cleanPlaceholder,
                    //     'min' => $min,
                    //     'max' => $max,
                    //     'value' => $randomValues[$placeholder],
                    //     'type' => $type
                    // ]);
                } else {
                    $randomValues[$placeholder] = rand((int)$min, (int)$max);
                    // Log::info('Generated integer value', [
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
                // Log::warning('No range defined for placeholder', [
                //     'placeholder' => $placeholder,
                //     'clean_placeholder' => $cleanPlaceholder,
                //     'value' => $randomValues[$placeholder]
                // ]);
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
                        Log::info('After replacing previous result', [
                            'placeholder' => $placeholder,
                            'value' => $value,
                            'expression' => $expression,
                        ]);
                    }

                    foreach ($this->constants as $constant => $value) {
                        $expression = str_replace($constant, (string)$value, $expression);
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

    protected function formatExpression($expression)
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

    protected function addParenthesesForOrder($expression)
    {
        $expression = preg_replace('/([0-9\.]+)\*\*([0-9\.]+)/', '($1)**($2)', $expression);
        
        $expression = preg_replace('/([0-9\.]+)\*([0-9\.]+)/', '($1)*($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\/([0-9\.]+)/', '($1)/($2)', $expression);
        
        $expression = preg_replace('/([0-9\.]+)\+([0-9\.]+)/', '($1)+($2)', $expression);
        $expression = preg_replace('/([0-9\.]+)\-([0-9\.]+)/', '($1)-($2)', $expression);
        
        return $expression;
    }

    protected function evaluateExpression($expression)
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