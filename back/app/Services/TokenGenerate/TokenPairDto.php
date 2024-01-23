<?php

namespace App\Services\TokenGenerate;

use App\Models\PersonalAccessToken;
use Faker\Provider\Person;
use Laravel\Sanctum\NewAccessToken;

class TokenPairDto
{
    private NewAccessToken $accessToken;

    private NewAccessToken $refreshToken;

    public function __construct(NewAccessToken $accessToken, NewAccessToken $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return NewAccessToken
     */
    public function getAccessToken(): NewAccessToken
    {
        return $this->accessToken;
    }

    /**
     * @return NewAccessToken
     */
    public function getRefreshToken(): NewAccessToken
    {
        return $this->refreshToken;
    }
}
