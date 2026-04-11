<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register School — SchoolFeed</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 0; background:#faf8f3; color:#111; }
        .container { max-width: 900px; margin: 0 auto; padding: 32px 16px; }
        .card { background:#fff; border:1px solid #e8e5de; border-radius: 12px; padding: 24px; box-shadow: 0 6px 24px rgba(0,0,0,0.05); }
        h1 { font-size: 28px; margin: 0 0 8px; }
        p.lead { color:#5b5b5b; margin:0 0 20px; }
        form { display:grid; gap:14px; }
        label { font-weight:600; font-size:14px; }
        input, textarea { width:100%; padding:12px 12px; border:1px solid #e3e0d9; border-radius:8px; background:#fdfcf9; font-size:14px; }
        .row { display:grid; gap:12px; grid-template-columns: 1fr 1fr; }
        .actions { display:flex; gap:10px; margin-top: 8px; }
        .btn { appearance:none; border:none; border-radius:10px; padding:12px 18px; font-weight:600; cursor:pointer; }
        .btn-primary { background:#E8300A; color:#fff; }
        .btn-secondary { background:#111; color:#fff; }
        .help { font-size:12px; color:#777; }
        @media (max-width: 720px) { .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <a href="/" style="text-decoration:none; color:#111; font-weight:700;">← Back to Home</a>
            <a href="https://wa.me/2348012345678?text=Hello%20SchoolFeed%20Team%2C%20I%27d%20like%20to%20register%20our%20school." target="_blank" style="text-decoration:none; color:#16a34a; font-weight:600;">Need help? WhatsApp</a>
        </div>
        <div class="card">
            <h1>Register your school</h1>
            <p class="lead">Fill this short form and our Super Admin will review and approve your account. You’ll receive your school URL at <code>{your-slug}</code>.</p>
            <form method="post" action="{{ route('schools.register') }}">
                @csrf
                <div class="row">
                    <div>
                        <label>School name</label>
                        <input type="text" name="name" placeholder="e.g., The Citadel School" required>
                    </div>
                    <div>
                        <label>School email</label>
                        <input type="email" name="email" placeholder="contact@school.edu" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label>Phone</label>
                        <input type="text" name="phone" placeholder="0801 234 5678">
                    </div>
                    <div>
                        <label>Preferred slug</label>
                        <input type="text" name="slug" placeholder="the-citadel-school">
                        <div class="help">We’ll try to use this; if taken, we’ll suggest an alternative.</div>
                    </div>
                </div>
                <div>
                    <label>Address</label>
                    <textarea name="address" rows="3" placeholder="Campus address"></textarea>
                </div>
                
                <hr style="margin: 20px 0; border: none; border-top: 1px solid #e8e5de;">
                <h3 style="font-size: 18px; margin: 0 0 16px;">School Administrator Account</h3>
                <p style="color: #5b5b5b; font-size: 14px; margin: 0 0 16px;">Create the main administrator account for your school. This account will be activated once your school is approved.</p>
                
                <div class="row">
                    <div>
                        <label>Admin name</label>
                        <input type="text" name="admin_name" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label>Admin email</label>
                        <input type="email" name="admin_email" placeholder="admin@school.edu" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label>Admin password</label>
                        <input type="password" name="admin_password" placeholder="Create a strong password" required minlength="8">
                    </div>
                    <div>
                        <label>Confirm password</label>
                        <input type="password" name="admin_password_confirmation" placeholder="Re-enter password" required minlength="8">
                    </div>
                </div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Submit registration</button>
                    <a class="btn btn-secondary" href="/">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
