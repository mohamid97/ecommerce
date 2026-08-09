<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Verify your email address</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; color:#1f2937; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        Your {{ config('app.name') }} verification code is {{ $otp }}. It expires in 5 minutes.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#f4f7fb;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:560px; background:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px 20px; background:#0f766e; color:#ffffff; text-align:center;">
                            <p style="margin:0; font-size:22px; font-weight:700;">{{ config('app.name') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 16px; color:#111827; font-size:24px; line-height:32px; font-weight:700;">Verify your email address</h1>
                            <p style="margin:0 0 24px; font-size:16px; line-height:24px;">Use this verification code to complete your request:</p>

                            <div style="margin:0 0 24px; padding:18px; border:1px solid #99f6e4; border-radius:8px; background:#f0fdfa; color:#115e59; font-size:30px; font-weight:700; letter-spacing:8px; line-height:36px; text-align:center;">{{ $otp }}</div>

                            <p style="margin:0 0 12px; font-size:15px; line-height:22px;">This code expires in <strong>5 minutes</strong>.</p>
                            <p style="margin:0; color:#6b7280; font-size:14px; line-height:21px;">If you did not request this code, you can safely ignore this email. Do not share this code with anyone.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; border-top:1px solid #e5e7eb; color:#6b7280; font-size:12px; line-height:18px; text-align:center;">
                            This is an automated security message from {{ config('app.name') }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
