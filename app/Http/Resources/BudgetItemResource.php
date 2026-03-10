<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $spentAmount = (float) ($this->expenses_sum_amount ?? 0);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'allocated_amount' => (float) $this->allocated_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Budget category ID for forms
            'budget_category_id' => $this->budget_category_id,
            // Conditionally load relationships to prevent N+1 problems
            'budget' => new BudgetResource($this->whenLoaded('budget')),
            'category' => new BudgetCategoryResource($this->whenLoaded('category')),
            // Return both for backwards compatibility
            'spent_amount' => $spentAmount,
            'expenses_sum_amount' => $spentAmount,
        ];
    }
}
