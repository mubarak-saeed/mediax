<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = [
            [
                'email' => 'superadmin@mediax.com',
                'name' => 'مدير النظام',
                'password' => config('app.admin_password'),
                'role' => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'admin@mediax.com',
                'name' => 'مدير المنصة',
                'password' => config('app.admin_password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
        ];
        foreach ($users as $user) {
            User::query()->firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->call([
            SectionSeeder::class,
            GenreSeeder::class,
            PlanSeeder::class,
            SubSectionSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
