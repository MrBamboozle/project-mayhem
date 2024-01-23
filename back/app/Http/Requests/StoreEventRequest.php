<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required | string',
            'tag_line' => 'string',
            'description' => 'required | string',
            'time' => 'required | Date',
            'location' => 'required | string',
            'user_id' => 'prohibited',
            'city_id ' => 'prohibited',
            'categories' => 'array', //custom validation, usually required
        ];
    }
}
