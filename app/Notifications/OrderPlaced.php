<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\SMSChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        
        // Add SMS channel if customer has phone
        if ($notifiable->phone) {
            $channels[] = SMSChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for your order.')
            ->line('Order Number: **' . $this->order->order_number . '**')
            ->line('Total Amount: GH₵ ' . number_format($this->order->total, 2))
            ->line('Status: ' . ucfirst(str_replace('_', ' ', $this->order->status)))
            ->action('View Order', route('manager.orders.show', $this->order))
            ->line('We will notify you when your order status changes.');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSMS(object $notifiable): string
    {
        return sprintf(
            "Thank you for your order! Order #%s for GH₵%s has been placed. We'll notify you of any updates.",
            $this->order->order_number,
            number_format($this->order->total, 2)
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'status' => $this->order->status,
            'message' => 'New order placed: ' . $this->order->order_number,
        ];
    }
}
