<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $userName;
    protected $userEmail;
    protected $userPassword;
    protected $dashboardUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($userName, $userEmail, $userPassword, $dashboardUrl = null)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userPassword = $userPassword;
        $this->dashboardUrl = $dashboardUrl ?? url('/dashboard');
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
                'userName'     => $this->userName,
                'userEmail'    => $this->userEmail,
                'userPassword' => $this->userPassword,
                'dashboardUrl' => $this->dashboardUrl,
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
            'userName'     => $this->userName,
            'userEmail'    => $this->userEmail,
            'dashboardUrl' => $this->dashboardUrl,
        ];
    }
}
