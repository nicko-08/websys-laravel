<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\BudgetModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\RecalculateBudgetAnalytics;
use Illuminate\Support\Facades\Cache;

/**
 * @group Budgets
 * Budget management endpoints
 */
class BudgetController extends Controller
{
    /**
     * List budgets
     *
     * @queryParam page integer Page number. Example: 1
     */
    public function index(): AnonymousResourceCollection
    {
        return BudgetResource::collection(
            Budget::with(['governmentUnit', 'fiscalYear'])
                ->latest()
                ->paginate(15)
        );
    }

    /**
     * Create budget
     *
     * @authenticated
     *
     * @bodyParam name string required Budget name. Example: Annual Budget 2024
     * @bodyParam government_unit_id integer required Government unit ID. Example: 1
     * @bodyParam fiscal_year_id integer required Fiscal year ID. Example: 1
     * @bodyParam total_amount number required Total amount. Example: 1000000.00
     */
    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $this->authorize('create', Budget::class);

        $budget = Budget::create($request->validated());

        event(new BudgetModified(
            $budget,
            $request->user(),
            'created'
        ));

        return (new BudgetResource(
            $budget->load(['governmentUnit', 'fiscalYear'])
        ))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Show budget
     */
    public function show(Budget $budget): BudgetResource
    {
        $budget->load([
            'governmentUnit',
            'fiscalYear',
            'budgetItems' => fn($query) => $query->withSum('expenses', 'amount')
                ->with('category'),
        ]);

        return new BudgetResource($budget);
    }

    /**
     * Update budget
     *
     * @authenticated
     */
    public function update(UpdateBudgetRequest $request, Budget $budget): BudgetResource
    {
        $this->authorize('update', $budget);

        $budget->update($request->validated());

        event(new BudgetModified(
            $budget,
            $request->user(),
            'updated'
        ));

        return new BudgetResource(
            $budget->load(['governmentUnit', 'fiscalYear'])
        );
    }

    /**
     * Delete budget
     *
     * @authenticated
     */

    public function destroy(Request $request, Budget $budget): Response
    {
        $this->authorize('delete', $budget);

        DB::transaction(function () use ($budget, $request) {
            $governmentUnitId = $budget->government_unit_id;
            $fiscalYearId = $budget->fiscal_year_id;
            $budgetId = $budget->id;

            $budget->delete();

            // Clear cache IMMEDIATELY for instant UI update
            Cache::forget("analytics:overall-summary:{$fiscalYearId}");
            Cache::forget("analytics:barangay-list:{$fiscalYearId}");
            Cache::forget("analytics:barangay:{$budgetId}");
            Cache::forget("budget:{$budgetId}");

            // Dispatch job for background recalculation
            RecalculateBudgetAnalytics::dispatch(
                $governmentUnitId,
                $fiscalYearId
            );

            event(new BudgetModified(
                $budget,
                $request->user(),
                'deleted'
            ));
        });

        return response()->noContent();
    }
}
