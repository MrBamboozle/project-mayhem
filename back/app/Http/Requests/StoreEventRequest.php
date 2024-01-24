<?php

namespace App\Http\Requests;

use App\Enums\JsonFieldNames;
use App\Enums\RequestRules;
use App\Rules\OsmAddressRule;
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
            JsonFieldNames::TITLE->value => [
                RequestRules::REQUIRED->value,
                RequestRules::STRING->value
            ],
            JsonFieldNames::TAG_LINE->value => [RequestRules::STRING->value],
            JsonFieldNames::DESCRIPTION->value => [
                RequestRules::REQUIRED->value,
                RequestRules::STRING->value
            ],
            JsonFieldNames::START_TIME->value => [
                RequestRules::REQUIRED->value,
                RequestRules::DATE->value
            ],
            JsonFieldNames::END_TIME->value => [
                RequestRules::REQUIRED->value,
                RequestRules::DATE->value
             ],
            JsonFieldNames::LOCATION->value => [
                RequestRules::REQUIRED->value,
                RequestRules::STRING->value
            ],
            JsonFieldNames::USER_ID->value => [RequestRules::PROHIBITED->value],
            JsonFieldNames::CITY_ID->value => [RequestRules::PROHIBITED->value],
            JsonFieldNames::CATEGORIES->value => [RequestRules::ARRAY->value], //custom validation, usually required
            JsonFieldNames::ADDRESS->value => [new OsmAddressRule],
        ];
    }
}
