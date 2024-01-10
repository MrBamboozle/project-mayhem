<?php

namespace App\Http\Requests;

use App\Enums\JsonFieldNames;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            JsonFieldNames::EMAIL->value => 'string | required',
            JsonFieldNames::PASSWORD->value => 'string | required'
        ];
    }
}
