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
                    
                } else {
                    $randomValues[$placeholder] = rand((int)$min, (int)$max);
                   
                }
            } else {
                $randomValues[$placeholder] = rand(1, 100);
                
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
            return [];
        }

        $rumusValues = [];
        $precision = $question->precision ?? 6; 

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

                    $expression = $this->formatExpression($expression);

                    $result = $this->evaluateExpression($expression);
                    

                    if ($result === 'Error') {
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