<?php

namespace App\Http\Controllers;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;
use App\Exceptions\Exceptions\FailToAddAvatarException;
use App\Exceptions\Exceptions\FailToDeleteCurrentAvatar;
use App\Exceptions\Exceptions\InvalidCredentialsException;
use App\Exceptions\Exceptions\InvalidTokenGeneration\MalformedRefreshTokenException;
use App\Exceptions\Exceptions\InvalidTokenGeneration\UnableToGenerateTokenPairsException;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\VerifyEmail;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Services\TokenGenerate\TokenGeneration;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class Authenticate extends Controller
{
    public function __construct(
        protected TokenGeneration $tokenGenerationService,
        protected UserService $userService
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
            JsonFieldNames::USER->value=> $user,
            JsonFieldNames::TOKEN->value => $tokenPair->getAccessToken()->plainTextToken,
            JsonFieldNames::REFRESH_TOKEN->value => $tokenPair->getRefreshToken()->plainTextToken,
        ];
    }

    public function logout(): array
    {
        auth()->user()->tokens()->delete();

        return [
            JsonFieldNames::MESSAGE->value => 'Logged out'
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
            JsonFieldNames::TOKEN->value => $tokenPair->getAccessToken()->plainTextToken,
            JsonFieldNames::REFRESH_TOKEN->value => $tokenPair->getRefreshToken()->plainTextToken,
        ];
    }

    /**
     * @param RegisterRequest $request
     * @return array
     * @throws UnableToGenerateTokenPairsException
     * @throws FailToAddAvatarException
     * @throws FailToDeleteCurrentAvatar
     */
    public function register(RegisterRequest $request): array
    {
        $user = $this->userService->createUser($request->validated());
        $tokenPair = $this->tokenGenerationService->generateTokenPair($user);

        $url = URL::temporarySignedRoute('verifyEmail', now()->addMinutes(5), ['user' => $user->id]);

        Mail::to($user)->queue(new VerifyEmail($user, $url));

        return [
            JsonFieldNames::USER->value=> $user,
            JsonFieldNames::TOKEN->value => $tokenPair->getAccessToken()->plainTextToken,
            JsonFieldNames::REFRESH_TOKEN->value => $tokenPair->getRefreshToken()->plainTextToken,
        ];
    }

    public function verifyEmail(Request $request, User $user)
    {
        if ($this->userService->verifyUserEmail($user, $request)) {
            return view('emailVerified');
        }

        return view('linkExpired');
    }
}
