<?php

namespace App\Jobs;

use App\Services\BudgetAnalyticsCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

final class RecalculateBudgetAnalytics implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 30; // debounce: 30 seconds

    public function __construct(
        public int $governmentUnitId,
        public int $fiscalYearId
    ) {}

    public function uniqueId(): string
    {
        return "analytics:{$this->governmentUnitId}:{$this->fiscalYearId}";
    }

    public function handle(BudgetAnalyticsCalculator $calculator): void
    {
        // Recalculate the analytics
        $calculator->recalculate(
            $this->governmentUnitId,
            $this->fiscalYearId
        );

        // Clear all related caches
        Cache::forget("analytics:overall-summary:{$this->fiscalYearId}");
        Cache::forget("analytics:barangay-list:{$this->fiscalYearId}");
        Cache::forget("analytics:barangay:{$this->governmentUnitId}");

        // Also clear cache for all budgets under this government unit
        $budgetIds = DB::table('budgets')
            ->where('government_unit_id', $this->governmentUnitId)
            ->where('fiscal_year_id', $this->fiscalYearId)
            ->pluck('id');

        foreach ($budgetIds as $budgetId) {
            Cache::forget("analytics:barangay:{$budgetId}");
        }
    }
}
