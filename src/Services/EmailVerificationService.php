<?php

namespace Fuisic\Auth\Services;

use Fuisic\Auth\Jobs\SendVerificationEmailJob;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EmailVerificationService
{
    public function send(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        SendVerificationEmailJob::dispatch($user)
            ->onConnection(config('fuisic-auth.queue.connection'))
            ->onQueue(config('fuisic-auth.queue.verification'));
    }

    public function ensureCanLogin(MustVerifyEmail $user): void
    {
        if (! config('fuisic-auth.require_email_verification')) {
            return;
        }

        if (! $user->hasVerifiedEmail()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => [__('fuisic-auth::auth.email_not_verified')],
            ])->status(403);
        }
    }
}
