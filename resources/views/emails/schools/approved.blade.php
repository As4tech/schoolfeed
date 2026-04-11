<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>School Approved</title></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #E8300A;">Welcome to SchoolFeed!</h2>
    <p>Hi {{ $school->name }},</p>
    <p>Great news! Your SchoolFeed account has been approved and is now active.</p>
    
    @if($adminUser)
    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="margin-top: 0;">Your Administrator Login Details:</h3>
        <p><strong>Login URL:</strong> <a href="{{ url('/' . $school->slug . '/login') }}">{{ url('/' . $school->slug . '/login') }}</a></p>
        <p><strong>Email:</strong> {{ $adminUser->email }}</p>
        <p><strong>Password:</strong> The password you created during registration</p>
    </div>
    @else
    <p>You can now log in at: <a href="{{ url('/' . $school->slug . '/login') }}">{{ url('/' . $school->slug . '/login') }}</a></p>
    @endif
    
    <p>What's next?</p>
    <ul>
        <li>Log in to your dashboard</li>
        <li>Add your students and staff</li>
        <li>Set up your meal plans and pricing</li>
        <li>Start managing your school feeding program</li>
    </ul>
    
    <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
    
    <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
    <p style="color: #666; font-size: 14px;">Best regards,<br>The SchoolFeed Team</p>
</body>
</html>
