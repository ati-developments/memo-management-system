<?php

namespace App\Notifications;

use App\Models\Memo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemoApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $memoId)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return Memo::query()
            ->whereKey($this->memoId)
            ->where('status', 'approved')
            ->whereHas('approvals', fn ($query) => $query
                ->where('approval_role', 'prepared')
                ->where('approver_id', $notifiable->getKey()))
            ->exists();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $memo = Memo::findOrFail($this->memoId);

        return (new MailMessage)
            ->subject('Memo approved: '.$memo->memo_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your memo has been approved.')
            ->line('Memo: '.$memo->memo_number)
            ->line('Subject: '.$memo->subject)
            ->action('View approved memo', route('memos.show', $memo))
            ->salutation('Regards, Amana Takaful PLC');
    }
}
