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
        if($this->ticket_data['type']=='Reopen User')
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('Service: '.$this->ticket_data['service_name'])
            ->line('Status: Open')
            ->line('Thank you for contacting our support team. A support ticket has now been Re-Opened for your request. You will be notified when a response is made by email.')
            ->line( 'You can view the ticket at any time at '.'https://jmnation.com/ticket/ticket-response/'.$this->ticket_data['ticket_id']);
        }

        if($this->ticket_data['type']=='Reopen Admin')
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('Service: '.$this->ticket_data['service_name'])
            ->line('Status: Open')
            ->line('A support ticket has now been Re-Opened');

        }


        if($this->ticket_data['type']=='Closing User')
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('Service: '.$this->ticket_data['service_name'])
            ->line('Status: Closed')
            ->line('This is a formal notification to let you know that we are glad to work with you and concerned about any type of problems. We are changing the status of ticket #'.$this->ticket_data['ticket_id'].' to closed as we are considering that you do not need further support on this regard. You can feel free to reopen it.')
            ->line( 'You can view the ticket at any time at '.'https://jmnation.com/ticket/ticket-response/'.$this->ticket_data['ticket_id']);
        }



        if($this->ticket_data['type']=='Closing Admin')
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('Service: '.$this->ticket_data['service_name'])
            ->line('Status: Closed')
            ->line('The status of ticket #'.$this->ticket_data['service_name'].' has been closed');

        }

        if($this->ticket_data['type']=='reply')
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('You have a new response for your ticket.')
            ->line($this->ticket_data['message'])
            ->line( 'You can view the ticket at any time at '.'https://jmnation.com/ticket/ticket-response/'.$this->ticket_data['ticket_id']);

        }

        if($this->ticket_data['type']=='Admin')
        {
        return (new MailMessage)
        ->greeting($this->ticket_data['user_name'])
        ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
        ->line('Service: '.$this->ticket_data['service_name'])
        ->line('Status: '.$this->ticket_data['status'])
        ->line('A support ticket has now been opened')
        ->line($this->ticket_data['message']);



        }
        else
        {
            return (new MailMessage)
            ->greeting($this->ticket_data['user_name'])
            ->subject('[Ticket ID: '.$this->ticket_data['ticket_id'].'] '.$this->ticket_data['service_name'])
            ->line('Service: '.$this->ticket_data['service_name'])
            ->line('Status: '.$this->ticket_data['status'])
            ->line('Thank you for contacting our support team. A support ticket has now been opened for your request. You will be notified when a response is made by email.')
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
