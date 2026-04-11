<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PaymentSuccessNotification extends Notification
{
    use Queueable;

    protected Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'reference' => $this->payment->reference,
            'amount' => $this->payment->total_amount,
            'status' => $this->payment->status,
            'school_name' => $this->payment->school?->name,
            'message' => 'Your payment of GH₵' . number_format($this->payment->total_amount, 2) . ' has been received successfully.',
            'action_url' => route('admin.payments.show', $this->payment),
            'action_text' => 'View Payment',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Successful - ' . config('app.name'))
            ->greeting('Hello!')
            ->line('Your payment has been received successfully.')
            ->line('Amount: GH₵' . number_format($this->payment->total_amount, 2))
            ->line('Reference: ' . $this->payment->reference)
            ->line('Status: ' . ucfirst($this->payment->status))
            ->action('View Payment Details', route('admin.payments.show', $this->payment))
            ->line('Thank you for using our service!');
    }
}
