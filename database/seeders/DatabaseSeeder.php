<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        \App\Models\User::create([
            'name' => 'Yunita',
            'email' => 'yunita@gmail.com',
            'password' => '12345678', 
            'status' => 'verified',
        ]);

        // Seeder permissions (dari screenshot)
        $permissions = [
            ['id' => 1, 'name' => 'view_leaderboard', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'view_any_leaderboard', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'create_leaderboard', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'update_leaderboard', 'guard_name' => 'web'],
            ['id' => 5, 'name' => 'restore_leaderboard', 'guard_name' => 'web'],
            ['id' => 6, 'name' => 'restore_any_leaderboard', 'guard_name' => 'web'],
            ['id' => 7, 'name' => 'replicate_leaderboard', 'guard_name' => 'web'],
            ['id' => 8, 'name' => 'reorder_leaderboard', 'guard_name' => 'web'],
            ['id' => 9, 'name' => 'delete_leaderboard', 'guard_name' => 'web'],
            ['id' => 10, 'name' => 'delete_any_leaderboard', 'guard_name' => 'web'],
            ['id' => 11, 'name' => 'force_delete_leaderboard', 'guard_name' => 'web'],
            ['id' => 12, 'name' => 'force_delete_any_leaderboard', 'guard_name' => 'web'],
            ['id' => 13, 'name' => 'view_question', 'guard_name' => 'web'],
            ['id' => 14, 'name' => 'view_any_question', 'guard_name' => 'web'],
            ['id' => 15, 'name' => 'create_question', 'guard_name' => 'web'],
            ['id' => 16, 'name' => 'update_question', 'guard_name' => 'web'],
            ['id' => 17, 'name' => 'restore_question', 'guard_name' => 'web'],
            ['id' => 18, 'name' => 'restore_any_question', 'guard_name' => 'web'],
            ['id' => 19, 'name' => 'replicate_question', 'guard_name' => 'web'],
            ['id' => 20, 'name' => 'reorder_question', 'guard_name' => 'web'],
            ['id' => 21, 'name' => 'delete_question', 'guard_name' => 'web'],
            ['id' => 22, 'name' => 'delete_any_question', 'guard_name' => 'web'],
            ['id' => 23, 'name' => 'force_delete_question', 'guard_name' => 'web'],
            ['id' => 24, 'name' => 'force_delete_any_question', 'guard_name' => 'web'],
            ['id' => 25, 'name' => 'view_quiz', 'guard_name' => 'web'],
        ];
        foreach ($permissions as $perm) {
            \DB::table('permissions')->updateOrInsert(['id' => $perm['id']], [
                'name' => $perm['name'],
                'guard_name' => $perm['guard_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeder role_has_permissions (semua permission_id 1-25 untuk role_id 1)
        for ($i = 1; $i <= 25; $i++) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $i,
                'role_id' => 1,
            ], []);
        }

        // Seeder model_has_roles
        \DB::table('model_has_roles')->updateOrInsert([
            'role_id' => 1,
            'model_type' => 'App\\Models\\User',
            'model_id' => 1,
        ], []);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
