<?php

namespace App\Http\Requests;

use App\Enums\JsonFieldNames;
use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            JsonFieldNames::LOCATION->value => 'string | required',
        ];
    }
}
