<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('Form Data Before Create', ['data' => $data]);

        // Proses random_variables menjadi teks sederhana
        if (isset($data['random_variables']) && is_array($data['random_variables']) && !empty(array_filter($data['random_variables']))) {
            $ranges = [];
            foreach ($data['random_variables'] as $item) {
                $variabel = $item['variabel'] ?? null;
                if ($variabel && isset($item['type']) && isset($item['min_value']) && isset($item['max_value'])) {
                    $ranges[] = implode('|', [
                        '%' . $variabel . '%',
                        floatval($item['min_value']),
                        floatval($item['max_value']),
                        $item['type'] ?? 'integer',
                    ]);
                } else {
                    Log::warning('Invalid random variable item', ['item' => $item]);
                }
            }
            $data['random_ranges'] = implode(';', $ranges);
            Log::info('Random Ranges Before Save', ['random_ranges' => $data['random_ranges']]);
        } else {
            $data['random_ranges'] = null;
            Log::info('No Valid Random Variables Provided');
            throw new \Exception('Random variables are required.');
        }

        // Hapus random_variables dari data agar tidak disimpan sebagai kolom
        unset($data['random_variables']);

        return $data;
    }

    protected function beforeSave(): void
    {
        $data = $this->form->getState();
        Log::info('Form State Before Save', ['state' => $data]);
        if (!isset($data['random_variables']) || empty(array_filter($data['random_variables']))) {
            throw new \Exception('Random variables cannot be empty.');
        }
    }
}