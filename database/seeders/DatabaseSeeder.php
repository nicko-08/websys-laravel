<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\BudgetItem;
use App\Models\Expense;
use App\Models\FiscalYear;
use App\Models\GovernmentUnit;
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
        $this->command->info('🌱 Starting database seeding...');

        // ========================================
        // 1. CREATE USERS
        // ========================================
        $this->command->info('👤 Creating users...');

        $admin = User::updateOrCreate(
            ['email' => 'admin@barangay.gov.ph'],
            [
                'name' => 'Nicko Rodavia',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'budget.officer@barangay.gov.ph'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'budget-officer',
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'auditor@barangay.gov.ph'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'auditor',
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@barangay.gov.ph'],
            [
                'name' => 'Pedro Reyes',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Users created (4 users)');

        // ========================================
        // 2. CREATE BUDGET CATEGORIES
        // ========================================
        $this->command->info('📂 Creating budget categories...');

        $categoryNames = [
            'Personnel Services',
            'Maintenance and Other Operating Expenses',
            'Capital Outlay',
            'Financial Expenses',
            'Special Purpose Funds',
            'Development Projects',
        ];

        $categories = [];
        foreach ($categoryNames as $categoryName) {
            $categories[] = BudgetCategory::updateOrCreate(['name' => $categoryName]);
        }

        $this->command->info('✅ Budget categories created (' . count($categories) . ' categories)');

        // ========================================
        // 3. CREATE GOVERNMENT UNITS
        // ========================================
        $this->command->info('🏛️  Creating government units...');

        $units = [
            ['name' => 'Manila City', 'type' => 'city'],
            ['name' => 'Las Piñas City', 'type' => 'city'],
            ['name' => 'Marikina City', 'type' => 'city'],
            ['name' => 'Quezon City', 'type' => 'city'],
        ];

        $governmentUnits = [];
        foreach ($units as $unitData) {
            $governmentUnits[] = GovernmentUnit::updateOrCreate(
                ['name' => $unitData['name']],
                ['type' => $unitData['type']]
            );
        }

        $this->command->info('✅ Government units created (' . count($governmentUnits) . ' units)');

        // ========================================
        // 4. CREATE FISCAL YEARS
        // ========================================
        $this->command->info('📅 Creating fiscal years...');

        $fiscalYear2025 = FiscalYear::updateOrCreate(
            ['year' => 2025],
            [
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'is_active' => false,
            ]
        );

        $fiscalYear2026 = FiscalYear::updateOrCreate(
            ['year' => 2026],
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Fiscal years created (2026 is active)');

        // ========================================
        // 5. CREATE BUDGETS & BUDGET ITEMS
        // ========================================
        $this->command->info('💰 Creating budgets and budget items...');

        $budgetData = [
            [
                'unit' => 0, // Manila City
                'name' => 'Health',
                'total' => 1000000,
                'items' => [
                    ['name' => 'Medical Supplies', 'category' => 1, 'allocated' => 400000, 'expenses' => [
                        ['desc' => 'Syringes and needles purchase', 'amount' => 150000, 'date' => '2026-01-15'],
                        ['desc' => 'Surgical gloves bulk order', 'amount' => 250000, 'date' => '2026-02-10'],
                    ]],
                    ['name' => 'Hospital Equipment', 'category' => 2, 'allocated' => 600000, 'expenses' => [
                        ['desc' => 'X-ray machine repair', 'amount' => 200000, 'date' => '2026-01-20'],
                        ['desc' => 'ECG monitor purchase', 'amount' => 400000, 'date' => '2026-02-28'],
                    ]],
                ],
            ],
            [
                'unit' => 1, // Las Piñas City
                'name' => 'Infrastructure',
                'total' => 1000000,
                'items' => [
                    ['name' => 'Road Maintenance', 'category' => 2, 'allocated' => 500000, 'expenses' => [
                        ['desc' => 'Asphalt for highway repairs', 'amount' => 200000, 'date' => '2026-01-25'],
                        ['desc' => 'Road painting materials', 'amount' => 100000, 'date' => '2026-02-15'],
                    ]],
                    ['name' => 'Bridge Construction', 'category' => 2, 'allocated' => 500000, 'expenses' => [
                        ['desc' => 'Concrete mixer rental', 'amount' => 100000, 'date' => '2026-02-05'],
                    ]],
                ],
            ],
            [
                'unit' => 2, // Marikina City
                'name' => 'Services',
                'total' => 5100000,
                'items' => [
                    ['name' => 'Waste Management', 'category' => 1, 'allocated' => 2000000, 'expenses' => [
                        ['desc' => 'Garbage trucks fuel', 'amount' => 50000, 'date' => '2026-01-10'],
                        ['desc' => 'Waste collection salaries', 'amount' => 50000, 'date' => '2026-02-01'],
                    ]],
                    ['name' => 'Public Safety', 'category' => 0, 'allocated' => 3100000, 'expenses' => []],
                ],
            ],
            [
                'unit' => 3, // Quezon City
                'name' => 'Education',
                'total' => 100000,
                'items' => [
                    ['name' => 'School Supplies', 'category' => 1, 'allocated' => 50000, 'expenses' => [
                        ['desc' => 'Textbooks for elementary', 'amount' => 30000, 'date' => '2026-01-05'],
                        ['desc' => 'Notebooks and pens', 'amount' => 15000, 'date' => '2026-01-12'],
                    ]],
                    ['name' => 'Teacher Training', 'category' => 0, 'allocated' => 50000, 'expenses' => [
                        ['desc' => 'Workshop facilitator fee', 'amount' => 25000, 'date' => '2026-02-20'],
                    ]],
                ],
            ],
        ];

        foreach ($budgetData as $budgetInfo) {
            $budget = Budget::create([
                'name' => $budgetInfo['name'],
                'government_unit_id' => $governmentUnits[$budgetInfo['unit']]->id,
                'fiscal_year_id' => $fiscalYear2026->id,
                'total_amount' => $budgetInfo['total'],
            ]);

            foreach ($budgetInfo['items'] as $itemInfo) {
                $budgetItem = BudgetItem::create([
                    'name' => $itemInfo['name'],
                    'code' => strtoupper(substr($itemInfo['name'], 0, 3)) . '-' . rand(1000, 9999),
                    'budget_id' => $budget->id,
                    'budget_category_id' => $categories[$itemInfo['category']]->id,
                    'allocated_amount' => $itemInfo['allocated'],
                ]);

                foreach ($itemInfo['expenses'] as $expenseInfo) {
                    Expense::create([
                        'description' => $expenseInfo['desc'],
                        'budget_item_id' => $budgetItem->id,
                        'amount' => $expenseInfo['amount'],
                        'transaction_date' => $expenseInfo['date'],
                    ]);
                }
            }
        }

        $this->command->info('✅ Budgets and budget items created with expenses');

        // ========================================
        // 6. TRIGGER ANALYTICS CALCULATION
        // ========================================
        $this->command->info('📊 Calculating analytics...');

        $calculator = app(\App\Services\BudgetAnalyticsCalculator::class);
        foreach ($governmentUnits as $unit) {
            $calculator->recalculate($unit->id, $fiscalYear2026->id);
        }

        $this->command->info('✅ Analytics calculated for all government units');

        // ========================================
        // SUMMARY
        // ========================================
        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Resource', 'Count'],
            [
                ['Users', User::count()],
                ['Budget Categories', BudgetCategory::count()],
                ['Government Units', GovernmentUnit::count()],
                ['Fiscal Years', FiscalYear::count()],
                ['Budgets', Budget::count()],
                ['Budget Items', BudgetItem::count()],
                ['Expenses', Expense::count()],
            ]
        );
        $this->command->newLine();
        $this->command->info('📧 Login credentials:');
        $this->command->info('   Admin:          admin@barangay.gov.ph / password');
        $this->command->info('   Budget Officer: budget.officer@barangay.gov.ph / password');
        $this->command->info('   Auditor:        auditor@barangay.gov.ph / password');
        $this->command->info('   User:           user@barangay.gov.ph / password');
    }
}
