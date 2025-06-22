<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'image_path', 'answers', 'rumus', 'random_ranges'];

    protected $casts = [
        'answers' => 'array',
        'rumus' => 'array',
        'image_path' => 'string',
    ];

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question');
    }

    public function getRandomRangesAttribute($value)
    {
        return $value ? $value : '';
    }

    public function getRandomVariablesAttribute()
    {

        $ranges = $this->random_ranges ? explode(';', $this->random_ranges) : [];
        $variables = [];


        foreach ($ranges as $index => $range) {
            if (empty($range)) {
                continue;
            }

            $parts = explode('|', $range);

            if (count($parts) === 4) {
                $variabel = trim(str_replace(['%', '%'], '', $parts[0]));
                $variables[] = [
                    'variabel' => $variabel,
                    'min_value' => floatval($parts[1]),
                    'max_value' => floatval($parts[2]),
                    'type' => strtolower(trim($parts[3])),
                ];
            } else {
                //
            }
        }



        return $variables;
    }

    public function setRumusAttribute($value)
    {

        if (empty($value)) {
            $this->attributes['rumus'] = null;
            return;
        }

        $formatted = [];
        foreach ($value as $index => $item) {
            $variabelRumus = $item['variabel_rumus'] ?? null;
            if ($variabelRumus) {
                $formatted[] = [
                    'variabelrumus' . ($index + 1) => $variabelRumus,
                ];
            }
        }

        $this->attributes['rumus'] = json_encode($formatted);

    }

    public function getRumusAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (empty($decoded)) {
            return [];
        }

        $formatted = [];
        if (isset($decoded[0])) {
            foreach ($decoded as $index => $item) {
                $key = array_key_first($item);
                $formatted[] = [
                    'variabel_rumus' => $item[$key],
                ];
            }
        } else {
            $key = array_key_first($decoded);
            $formatted[] = [
                'variabel_rumus' => $decoded[$key],
            ];
        }

        return $formatted;
    }
}
