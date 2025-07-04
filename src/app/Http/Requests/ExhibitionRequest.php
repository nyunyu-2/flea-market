<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|file|mimes:jpeg,png|max:2048',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'status' => 'required|string',
            'price' => 'required|integer|min:0',
        ];
    }
}

