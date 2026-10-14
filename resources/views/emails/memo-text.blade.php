AMANA TAKAFUL PLC

{{ $approved ? 'Your memo is approved' : 'A memo needs your review' }}

Hello {{ $recipientName }},

{{ $approved ? 'The approval process is complete. View the approved memo as a PDF using the link below.' : 'Please review the memo below and record your approval decision.' }}

Memo: {{ $memo->memo_number }}
Subject: {{ $memo->subject }}
@if(!$approved && $memo->creator)
Prepared by: {{ $memo->creator->name }}
@endif

{{ $approved ? 'View approved memo PDF' : 'Review memo' }}: {{ $actionUrl }}

Sign in to access the memo.

Regards,
Amana Takaful PLC
