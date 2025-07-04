<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\AddressRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'nullable|image|max:2048',
            'username' => 'nullable|string|max:255',
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }
}
