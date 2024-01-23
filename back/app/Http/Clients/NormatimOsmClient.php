<?php

namespace App\Http\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

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
        return $this->reverseSearch($parameters)->json('address');
    }
}
