<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ProcessesQuestionData
{
    private $constants = [
        'g' => 9.8,
        'pi' => M_PI,
    ];

    protected function generateRandomValues($content, $rumus, $randomRangesConfig)
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
        if ($randomRangesConfig) {
            foreach ($randomRangesConfig as $range) {
                 if (isset($range['variabel']) && isset($range['min_value']) && isset($range['max_value']) && isset($range['type'])) {
                    $randomRanges['%' . $range['variabel'] . '%'] = [
                        'min_value' => floatval($range['min_value']),
                        'max_value' => floatval($range['max_value']),
                        'type' => $range['type'],
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

    protected function replacePlaceholders($content, $values)
    {
        foreach ($values as $placeholder => $value) {
            $content = str_replace($placeholder, (string)$value, $content);
        }
        return $content;
    }

    protected function processRumus($rumus, $randomValues, $precision = 3)
    {
        if (empty($rumus) || !is_array($rumus)) {
            return [];
        }

        $rumusValues = [];

        foreach ($rumus as $index => $item) {
            if (is_array($item) && isset($item['variabel_rumus'])) {
                $expression = $item['variabel_rumus'];

                foreach ($randomValues as $placeholder => $value) {
                    $expression = str_replace($placeholder, (string)$value, $expression);
                }

                foreach ($rumusValues as $placeholder => $value) {
                    $expression = str_replace($placeholder, (string)$value, $expression);
                }

                foreach ($this->constants as $constant => $value) {
                    $expression = str_replace($constant, (string)$value, $expression);
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
                        $result = sqrt(abs($argValue));
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
        return $rumusValues;
    }

    protected function evaluateExpression($expression)
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
                    $result = sqrt(abs($argValue));
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

            $result = eval('return ' . $expression . ';');

            if (!is_numeric($result)) {
                throw new \Exception('Expression did not evaluate to a number');
            }

            return round($result, 6);
        } catch (\Exception $e) {
            Log::error('Error evaluating expression', [
                'expression' => $expression,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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