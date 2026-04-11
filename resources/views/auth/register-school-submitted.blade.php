<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Submitted — SchoolFeed</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 0; background:#faf8f3; color:#111; }
        .container { max-width: 760px; margin: 0 auto; padding: 40px 16px; }
        .card { background:#fff; border:1px solid #e8e5de; border-radius: 12px; padding: 28px; box-shadow: 0 6px 24px rgba(0,0,0,0.05); text-align:center; }
        h1 { font-size: 26px; margin: 0 0 10px; }
        p { color:#4b4b4b; margin: 0 0 8px; }
        .pill { display:inline-block; padding:6px 12px; border-radius:999px; background:#111; color:#fff; font-weight:600; font-size:13px; }
        a.btn { display:inline-block; margin-top:16px; padding:12px 18px; border-radius:10px; background:#111; color:#fff; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <div class="container">
        <div style="margin-bottom:16px;">
            <a href="/" style="text-decoration:none; color:#111; font-weight:700;">← Back to Home</a>
        </div>
        <div class="card">
            <h1>Registration submitted successfully</h1>
            <p>Thank you. Your school account has been submitted and is awaiting approval by a Super Admin.</p>
            @if(session('success'))
                <p class="pill">{{ session('success') }}</p>
            @endif
            @if(!empty($slug))
                <p>When your account is approved, you will be able to log in at:</p>
                <p><strong>{{ url('/' . $slug . '/login') }}</strong></p>
            @endif
            <p>We will notify you at the email you provided once your school is approved.</p>
            <a class="btn" href="/">Return to homepage</a>
        </div>
    </div>
</body>
</html>
