<?php

namespace App\Http\Controllers;

use App\Exceptions\BaseException;
use App\Exceptions\Exceptions\InvalidCredentialsException;
use App\Http\Requests\AuthenticateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class Authenticate extends Controller
{
    /**
     * @throws BaseException
     */
    public function login(AuthenticateRequest $request): array
    {
        if (!Auth::attempt($request->validated())) {
            throw new InvalidCredentialsException();
        }

        Auth::login(User::where('email', $request->email)->firstOrFail());
        $user = Auth::user();

        /** @var PersonalAccessToken[] $tokens */
        $tokens = $user->tokens;

        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                $token->delete();
            }
        }

        return [
            'token' => $user
                ->createToken(
                    'secret',
                    ['*'],
                    now()->addDays(6)
                )->plainTextToken
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
}
