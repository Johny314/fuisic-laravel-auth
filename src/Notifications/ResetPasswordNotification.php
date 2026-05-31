<?php

namespace Fuisic\Auth\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(config('fuisic-auth.frontend_url').'/reset-password?token='.$this->token.'&email='.urlencode($notifiable->getEmailForPasswordReset()));

        return (new MailMessage)
            ->subject(__('fuisic-auth::auth.reset_password_subject'))
            ->line(__('fuisic-auth::auth.reset_password_line'))
            ->action(__('fuisic-auth::auth.reset_password_action'), $url)
            ->line(__('fuisic-auth::auth.reset_password_expiry', [
                'count' => config('fuisic-auth.password_reset.expire'),
            ]));
    }
}
