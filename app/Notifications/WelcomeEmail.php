<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Notification
{
    use Queueable;

    protected $userName;
    protected $dashboardUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($userName)
    {
        $this->userName = $userName;
        $this->dashboardUrl = 'https://www.capitronix.com/dashboard';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Welcome to ' . config('app.name'))
                    ->view('mail.Welcome', [
                        'userName' => $this->userName,
                        'dashboardUrl' => $this->dashboardUrl
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
