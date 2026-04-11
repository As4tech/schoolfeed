<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    protected Student $student;
    protected float $amount;
    protected ?string $dueDate;

    public function __construct(Student $student, float $amount, ?string $dueDate = null)
    {
        $this->student = $student;
        $this->amount = $amount;
        $this->dueDate = $dueDate;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = 'Payment reminder: GH₵' . number_format($this->amount, 2) . ' is due';
        if ($this->dueDate) {
            $message .= ' on ' . $this->dueDate;
        }
        $message .= ' for ' . $this->student->full_name;

        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->full_name,
            'amount' => $this->amount,
            'due_date' => $this->dueDate,
            'message' => $message,
            'action_url' => route('payments.index'),
            'action_text' => 'Make Payment',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Payment Reminder - ' . config('app.name'))
            ->greeting('Hello!')
            ->line('This is a reminder that a payment is due for ' . $this->student->full_name . '.')
            ->line('Amount: GH₵' . number_format($this->amount, 2));

        if ($this->dueDate) {
            $message->line('Due Date: ' . $this->dueDate);
        }

        $message->action('Make Payment Now', route('payments.index'))
            ->line('Thank you for using our service!');

        return $message;
    }
}
