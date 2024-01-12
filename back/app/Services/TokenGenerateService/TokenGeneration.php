<?php

namespace App\Services\TokenGenerateService;

use App\Enums\TokenAbility;
use App\Exceptions\Exceptions\InvalidTokenGeneration\UnableToGenerateTokenPairsException;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TokenGeneration
{
    /**
     * @throws UnableToGenerateTokenPairsException
     */
    public function generateTokenPair(User $user): TokenPairDto
    {
        DB::beginTransaction();
        try {
            $accessToken = $user
                ->createToken(
                    'access_token',
                    null,
                    [TokenAbility::ACCESS_API->value],
                    now()->addMinutes(config('sanctum.expiration')),
                );
            $refreshToken = $user
                ->createToken(
                    'refresh_token',
                    $accessToken->accessToken->id,
                    [TokenAbility::ISSUE_ACCESS_TOKEN->value],
                    now()->addMinutes(config('sanctum.rt_expiration')),
                );
        } catch (\Throwable $error) {
            DB::rollBack();

            throw new UnableToGenerateTokenPairsException($error->getMessage());
        }

        DB::commit();

        return new TokenPairDto($accessToken, $refreshToken);
    }

    /**
     * @throws UnableToGenerateTokenPairsException
     */
    public function refreshTokenPair(User $user, PersonalAccessToken $refreshToken, PersonalAccessToken $accessToken): TokenPairDto
    {
        DB::beginTransaction();
        try {
            $tokenPair = $this->generateTokenPair($user);
            $refreshToken->delete();
            $accessToken->delete();
        } catch (\Throwable $error) {
            DB::rollBack();

            throw new UnableToGenerateTokenPairsException($error->getMessage());
        }

        DB::commit();

        return $tokenPair;
    }
}
