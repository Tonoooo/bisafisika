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
            ['id' => 26, 'name' => 'view_any_quiz', 'guard_name' => 'web'],
            ['id' => 27, 'name' => 'create_quiz', 'guard_name' => 'web'],
            ['id' => 28, 'name' => 'update_quiz', 'guard_name' => 'web'],
            ['id' => 29, 'name' => 'delete_quiz', 'guard_name' => 'web'],
            ['id' => 30, 'name' => 'delete_any_quiz', 'guard_name' => 'web'],
            ['id' => 31, 'name' => 'force_delete_quiz', 'guard_name' => 'web'],
            ['id' => 32, 'name' => 'force_delete_any_quiz', 'guard_name' => 'web'],
            ['id' => 33, 'name' => 'restore_quiz', 'guard_name' => 'web'],
            ['id' => 34, 'name' => 'restore_any_quiz', 'guard_name' => 'web'],
            ['id' => 35, 'name' => 'replicate_quiz', 'guard_name' => 'web'],
            ['id' => 36, 'name' => 'reorder_quiz', 'guard_name' => 'web'],
            ['id' => 37, 'name' => 'view_user', 'guard_name' => 'web'],
            ['id' => 38, 'name' => 'view_any_user', 'guard_name' => 'web'],
            ['id' => 39, 'name' => 'create_user', 'guard_name' => 'web'],
            ['id' => 40, 'name' => 'update_user', 'guard_name' => 'web'],
            ['id' => 41, 'name' => 'delete_user', 'guard_name' => 'web'],
            ['id' => 42, 'name' => 'delete_any_user', 'guard_name' => 'web'],
            ['id' => 43, 'name' => 'force_delete_user', 'guard_name' => 'web'],
            ['id' => 44, 'name' => 'force_delete_any_user', 'guard_name' => 'web'],
            ['id' => 45, 'name' => 'restore_user', 'guard_name' => 'web'],
            ['id' => 46, 'name' => 'restore_any_user', 'guard_name' => 'web'],
            ['id' => 47, 'name' => 'replicate_user', 'guard_name' => 'web'],
            ['id' => 48, 'name' => 'reorder_user', 'guard_name' => 'web'],
            ['id' => 49, 'name' => 'view_role', 'guard_name' => 'web'],
            ['id' => 50, 'name' => 'view_any_role', 'guard_name' => 'web'],
            ['id' => 51, 'name' => 'create_role', 'guard_name' => 'web'],
            ['id' => 52, 'name' => 'update_role', 'guard_name' => 'web'],
            ['id' => 53, 'name' => 'delete_role', 'guard_name' => 'web'],
            ['id' => 54, 'name' => 'delete_any_role', 'guard_name' => 'web'],
            ['id' => 55, 'name' => 'force_delete_role', 'guard_name' => 'web'],
            ['id' => 56, 'name' => 'force_delete_any_role', 'guard_name' => 'web'],
            ['id' => 57, 'name' => 'restore_role', 'guard_name' => 'web'],
            ['id' => 58, 'name' => 'restore_any_role', 'guard_name' => 'web'],
            ['id' => 59, 'name' => 'replicate_role', 'guard_name' => 'web'],
            ['id' => 60, 'name' => 'reorder_role', 'guard_name' => 'web'],
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
        for ($i = 1; $i <= 60; $i++) {
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
