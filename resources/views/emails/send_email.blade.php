<!DOCTYPE html>
<html>
<head>
    <title>Parivar Portal</title>
</head>
<body>
    <p>Hello {{ $data['first_name'] }},</p>
    @if (isset($data['mail_type']) && $data['mail_type'] == 'forgot-password')
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>
        If you requested a password reset, please click the following link to reset your password:
        <a href="{{ $data['link'] }}">Reset Password</a>
    </p>
    <p>If you did not request a password reset, no further action is required.</p>
    @endif
    @if (isset($data['mail_type']) && $data['mail_type'] == 'user-rejected')
    <p>You have been rejected by the administrator.</p>
    <p>Due to this reject reason - {{ $data['reject_reason'] }},</p>
    @endif
    @if (isset($data['mail_type']) && $data['mail_type'] == 'user-approved')
    <p> You have been approved by the administrator. </p>
    @endif
    <p>Thank you!</p>
</body>
</html>