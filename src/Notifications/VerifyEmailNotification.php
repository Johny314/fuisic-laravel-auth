<?php

namespace Fuisic\Auth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'fuisic-auth.verification.verify',
            now()->addMinutes((int) config('fuisic-auth.verification.expire')),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('fuisic-auth::auth.verify_email_subject'))
            ->line(__('fuisic-auth::auth.verify_email_line'))
            ->action(__('fuisic-auth::auth.verify_email_action'), $this->verificationUrl($notifiable))
            ->line(__('fuisic-auth::auth.verify_email_expiry', [
                'count' => config('fuisic-auth.verification.expire'),
            ]));
    }
}
