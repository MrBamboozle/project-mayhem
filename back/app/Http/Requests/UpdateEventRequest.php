<?php

namespace App\Http\Requests;

use App\Enums\JsonFieldNames;
use App\Enums\RequestRules;
use App\Rules\EventCategoriesRule;
use App\Rules\OsmAddressRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
                RequestRules::STRING->value
            ],
            JsonFieldNames::TAG_LINE->value => [RequestRules::STRING->value],
            JsonFieldNames::DESCRIPTION->value => [
                RequestRules::STRING->value
            ],
            JsonFieldNames::START_TIME->value => [
                RequestRules::DATE->value
            ],
            JsonFieldNames::END_TIME->value => [
                RequestRules::DATE->value
            ],
            JsonFieldNames::LOCATION->value => [
                RequestRules::STRING->value
            ],
            JsonFieldNames::USER_ID->value => [RequestRules::PROHIBITED->value],
            JsonFieldNames::CITY_ID->value => [RequestRules::PROHIBITED->value],
            JsonFieldNames::CATEGORIES->value => [new EventCategoriesRule()], //if not sent, all existing categories will be deleted
            JsonFieldNames::ADDRESS->value => [new OsmAddressRule],

        ];
    }
}
