<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\MenuItem;

use App\Enum\MenuItem\CategoryEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMenuItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'category' => ['nullable', 'string', 'max:255', new Enum(CategoryEnum::class)],
            'is_available' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'price.decimal' => 'The price must be a valid decimal number with up to 2 decimal places.',
            'category.enum' => 'The selected category is invalid. Valid categories are: '.implode(', ', CategoryEnum::values()),
        ];
    }
}
