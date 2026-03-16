<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\MenuItem;

use App\Enum\MenuItem\CategoryEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class IndexMenuItemRequest extends FormRequest
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
            'category' => ['sometimes', 'nullable', 'max:255', new Enum(CategoryEnum::class)],
            'search' => ['sometimes', 'max:255'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages()
    {
        return [
            'category.enum' => 'The selected category is invalid. Valid categories are: ' . implode(', ', CategoryEnum::values()),
        ];
    }
}
