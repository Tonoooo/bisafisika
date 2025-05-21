<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentScore;

class StudentScoreSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'siswa');
        })->get();

        foreach ($users as $user) {
            StudentScore::updateScore($user->id);
        }
    }
} 