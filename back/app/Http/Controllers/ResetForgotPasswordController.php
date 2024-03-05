<?php

namespace App\Http\Controllers;

use App\Enums\Operators;
use App\Mail\ResetPassword;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ResetForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request): array
    {
        $validated = $request->validate(['email' => 'required|email']);
        $validatedEmail = $validated['email'];

        $user = User::where('email', Operators::EQUALS->value, $validatedEmail)->get();

        if ($user->isEmpty()) {
            return [];
        }

        if ($user->count() > 1) {
            return [];
        }

        // check if valid token already exists in the db
        $tokens = PasswordResetToken::where('email', Operators::EQUALS->value, $validatedEmail)->get();

        // if it exists and valid do nothing else continue.
        if ($tokens->isNotEmpty()) {
            $invalidTs = $tokens->filter(fn (PasswordResetToken $rt) => $rt->created_at->addMinutes(10) < now());
            $invalidTs->each(fn (PasswordResetToken $rt) => $rt->delete());

            if ($tokens->diff($invalidTs)->isNotEmpty()) {
                return [];
            }
        }

        // generate temporary signed url
        $signedUrl = URL::temporarySignedRoute('passwordForm', now()->addMinutes(10), ['user' => $user->first()->id]);
        $token = Str::remove(
            'signature=',
            collect(explode('&', $signedUrl))->first(
                fn (string $item):string => Str::contains($item, 'signature=')
            )
        );

        // save token to database from url
        $newPasswordResetToken = PasswordResetToken::create([
            'email' => $user->first()->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // send email with url that redirects to reset password form
        Mail::to($user)->queue(new ResetPassword($signedUrl));

        return [
            'message' => 'Password reset email has been sent'
        ];
    }

    public function resetPassword(Request $request, string $token)
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        $tokenCollection = PasswordResetToken::where('token', Operators::EQUALS->value, $token)->get();

        if ($tokenCollection->isEmpty()) {
            return view('linkExpired');
        }

        if ($tokenCollection->count() > 1) {
            return view('linkExpired');
        }

        $userToken = $tokenCollection->first();

        if ($userToken->created_at->addMinutes(10) < now()) {
            return view('linkExpired');
        }

        $userCollection = User::where('email', Operators::EQUALS->value, $userToken->email)->get();

        if ($userCollection->isEmpty()) {
            return view('linkExpired');
        }

        if ($userCollection->count() > 1) {
            return view('linkExpired');
        }

        $user = $userCollection->first();

        $user->password = Hash::make($validated['password']);
        $user->save();

        return view('passwordChangeSuccess');
    }
}
