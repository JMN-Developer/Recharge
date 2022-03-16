<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
{
    use Queueable;
    private $ticket_data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->ticket_data = $data;
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
        if($this->ticket_data['type']=='Admin')
        {
        return (new MailMessage)
        ->greeting($this->ticket_data['user_name'])
        ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
        ->line('A support ticket has now been opened.The details of ticket are shown below.')
        ->line($this->ticket_data['message'])
        ->line('Service: '.$this->ticket_data['service_name'])
        ->line('Status: '.$this->ticket_data['status']);

        }
        else
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('Thank you for contacting our support team. A support ticket has now been opened for your request. You will be notified when a response is made by email. The details of your ticket are shown below.')
            ->line('Service: '.$this->ticket_data['service_name'])
            ->line('Status: '.$this->ticket_data['status'])
            ->line( 'You can view the ticket at any time at '.'https://jmnation.com/ticket/ticket-response/'.$this->ticket_data['ticket_id']);
        }

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
