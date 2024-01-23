<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class OsmAddressRule implements ValidationRule
{
    private const SUBDIVISION_ID = 'countrySubdivisionId';
    private const CITY = 'city';

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("$attribute should be an object");
        }

        if (!array_key_exists(self::SUBDIVISION_ID, $value) || !array_key_exists(self::CITY, $value)) {
            $fail($attribute . ' should contain ' . self::CITY . ' and ' . self::SUBDIVISION_ID);
        }

        if ($value[self::CITY] !== null && !is_string($value[self::CITY])) {
            $fail(self::CITY . 'must be string or null');
        }

        if (!is_string($value[self::SUBDIVISION_ID])) {
            $fail(self::SUBDIVISION_ID . ' must be a string');
        }
    }
}
