<?php

namespace App\Services;

use App\Models\FiscalYear;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class AnalyticsService
{
    public function overallSummary(): ?object
    {
        $fiscalYearId = FiscalYear::where('is_active', true)->value('id');

        if (! $fiscalYearId) {
            return null;
        }

        $this->ensureSummariesExist($fiscalYearId);

        return Cache::remember(
            "analytics:overall-summary:{$fiscalYearId}",
            now()->addMinutes(15),
            function () use ($fiscalYearId) {

                $summaries = DB::table('budget_summaries')
                    ->where('fiscal_year_id', $fiscalYearId)
                    ->get([
                        'total_allocated',
                        'total_spent',
                        'spending_by_category',
                    ]);

                if ($summaries->isEmpty()) {
                    return null;
                }

                $totalAllocated = $summaries->sum('total_allocated');
                $totalSpent = $summaries->sum('total_spent');

                $categoryTotals = [];

                foreach ($summaries as $summary) {

                    $decoded = json_decode($summary->spending_by_category, true);

                    if (! is_array($decoded)) {
                        continue;
                    }

                    foreach ($decoded as $key => $value) {

                        if (is_string($key)) {
                            $categoryTotals[$key] =
                                ($categoryTotals[$key] ?? 0) + (float) $value;
                            continue;
                        }

                        if (is_array($value) && isset($value['name'], $value['total_spent'])) {
                            $categoryTotals[$value['name']] =
                                ($categoryTotals[$value['name']] ?? 0) + (float) $value['total_spent'];
                        }
                    }
                }

                return (object) [
                    'total_allocated' => (float) $totalAllocated,
                    'total_spent' => (float) $totalSpent,
                    'utilization_rate' => $totalAllocated > 0
                        ? round(($totalSpent / $totalAllocated) * 100, 2)
                        : 0,
                    'spending_by_category' => $categoryTotals,
                ];
            }
        );
    }


    public function barangayList(): array
    {
        $fiscalYear = FiscalYear::where('is_active', true)->first();

        if (! $fiscalYear) {
            return [];
        }

        $this->ensureSummariesExist($fiscalYear->id);

        return Cache::remember(
            "analytics:barangay-list:{$fiscalYear->id}",
            now()->addMinutes(10),
            function () use ($fiscalYear) {

                return DB::table('budgets')
                    ->join('government_units', 'budgets.government_unit_id', '=', 'government_units.id')
                    ->leftJoin('budget_summaries', function ($join) use ($fiscalYear) {
                        $join->on('budgets.government_unit_id', '=', 'budget_summaries.government_unit_id')
                            ->where('budget_summaries.fiscal_year_id', $fiscalYear->id);
                    })
                    ->where('budgets.fiscal_year_id', $fiscalYear->id)
                    ->get([
                        'budgets.id as budget_id',
                        'budgets.name as budget_name',
                        'government_units.name as barangay_name',
                        'budget_summaries.total_allocated',
                        'budget_summaries.total_spent',
                        'budget_summaries.utilization_rate',
                    ])
                    ->map(function ($row) {

                        $rate = (float) ($row->utilization_rate ?? 0);

                        return [
                            'budget_id' => $row->budget_id,
                            'barangay_name' => $row->barangay_name,
                            'budget_name' => $row->budget_name,
                            'total_allocated' => (float) ($row->total_allocated ?? 0),
                            'total_spent' => (float) ($row->total_spent ?? 0),
                            'utilization_rate' => $rate,
                            'status' => $this->determineStatus($rate),
                        ];
                    })
                    ->all();
            }
        );
    }


    public function barangayAnalytics(int $budgetId): ?array
    {
        $governmentUnitId = DB::table('budgets')
            ->where('id', $budgetId)
            ->value('government_unit_id');

        if (! $governmentUnitId) {
            return null;
        }

        return Cache::remember(
            "analytics:barangay:{$budgetId}",
            now()->addMinutes(10),
            function () use ($budgetId) {

                $barangay = DB::table('budgets')
                    ->join('government_units', 'budgets.government_unit_id', '=', 'government_units.id')
                    ->leftJoin('budget_summaries', function ($join) {
                        $join->on('budgets.government_unit_id', '=', 'budget_summaries.government_unit_id')
                            ->on('budgets.fiscal_year_id', '=', 'budget_summaries.fiscal_year_id');
                    })
                    ->where('budgets.id', $budgetId)
                    ->first([
                        'government_units.name as barangay_name',
                        'budget_summaries.total_allocated',
                        'budget_summaries.total_spent',
                        'budget_summaries.utilization_rate',
                    ]);

                if (! $barangay) {
                    return null;
                }

                $projects = DB::table('budget_items')
                    ->leftJoin('expenses', 'budget_items.id', '=', 'expenses.budget_item_id')
                    ->where('budget_items.budget_id', $budgetId)
                    ->groupBy('budget_items.id', 'budget_items.name', 'budget_items.allocated_amount')
                    ->get([
                        'budget_items.id',
                        'budget_items.name',
                        'budget_items.allocated_amount',
                        DB::raw('COALESCE(SUM(expenses.amount),0) as spent_amount'),
                    ])
                    ->map(function ($project) {

                        $rate = $project->allocated_amount > 0
                            ? ($project->spent_amount / $project->allocated_amount) * 100
                            : 0;

                        return [
                            'project_id' => $project->id,
                            'project_name' => $project->name,
                            'allocated_amount' => (float) $project->allocated_amount,
                            'spent_amount' => (float) $project->spent_amount,
                            'utilization_rate' => round($rate, 2),
                            'status' => $this->determineStatus($rate),
                        ];
                    })
                    ->all();

                return [
                    'barangay_name' => $barangay->barangay_name,
                    'total_allocated' => (float) ($barangay->total_allocated ?? 0),
                    'total_spent' => (float) ($barangay->total_spent ?? 0),
                    'utilization_rate' => (float) ($barangay->utilization_rate ?? 0),
                    'projects' => $projects,
                ];
            }
        );
    }


    private function determineStatus(float $rate): string
    {
        if ($rate > 100) {
            return 'Over Budget';
        }

        if ($rate >= 100) {
            return 'At Limit';
        }

        if ($rate >= 80) {
            return 'Near Limit';
        }

        return 'Under Budget';
    }


    private function ensureSummariesExist(int $fiscalYearId): void
    {
        $exists = DB::table('budget_summaries')
            ->where('fiscal_year_id', $fiscalYearId)
            ->exists();

        if ($exists) {
            return;
        }

        $units = DB::table('government_units')->pluck('id');

        foreach ($units as $unitId) {
            app(\App\Services\BudgetAnalyticsCalculator::class)
                ->recalculate($unitId, $fiscalYearId);
        }
    }
}
