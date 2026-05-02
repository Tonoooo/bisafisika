<?php

namespace App\Exports;

use App\Models\UserQuiz;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class QuizLeaderboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $quizId;
    protected $authUser;

    public function __construct($quizId, $authUser)
    {
        $this->quizId = $quizId;
        $this->authUser = $authUser;
    }

    public function collection()
    {
        return UserQuiz::query()
            ->where('quiz_id', $this->quizId)
            ->where('is_completed', true)
            ->when($this->authUser->roles->contains('name', 'guru') || $this->authUser->roles->contains('name', 'dosen'), function (Builder $query) {
                $query->whereHas('user', function ($q) {
                    $q->where('school_id', $this->authUser->school_id);
                });
            })
            ->when($this->authUser->roles->contains('name', 'siswa') || $this->authUser->roles->contains('name', 'mahasiswa'), function (Builder $query) {
                $query->whereHas('user', function ($q) {
                    $q->where('school_id', $this->authUser->school_id)
                      ->where('level', $this->authUser->level)
                      ->where('class', $this->authUser->class);
                });
            })
            ->with(['user.school'])
            ->orderBy('score', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Sekolah',
            'Tingkat',
            'Kelas',
            'Nilai',
            'Total Pelanggaran',
            'Selesai Pada',
        ];
    }

    public function map($row): array
    {
        return [
            $row->user->name,
            $row->user->school?->name ?? '-',
            $row->user->level ?? '-',
            $row->user->class ?? '-',
            number_format($row->score, 2),
            $row->total_violations,
            $row->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
