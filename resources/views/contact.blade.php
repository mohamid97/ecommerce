<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; color: #333; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        h2 { color: #2c3e50; }
        .info { margin-bottom: 20px; }
        .info p { margin: 5px 0; }
        .message-box { background: #f3f4f6; padding: 15px; border-radius: 6px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
        a.button { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px; }
        a.button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📬 New Contact Form Submission</h2>

        <div class="info">
            <p><strong>Name:</strong> {{ $name }}</p>
            <p><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></p>
            <p><strong>Phone:</strong> {{ $phone ?? 'N/A' }}</p>
            <p><strong>Subject:</strong> {{ $subject }}</p>
        </div>

        <div class="message-box">
            <strong>Message:</strong>
            <p>{{ $content }}</p>
        </div>

        <a href="mailto:{{ $email }}" class="button">Reply to {{ $name }}</a>

        <div class="footer">
            <p>— {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
