<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ExpenseModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\RecalculateBudgetAnalytics;
use Illuminate\Support\Facades\Cache;

/**
 * @group Expenses
 * Expense management endpoints
 */
class ExpenseController extends Controller
{
    /**
     * Clear all analytics caches for a given government unit
     */
    private function clearAnalyticsCaches(int $governmentUnitId, int $fiscalYearId): void
    {
        Cache::forget("analytics:overall-summary:{$fiscalYearId}");
        Cache::forget("analytics:barangay-list:{$fiscalYearId}");
        Cache::forget("analytics:barangay:{$governmentUnitId}");
    }

    /**
     * List expenses
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $expenses = Expense::query()
            ->with(['budgetItem.budget.governmentUnit', 'budgetItem.budget.fiscalYear', 'budgetItem.category'])

            // Filter by budget_id (through budgetItem relationship)
            ->when(
                $request->filled('budget_id') && $request->integer('budget_id'),
                fn($query) => $query->whereHas('budgetItem', function ($q) use ($request) {
                    $q->where('budget_id', $request->budget_id);
                })
            )

            // Filter by budget_item_id (direct relationship)
            ->when(
                $request->filled('budget_item_id') && $request->integer('budget_item_id'),
                fn($query) => $query->where('budget_item_id', $request->budget_item_id)
            )

            // Filter by date range - from_date
            ->when(
                $request->filled('from_date'),
                fn($query) => $query->where('transaction_date', '>=', $request->from_date)
            )

            // Filter by date range - to_date
            ->when(
                $request->filled('to_date'),
                fn($query) => $query->where('transaction_date', '<=', $request->to_date)
            )

            ->latest('transaction_date')
            ->paginate(20);

        return ExpenseResource::collection($expenses);
    }

    /**
     * Create expense
     *
     * @authenticated
     *
     * @bodyParam description string required Expense description. Example: Office supplies purchase
     * @bodyParam amount number required Expense amount. Example: 1500.00
     * @bodyParam budget_item_id integer required Budget item ID. Example: 1
     * @bodyParam transaction_date date required Transaction date. Example: 2024-01-15
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $this->authorize('create', Expense::class);

        $expense = DB::transaction(function () use ($request) {
            $expense = Expense::create($request->validated());

            // Load relationships to get government_unit_id
            $expense->load('budgetItem.budget');

            $governmentUnitId = $expense->budgetItem->budget->government_unit_id;
            $fiscalYearId = $expense->budgetItem->budget->fiscal_year_id;

            // Clear cache immediately for instant analytics update
            $this->clearAnalyticsCaches($governmentUnitId, $fiscalYearId);

            // Clear budget-specific cache too
            Cache::forget("budget:{$expense->budgetItem->budget_id}");

            // Dispatch job for background recalculation
            RecalculateBudgetAnalytics::dispatch($governmentUnitId, $fiscalYearId);

            // In development/local, also run synchronously for immediate update
            if (config('app.env') === 'local') {
                app(\App\Services\BudgetAnalyticsCalculator::class)
                    ->recalculate($governmentUnitId, $fiscalYearId);
            }

            event(new ExpenseModified($expense, $request->user(), 'created'));

            return $expense;
        });

        return (new ExpenseResource($expense->load('budgetItem')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Show expense
     */
    public function show(Expense $expense): ExpenseResource
    {
        // Load relationships and aggregate the sum of expenses for the budget item
        $expense->load([
            'budgetItem.budget.governmentUnit',
            'budgetItem.budget.fiscalYear',
            'budgetItem.category'
        ]);

        // Load the sum of expenses for this budget item
        $expense->budgetItem->loadSum('expenses', 'amount');

        return new ExpenseResource($expense);
    }

    /**
     * Update expense
     *
     * @authenticated
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): ExpenseResource
    {
        $this->authorize('update', $expense);

        DB::transaction(function () use ($request, $expense) {
            $governmentUnitId = $expense->budgetItem->budget->government_unit_id;
            $fiscalYearId = $expense->budgetItem->budget->fiscal_year_id;

            $expense->update($request->validated());

            // Clear cache immediately for instant analytics update
            $this->clearAnalyticsCaches($governmentUnitId, $fiscalYearId);

            // Dispatch job for background recalculation
            RecalculateBudgetAnalytics::dispatch($governmentUnitId, $fiscalYearId);

            event(new ExpenseModified($expense, $request->user(), 'updated'));
        });

        return new ExpenseResource($expense->fresh()->load('budgetItem'));
    }

    /**
     * Delete expense
     *
     * @authenticated
     */
    public function destroy(Request $request, Expense $expense): Response
    {
        $this->authorize('delete', $expense);

        DB::transaction(function () use ($expense, $request) {
            $governmentUnitId = $expense->budgetItem->budget->government_unit_id;
            $fiscalYearId = $expense->budgetItem->budget->fiscal_year_id;

            $expense->delete();

            // Clear cache immediately for instant analytics update
            $this->clearAnalyticsCaches($governmentUnitId, $fiscalYearId);

            // Dispatch job for background recalculation
            RecalculateBudgetAnalytics::dispatch($governmentUnitId, $fiscalYearId);

            event(new ExpenseModified($expense, $request->user(), 'deleted'));
        });

        return response()->noContent();
    }

    /**
     * Get expense summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $query = Expense::query();

        // Apply same filters as index
        if ($request->filled('budget_id') && $request->integer('budget_id')) {
            $query->whereHas('budgetItem', function ($q) use ($request) {
                $q->where('budget_id', $request->budget_id);
            });
        }

        if ($request->filled('budget_item_id') && $request->integer('budget_item_id')) {
            $query->where('budget_item_id', $request->budget_item_id);
        }

        if ($request->filled('from_date')) {
            $query->where('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('transaction_date', '<=', $request->to_date);
        }

        // Calculate statistics
        $total = $query->sum('amount');
        $count = $query->count();
        $average = $count > 0 ? $total / $count : 0;

        // Calculate this month's total
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $monthTotal = Expense::query()
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->when(
                $request->filled('budget_id'),
                fn($q) => $q->whereHas('budgetItem', fn($q2) => $q2->where('budget_id', $request->budget_id))
            )
            ->when(
                $request->filled('budget_item_id'),
                fn($q) => $q->where('budget_item_id', $request->budget_item_id)
            )
            ->sum('amount');

        return response()->json([
            'total' => $total,
            'count' => $count,
            'average' => $average,
            'month_total' => $monthTotal,
        ]);
    }
}
