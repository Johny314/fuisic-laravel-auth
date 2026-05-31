<?php

namespace Fuisic\Auth\Jobs;

use Fuisic\Auth\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPasswordResetEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public CanResetPassword $user,
        public string $token,
    ) {}

    public function handle(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            $frontend = rtrim(config('fuisic-auth.frontend_url'), '/');

            return $frontend.'/reset-password?token='.$token.'&email='.urlencode($notifiable->getEmailForPasswordReset());
        });

        $this->user->notify(new ResetPasswordNotification($this->token));
    }
}
