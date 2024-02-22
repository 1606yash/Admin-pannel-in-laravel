<!DOCTYPE html>
<html>
<head>
    <title>Parivar Portal</title>
</head>
<body>
    <p>Hello {{ $data['first_name'] }},</p>

    @if (isset($data['mail_type']) && $data['mail_type'] == 'user-rejected')
        @if (isset($data['user_type']) && $data['user_type'] == 'user')
            <p>Your profile have been rejected by the administrator.</p>
            <p>Due to this reject reason - {{ $data['reject_reason'] }},</p>
        @endif

        @if (isset($data['user_type']) && $data['user_type'] == 'admin_user')
            <p>You have rejected this user profile. User Email is - {{ $data['user_email'] }}</p>
            <p>Due to this reject reason - {{ $data['reject_reason'] }},</p>
        @endif

        @if (isset($data['user_type']) && $data['user_type'] == 'superior_user')
            <p>This user profile is rejected by the administrator. User Email is - {{ $data['user_email'] }}</p>
            <p>Due to this reject reason - {{ $data['reject_reason'] }},</p>
        @endif
    @endif

    @if (isset($data['mail_type']) && $data['mail_type'] == 'user-approved')
        @if (isset($data['user_type']) && $data['user_type'] == 'user')
            <p>Your profile have been approved by the administrator.
                <a href="{{ $data['login_link'] }}">Login</a>    
            </p> 
        @endif
        @if (isset($data['user_type']) && $data['user_type'] == 'admin_user')
            <p>You have approved this user profile. User Email is - {{ $data['user_email'] }}</p>
        @endif
        @if (isset($data['user_type']) && $data['user_type'] == 'superior_user')
            <p>This user profile is approved by the administrator. User Email is - {{ $data['user_email'] }}</p>
        @endif
    @endif

    <p>Thank you!</p>
</body>
</html>
