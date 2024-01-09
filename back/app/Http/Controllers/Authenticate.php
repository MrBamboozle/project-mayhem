<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Exceptions\BaseException;
use App\Exceptions\Exceptions\InvalidCredentialsException;
use App\Exceptions\Exceptions\InvalidTokenGeneration\MalformedRefreshTokenException;
use App\Exceptions\Exceptions\InvalidTokenGeneration\UnableToGenerateTokenPairsException;
use App\Http\Requests\AuthenticateRequest;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Services\TokenGenerateService\TokenGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Authenticate extends Controller
{
    public function __construct(
        protected TokenGeneration $tokenGenerationService
    ){}

    /**
     * @throws BaseException
     */
    public function login(AuthenticateRequest $request): array
    {
        if (!Auth::attempt($request->validated())) {
            throw new InvalidCredentialsException();
        }

        Auth::login(User::where('email', $request->email)->firstOrFail());
        /** @var User $user */
        $user = Auth::user();

        $tokenPair = $this->tokenGenerationService->generateTokenPair($user);

        return [
            'user' => $user,
            'token' => $tokenPair->getAccessToken()->plainTextToken,
            'refresh_token' => $tokenPair->getRefreshToken()->plainTextToken,
        ];
    }

    public function logout(): array
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function loggedInUser(Request $request): User
    {
        return $request->user();
    }

    /**
     * @throws MalformedRefreshTokenException
     * @throws UnableToGenerateTokenPairsException
     */
    public function refreshAccessToken(): array
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $accessToken = $token->parentToken;

        // if access token not yet expired we assume it is a malicious user and want to stop him in his tracks
        // normal users do not request token refresh if access token has not yet expired
        if ($accessToken->expires_at > now()) {
            $user->tokens()->delete();

            throw new MalformedRefreshTokenException();
        }

        $tokenPair = $this->tokenGenerationService->refreshTokenPair($user, $accessToken, $token);

        return [
            'token' => $tokenPair->getAccessToken()->plainTextToken,
            'refresh_token' => $tokenPair->getRefreshToken()->plainTextToken
        ];
    }

    public function register(): array
    {
        return ['token' => 'moj mali lkurac'];
    }
}
