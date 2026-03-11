<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Calculate spent from loaded budget items
        $spent = 0;
        if ($this->relationLoaded('budgetItems')) {
            $spent = $this->budgetItems->sum(function ($item) {
                return $item->expenses_sum_amount ?? 0;
            });
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_amount' => (float) $this->total_amount,
            'spent' => (float) $spent,

            'government_unit' => new GovernmentUnitResource(
                $this->whenLoaded('governmentUnit')
            ),

            'fiscal_year' => new FiscalYearResource(
                $this->whenLoaded('fiscalYear')
            ),

            'budget_items' => BudgetItemResource::collection(
                $this->whenLoaded('budgetItems')
            ),
        ];
    }
}
