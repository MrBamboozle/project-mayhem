<?php

namespace App\Http\Requests;

use App\Enums\JsonFieldNames;
use App\Enums\RequestRules;
use App\Rules\EventCategoriesRule;
use App\Rules\OsmAddressRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            JsonFieldNames::PASSWORD->value . 'Old' => [
                RequestRules::REQUIRED->value,
                RequestRules::STRING->value,
            ],
            JsonFieldNames::PASSWORD->value => [
                RequestRules::REQUIRED->value,
                RequestRules::STRING->value,
                'min:8'
            ],
        ];
    }
}
