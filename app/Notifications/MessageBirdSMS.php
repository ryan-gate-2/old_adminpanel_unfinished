<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Messagebird\MessagebirdMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class MessageBirdSMS extends Notification
{
    public function via($notifiable)
    {
        return [MessagebirdChannel::class];
    }

    public function toMessagebird($notifiable)
    {
        $sender = (new MessagebirdMessage("Test Message"));
        Log::warning(json_encode($sender));
        return $sender;

    }
}