<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin
        $superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // Create content admin (editor)
        $contentAdmin = Admin::create([
            'name' => 'Content Admin',
            'email' => 'content@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'editor',
            'status' => 'active',
        ]);

        // Create user admin (editor)
        $userAdmin = Admin::create([
            'name' => 'User Admin',
            'email' => 'useradmin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'editor',
            'status' => 'active',
        ]);

        // Create financial admin (accountant)
        $financialAdmin = Admin::create([
            'name' => 'Financial Admin',
            'email' => 'finance@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'accountant',
            'status' => 'active',
        ]);

        // Create support admin (editor)
        $supportAdmin = Admin::create([
            'name' => 'Support Admin',
            'email' => 'support@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'editor',
            'status' => 'active',
        ]);

        // Create moderator admin (editor)
        $moderatorAdmin = Admin::create([
            'name' => 'Moderator',
            'email' => 'moderator@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'editor',
            'status' => 'active',
        ]);

        // Create inactive admin
        $inactiveAdmin = Admin::create([
            'name' => 'Inactive Admin',
            'email' => 'inactive@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'editor',
            'status' => 'inactive',
        ]);

        // Create blocked admin (editor)
        $blockedAdmin = Admin::create([
            'name' => 'Blocked Admin',
            'email' => 'blocked@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'editor',
            'status' => 'inactive',
        ]);

        $this->command->info('Admins seeded successfully!');
        $this->command->info('Created 8 admin users with various roles and statuses.');
        $this->command->info('Super Admin: superadmin@example.com / admin123');
        $this->command->info('Content Admin (Editor): content@example.com / admin123');
        $this->command->info('User Admin (Editor): useradmin@example.com / admin123');
        $this->command->info('Financial Admin (Accountant): finance@example.com / admin123');
        $this->command->info('Support Admin (Editor): support@example.com / admin123');
        $this->command->info('Moderator (Editor): moderator@example.com / admin123');
    }
}
