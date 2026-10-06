<?php

namespace App\Notifications;

use App\Models\MemoApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemoApprovalRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public function __construct(public MemoApproval $approval)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        $approval = $this->approval->fresh(['memo']);
        return $approval && $approval->action === 'pending' && $approval->memo?->status === 'pending';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $memo = $this->approval->memo;

        return (new MailMessage)
            ->subject('Approval requested: '.$memo->memo_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A memo is ready for your approval.')
            ->line('Memo: '.$memo->memo_number)
            ->line('Subject: '.$memo->subject)
            ->line('Prepared by: '.$memo->creator?->name)
            ->action('Review memo', route('approvals.review', $this->approval))
            ->line('Sign in to review the memo and record your decision.');
    }
}
