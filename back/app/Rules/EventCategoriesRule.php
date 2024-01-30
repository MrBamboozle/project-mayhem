<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class EventCategoriesRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("$attribute should be an array of Uudi's");

            return;
        }

        $areAllUuids = true;

        foreach ($value as $categoryId) {
            if (!Str::isUuid($categoryId)) {
                $areAllUuids = false;

                break;
            }
        }

        if (!$areAllUuids) {
            $fail("$attribute should be an array of Uudi's");
        }
    }
}
