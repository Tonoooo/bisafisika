<?php

namespace App\Exports;

use App\Models\StudentScore;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeaderboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Sekolah',
            'Tingkat',
            'Kelas',
            'Total Nilai',
            'Total Quiz',
            'Rata-rata Nilai'
        ];
    }

    public function map($row): array
    {
        return [
            $row->user->name,
            $row->user->school->name,
            $row->user->level,
            $row->user->class,
            number_format($row->total_score, 2),
            $row->total_quizzes,
            number_format($row->average_score, 2)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 