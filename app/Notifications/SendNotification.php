<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class SendNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }   
    
    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        // dd($notifiable);
        $data = '*New Booking*'.' '.PHP_EOL.' '.PHP_EOL;
        $data = $data.'Name: *'.$notifiable->name.'* '.PHP_EOL.' '.PHP_EOL;
        $data = $data.'Phone: *'.$notifiable->phone.'* '.PHP_EOL.' '.PHP_EOL;
        $data = $data.'Time: *'.$notifiable->time.'* '.PHP_EOL.' '.PHP_EOL;
        $data = $data.'Date: *'.$notifiable->date.'*';
        
        return TelegramMessage::create()
            ->to('-4059315960')
            ->content($data)
            ->button('View Order', 'http://127.0.0.1:3000/result?phone='.$notifiable->phone);
    }
}