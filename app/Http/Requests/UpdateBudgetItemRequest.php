<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Budget;
use App\Models\BudgetItem;

class UpdateBudgetItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var BudgetItem $budgetItem */
        $budgetItem = $this->route('budget_item');

        return [
            'budget_id' => ['sometimes', 'required', 'integer', Rule::exists('budgets', 'id')],
            'budget_category_id' => ['sometimes', 'required', 'integer', Rule::exists('budget_categories', 'id')],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('budget_items', 'code')->ignore($budgetItem)],
            'allocated_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty() && $this->has('allocated_amount')) {
                $this->validateTotalAllocation($validator);
            }
        });
    }

    /**
     * Validate that total allocated doesn't exceed budget total
     */
    private function validateTotalAllocation($validator): void
    {
        /** @var BudgetItem $budgetItem */
        $budgetItem = $this->route('budget_item');

        // Use the current or new budget_id
        $budgetId = $this->input('budget_id', $budgetItem->budget_id);
        $newAllocation = $this->input('allocated_amount');

        $budget = Budget::find($budgetId);
        if (!$budget) {
            return;
        }

        // Calculate current total allocation excluding this item
        $currentTotalAllocation = BudgetItem::where('budget_id', $budgetId)
            ->where('id', '!=', $budgetItem->id)
            ->sum('allocated_amount');

        // Add the new/updated allocation
        $projectedTotal = $currentTotalAllocation + $newAllocation;

        if ($projectedTotal > $budget->total_amount) {
            $remaining = $budget->total_amount - $currentTotalAllocation;
            $validator->errors()->add(
                'allocated_amount',
                "The allocated amount exceeds the remaining budget. " .
                    "Budget total: " . number_format($budget->total_amount, 2) . ", " .
                    "Currently allocated: " . number_format($currentTotalAllocation, 2) . ", " .
                    "Remaining: " . number_format($remaining, 2) . "."
            );
        }
    }

    public function messages(): array
    {
        return [
            'budget_id.required' => 'The budget ID is required when provided.',
            'budget_id.integer' => 'The budget ID must be a valid integer.',
            'budget_id.exists' => 'The selected budget does not exist.',

            'budget_category_id.required' => 'The budget category ID is required when provided.',
            'budget_category_id.integer' => 'The budget category ID must be a valid integer.',
            'budget_category_id.exists' => 'The selected budget category does not exist.',

            'name.required' => 'The budget item name is required when provided.',
            'name.string' => 'The budget item name must be a valid string.',
            'name.max' => 'The budget item name may not exceed 255 characters.',

            'code.required' => 'The budget item code is required when provided.',
            'code.string' => 'The budget item code must be a valid string.',
            'code.max' => 'The budget item code may not exceed 50 characters.',
            'code.unique' => 'This budget item code is already in use.',

            'allocated_amount.required' => 'The allocated amount is required when provided.',
            'allocated_amount.numeric' => 'The allocated amount must be a number.',
            'allocated_amount.min' => 'The allocated amount must be zero or greater.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'budget_id' => [
                'description' => 'ID of the parent budget to which this budget item belongs.',
                'example' => 1,
                'required' => false,
            ],

            'budget_category_id' => [
                'description' => 'ID of the budget category used to classify this budget item.',
                'example' => 3,
                'required' => false,
            ],

            'name' => [
                'description' => 'Updated name of the budget item.',
                'example' => 'Road and Bridge Rehabilitation',
                'required' => false,
            ],

            'code' => [
                'description' => 'Updated unique reference code for the budget item.',
                'example' => 'INFRA-RBR-002',
                'required' => false,
            ],

            'allocated_amount' => [
                'description' => 'Updated allocated amount for this budget item. Must be a non-negative number.',
                'example' => 3000000.00,
                'required' => false,
            ],
        ];
    }
}
