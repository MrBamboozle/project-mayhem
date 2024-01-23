<?php

namespace App\Http\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NormatimOsmClient
{
    private readonly string $url;

    private readonly string $format;

    public function __construct() {
        $config = config('clients.' . self::class);

        $this->url = $config['url'] ?? null;
        $this->format = $config['format'] ?? null;
    }

    public function reverseSearch(string $parameters): Response
    {
        $parametersParts = explode(',', $parameters);
        $latitude = trim($parametersParts[0]);
        $longitude = trim($parametersParts[1]);

        return Http::withHeaders(['Accept-Language' => 'en'])
            ->get(
                $this->url .
                'reverse?' .
                $this->format .
                "&lat=$latitude&lon=$longitude&zoom=18&addressdetails=1"
            )
        ;
    }

    public function getOsmAddress(string $parameters): array
    {
        return $this->serializeAddress($this->reverseSearch($parameters)->json('address'));
    }

    private function serializeAddress(array $address): array
    {
        $serializedAddress = ['city' => null];

        foreach ($address as $fieldName => $value) {
            if (Str::contains($fieldName, 'ISO3166')) {
                $serializedAddress['countrySubdivisionId'] = $value;

                continue;
            }

            if ($fieldName === 'town') {
                $serializedAddress['city'] = $value;

                continue;
            }

            $serializedAddress[Str::camel($fieldName)] = $value;
        }

        return $serializedAddress;
    }

}
