<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
 use Illuminate\Notifications\Notification;
//use Notification;
//use Illuminate\Support\Facades\Notification;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
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
        ->greeting('Hello!')
        ->subject('New Notification [ '.$this->data['service'].']')
        ->line('You have a new notification')
        ->line('Service: '.$this->data['service'])
        ->line($this->data['message']);

        // return (new MailMessage)
        // ->from($this->balance_data['from'])
        // ->subject('Balance Alert')
        // ->line('Your have balance alert'.PHP_EOL.$this->balance_data['message']);
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
            'service' => $this->data['service'],
            'message'=>$this->data['message']
        ];
    }
}
