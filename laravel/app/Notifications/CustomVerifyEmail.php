<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class CustomVerifyEmail extends BaseVerifyEmail

{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Подтверждение электронной почты')
            ->line('Спасибо за регистрацию в системе StyleFasad!')
            ->line('Для завершения регистрации подтвердите ваш адрес электронной почты.')
            ->action('Подтвердить Email', $url)
            ->line('Если вы не создавали аккаунт — просто проигнорируйте это письмо.')
            ->line('После подтверждения вы сможете получать уведомления о заказах, статусах и других важных событиях.');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }



}
