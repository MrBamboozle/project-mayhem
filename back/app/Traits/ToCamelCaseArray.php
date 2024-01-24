<?php

namespace App\Traits;

use App\Exceptions\Exceptions\CannotImplementThisMethodException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait ToCamelCaseArray
{
    /**
     * @throws CannotImplementThisMethodException
     */
    public function toCamelCaseArray(): array
    {
        if (!$this instanceof Model) {
            throw new CannotImplementThisMethodException('toCamelCaseArray', $this::class);
        }

        return $this->camelize($this->toArray());
    }

    private function camelize(array $data): array
    {
        $array = [];

        foreach ($data as $property => $value) {
            $propertyValue = $value;

            if (is_object($value)) {
                $propertyValue = json_decode(json_encode($value), true);
            }

            if (Str::contains($property, '_')) {
                $array[Str::camel($property)] = is_iterable($propertyValue) ? $this->camelize($propertyValue) : $propertyValue;

                continue;
            }

            $array[$property] = is_iterable($propertyValue) ? $this->camelize($propertyValue) : $propertyValue;
        }

        return $array;
    }
}
