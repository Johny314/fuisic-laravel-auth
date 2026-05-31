<?php

namespace Fuisic\Auth\Jobs;

use Fuisic\Auth\Notifications\VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public MustVerifyEmail $user,
    ) {}

    public function handle(): void
    {
        $this->user->notify(new VerifyEmailNotification);
    }
}
