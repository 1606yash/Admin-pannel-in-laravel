<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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

    // Fcm notification
    public function toFcm($notifiable) { 
        try {
            $message = new FcmMessage();
 
            $message->content([
            'title'        => 'Parivaar', 
            'body'         => $notifiable->message, 
            ]);
 
            if(isset($notifiable->data) && !empty($notifiable->data)) {
                $message = $message->data([
                    'data' => $notifiable->data 
                ]); 
            }
            // Opt
            \Storage::put('file_resposne.txt', json_encode($message));  
            return $message;
        } catch(Exception $e) {
          \Storage::put('file_resposne1.txt', $e->getMessage()); 
        }
    }
}
