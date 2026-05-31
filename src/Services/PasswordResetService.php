<?php

namespace Fuisic\Auth\Services;

use Fuisic\Auth\Jobs\SendPasswordResetEmailJob;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetService
{
    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(
            ['email' => $email],
            fn ($user, string $token) => SendPasswordResetEmailJob::dispatch($user, $token)
                ->onConnection(config('fuisic-auth.queue.connection'))
                ->onQueue(config('fuisic-auth.queue.password_reset'))
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    public function reset(string $email, string $token, string $password): string
    {
        $status = Password::reset(
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
                'token' => $token,
            ],
            function ($user) use ($password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }
}
