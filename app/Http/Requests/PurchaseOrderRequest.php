<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
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
        return [
            'supplier_id' => 'required|integer',
            'orders' => 'required|array|min:1',
            'orders.*.item_id' => 'required|exists:stock_items,id',
            'orders.*.qty_ordered' => 'required|min:1',
            'orders.*.qty_received' => 'nullable|min:0',
            'orders.*.unit_cost' => 'nullable|numeric',
            'orders.*.unit' => 'nullable|string',
            'status' => 'nullable|integer',
        ];
    }
}
