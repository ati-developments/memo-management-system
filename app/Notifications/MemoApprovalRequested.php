<?php

namespace App\Notifications;

use App\Models\MemoApproval;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemoApprovalRequested extends Notification
{
    public function __construct(public MemoApproval $approval)
    {
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
            ->view(['emails.memo', 'emails.memo-text'], [
                'memo' => $memo,
                'approved' => false,
                'recipientName' => $notifiable->name,
            ])
            ->subject('Approval requested: '.$memo->memo_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A memo is ready for your approval.')
            ->line('Memo: '.$memo->memo_number)
            ->line('Subject: '.$memo->subject)
            ->line('Prepared by: '.$memo->creator?->name)
            ->action('Review memo', route('approvals.review', $this->approval))
            ->line('Sign in to review the memo and record your decision.')
            ->salutation('Regards, Amana Takaful PLC');
    }
}
