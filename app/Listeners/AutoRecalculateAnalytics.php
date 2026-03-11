<?php
// filepath: /home/nicko/gov-budget-api/app/Listeners/AutoRecalculateAnalytics.php

namespace App\Listeners;

use App\Events\BudgetItemModified;
use App\Events\BudgetModified;
use App\Events\ExpenseModified;
use App\Events\FiscalYearModified;
use App\Jobs\RecalculateBudgetAnalytics;
use App\Models\GovernmentUnit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

final class AutoRecalculateAnalytics implements ShouldQueue
{
    public function handle(object $event): void
    {
        /**
         * Fiscal year activation, recalc everything
         */
        if ($event instanceof FiscalYearModified) {
            $fiscalYear = $event->model;

            if (! $fiscalYear->wasChanged('is_active') || ! $fiscalYear->is_active) {
                return;
            }

            GovernmentUnit::pluck('id')->each(function ($unitId) use ($fiscalYear) {
                RecalculateBudgetAnalytics::dispatch(
                    $unitId,
                    $fiscalYear->id
                );
            });

            Cache::forget("analytics:overall-summary:{$fiscalYear->id}");
            Cache::forget("analytics:barangay-list:{$fiscalYear->id}");

            return;
        }

        /**
         * Only react to CREATE / UPDATE
         * Deletes are handled explicitly in controllers
         */
        if (! in_array($event->action, ['created', 'updated', 'deleted'], true)) {
            return;
        }

        $model = $event->model;

        // Extract government_unit_id and fiscal_year_id based on event type
        [$governmentUnitId, $fiscalYearId] = $this->resolveIds($event);

        if ($governmentUnitId === null || $fiscalYearId === null) {
            return;
        }

        RecalculateBudgetAnalytics::dispatch($governmentUnitId, $fiscalYearId);
    }

    /**
     * Resolve government_unit_id and fiscal_year_id from the event model.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function resolveIds(object $event): array
    {
        $model = $event->model;

        // Budget has direct fields
        if ($event instanceof BudgetModified) {
            return [
                $model->government_unit_id,
                $model->fiscal_year_id,
            ];
        }

        // BudgetItem needs to go through Budget relationship
        if ($event instanceof BudgetItemModified) {
            $budget = $model->budget;

            if (! $budget) {
                return [null, null];
            }

            return [
                $budget->government_unit_id,
                $budget->fiscal_year_id,
            ];
        }

        // Expense needs to go through BudgetItem → Budget relationship
        if ($event instanceof ExpenseModified) {
            $budgetItem = $model->budgetItem;
            $budget = $budgetItem?->budget;

            if (! $budget) {
                return [null, null];
            }

            return [
                $budget->government_unit_id,
                $budget->fiscal_year_id,
            ];
        }

        return [null, null];
    }
}
