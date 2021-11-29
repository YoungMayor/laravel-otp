<?php

namespace YoungMayor\LaravelOtp\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YoungMayor\LaravelOtp\Models\OneTimePin;

class OTPGeneratedNotification extends Notification
{
    use Queueable;

    public $oneTimePin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(OneTimePin $oneTimePin)
    {
        $this->oneTimePin = $oneTimePin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->oneTimePin->properties['subject'])
            ->greeting($this->oneTimePin->properties['greeting'])
            ->line($this->oneTimePin->properties['message'])
            ->line("**{$this->oneTimePin->pin}**")
            ->line("Please note: OTP would expire in {$this->oneTimePin->properties['decay']} minutes on {$this->oneTimePin->expires_at->format('F jS, Y \a\t h:ia')}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
