<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\SMSChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public string $oldStatus
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Send email for important status changes
        if (in_array($this->order->status, ['payment_received', 'ready_for_delivery', 'out_for_delivery', 'delivered', 'cancelled'])) {
            $channels[] = 'mail';
        }
        
        // Always send SMS for status updates (if phone exists)
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
        $statusInfo = $this->getStatusInfo($this->order->status);
        
        $mailMessage = (new MailMessage)
            ->subject('Order Status Update - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($statusInfo['title']);

        if ($statusInfo['description']) {
            $mailMessage->line($statusInfo['description']);
        }

        $mailMessage->line('Order Number: **' . $this->order->order_number . '**')
            ->line('New Status: **' . ucfirst(str_replace('_', ' ', $this->order->status)) . '**');

        if ($statusInfo['action']) {
            $mailMessage->line($statusInfo['action']);
        }

        $mailMessage->action('View Order Details', route('manager.orders.show', $this->order))
            ->line('Thank you for choosing RealMan Livestock!');

        return $mailMessage;
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSMS(object $notifiable): string
    {
        $statusInfo = $this->getStatusInfo($this->order->status);
        
        return sprintf(
            "Order #%s: %s",
            $this->order->order_number,
            $statusInfo['sms']
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'message' => $this->getStatusInfo($this->order->status)['title'],
        ];
    }

    /**
     * Get status-specific information for notifications
     *
     * @param string $status
     * @return array
     */
    protected function getStatusInfo(string $status): array
    {
        $statusMap = [
            'pending' => [
                'title' => 'Your order has been received',
                'description' => 'We are reviewing your order and will process it shortly.',
                'action' => 'No action needed at this time.',
                'sms' => 'Order received and is being reviewed.',
            ],
            'processing' => [
                'title' => 'Your order is being prepared',
                'description' => 'Our team is currently processing your order.',
                'action' => 'We\'ll notify you once it\'s ready.',
                'sms' => 'Your order is now being prepared.',
            ],
            'payment_received' => [
                'title' => 'Payment confirmed!',
                'description' => 'We have received your payment. Your order is now in the queue for processing.',
                'action' => 'We\'ll start processing your order shortly.',
                'sms' => 'Payment confirmed! Your order will be processed soon.',
            ],
            'ready_for_delivery' => [
                'title' => 'Your order is ready!',
                'description' => 'Great news! Your order is ready for pickup or delivery.',
                'action' => 'Please contact us to arrange pickup/delivery.',
                'sms' => 'Your order is ready for pickup/delivery! Contact us to arrange.',
            ],
            'out_for_delivery' => [
                'title' => 'Your order is on the way',
                'description' => 'Your order has been dispatched and is out for delivery.',
                'action' => 'You should receive it shortly.',
                'sms' => 'Your order is out for delivery and will arrive soon.',
            ],
            'delivered' => [
                'title' => 'Order delivered successfully',
                'description' => 'Your order has been delivered. We hope you enjoy your purchase!',
                'action' => 'Thank you for your business. We look forward to serving you again.',
                'sms' => 'Your order has been delivered. Thank you!',
            ],
            'cancelled' => [
                'title' => 'Your order has been cancelled',
                'description' => 'Your order has been cancelled as requested.',
                'action' => 'If you did not request this cancellation, please contact us immediately.',
                'sms' => 'Your order has been cancelled. Contact us if you have questions.',
            ],
        ];

        return $statusMap[$status] ?? [
            'title' => 'Order status updated',
            'description' => 'Your order status has been changed to: ' . ucfirst(str_replace('_', ' ', $status)),
            'action' => null,
            'sms' => 'Status: ' . ucfirst(str_replace('_', ' ', $status)),
        ];
    }
}
