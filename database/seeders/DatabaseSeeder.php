<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update super admin
        User::updateOrCreate(
            ['email' => 'admin@barangay.gov.ph'],
            [
                'name' => 'Nicko Rodavia',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin created: admin@barangay.gov.ph / password');

        $categories = [
            'Personnel Services',
            'Maintenance and Other Operating Expenses',
            'Capital Outlay',
            'Financial Expenses',
            'Special Purpose Funds',
            'Development Projects',
        ];

        foreach ($categories as $category) {
            BudgetCategory::updateOrCreate([
                'name' => $category
            ]);
        }

        $this->command->info('Budget categories created: ' . count($categories) . ' categories');
    }
}
