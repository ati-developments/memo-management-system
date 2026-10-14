<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $approved ? 'Memo approved' : 'Your approval is requested' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f5f7; color:#243448; font-family:Arial,Helvetica,sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; mso-hide:all;">{{ $approved ? 'Your memo has been approved.' : 'A memo is ready for your review.' }} {{ $memo->memo_number }}</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f2f5f7;">
        <tr><td align="center" style="padding:32px 16px;">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%; max-width:600px; background-color:#ffffff; border:1px solid #dfe6eb; border-top:5px solid #167451; border-radius:10px;">
                <tr><td style="padding:32px 28px 16px;">
                    <p style="margin:0 0 18px; font-size:12px; font-weight:bold; letter-spacing:1px; color:#167451;">AMANA TAKAFUL PLC</p>
                    <span style="display:inline-block; padding:6px 10px; border-radius:4px; font-size:12px; font-weight:bold; color:{{ $approved ? '#176244' : '#825609' }}; background-color:{{ $approved ? '#e8f5ed' : '#fff3d9' }};">{{ $approved ? 'APPROVED' : 'AWAITING YOUR APPROVAL' }}</span>
                    <p style="margin:0 0 10px; font-size:15px; line-height:1.6;">Hello {{ $recipientName }},</p>
                    <h1 style="margin:18px 0 16px; font-size:24px; line-height:1.3; color:#203448;">{{ $approved ? 'Your memo is approved' : 'A memo needs your review' }}</h1>
                    
                    <p style="margin:0; font-size:15px; line-height:1.6; color:#586777;">{{ $approved ? 'The approval process is complete. You can now view the approved memo as a PDF.' : 'Please review the memo below and record your approval decision.' }}</p>
                </td></tr>
                <tr><td style="padding:8px 28px 24px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f5f8fa; border:1px solid #e2e9ee; border-radius:6px;">
                        <tr><td style="padding:18px 20px 6px; font-size:11px; font-weight:bold; color:#657587;">MEMO NUMBER</td></tr>
                        <tr><td style="padding:0 20px 16px; font-size:15px; font-weight:bold; overflow-wrap:anywhere;">{{ $memo->memo_number }}</td></tr>
                        <tr><td style="padding:0 20px 6px; font-size:11px; font-weight:bold; color:#657587;">SUBJECT</td></tr>
                        <tr><td style="padding:0 20px 18px; font-size:16px; line-height:1.5; overflow-wrap:anywhere;">{{ $memo->subject }}</td></tr>
                        @if(!$approved && $memo->creator)
                            <tr><td style="padding:0 20px 6px; font-size:11px; font-weight:bold; color:#657587;">PREPARED BY</td></tr>
                            <tr><td style="padding:0 20px 18px; font-size:15px;">{{ $memo->creator->name }}</td></tr>
                        @endif
                    </table>
                </td></tr>
                <tr><td style="padding:0 28px 28px;">
                    <table role="presentation" cellspacing="0" cellpadding="0"><tr><td bgcolor="#167451" style="border-radius:6px; mso-padding-alt:10px 14px;">
                        <a href="{{ $actionUrl }}" style="display:inline-block; padding:14px 24px; border:1px solid #167451; border-radius:6px; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none;">{{ $approved ? 'View approved memo PDF' : 'Review Memo' }}</a>
                    </td></tr></table>
                    <p style="margin:14px 0 26px; font-size:12px; line-height:1.6; color:#657587;">{{ $approved ? 'Sign in to access your approved document.' : 'Sign in with your assigned approver account to review this memo.' }}</p>
                    <p style="margin:0; font-size:14px; line-height:1.7;">Regards,<br><strong>Amana Takaful PLC</strong></p>
                </td></tr>
                <!-- <tr><td style="padding:20px 28px; border-top:1px solid #e2e9ee; font-size:12px; line-height:1.6; color:#657587;">
                    If the button does not work, copy and paste this link into your browser:<br>
                    <a href="{{ $actionUrl }}" style="color:#167451; word-break:break-all; overflow-wrap:anywhere;">{{ $actionUrl }}</a>
                </td></tr> -->
            </table>
            <p style="margin:20px 0 0; font-size:11px; color:#657587;">Memo Management System &middot; Amana Takaful PLC</p>
        </td></tr>
    </table>
</body>
</html>
