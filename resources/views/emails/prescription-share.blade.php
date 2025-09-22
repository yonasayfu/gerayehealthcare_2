<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Prescription from Geraye Healthcare</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f8fafc; color: #0f172a;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 30px 20px; text-align: center;">
                <h1 style="color: white; margin: 0; font-size: 24px; font-weight: 600;">Geraye Home Care Services</h1>
                <p style="color: #e0f2fe; margin: 8px 0 0; font-size: 16px;">Your Prescription is Ready</p>
            </div>

            <!-- Content -->
            <div style="padding: 30px;">
                <h2 style="color: #0f172a; margin: 0 0 20px; font-size: 20px; font-weight: 600;">Hello,</h2>
                
                <p style="color: #334155; margin: 0 0 20px; line-height: 1.6; font-size: 16px;">
                    You have received a prescription from <strong>{{ $doctorName }}</strong> at Geraye Home Care Services.
                </p>
                
                @if($messageText)
                <div style="background-color: #f1f5f9; border-left: 4px solid #0ea5e9; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 0; color: #0f172a; font-weight: 500;">Message from doctor:</p>
                    <p style="margin: 8px 0 0; color: #334155; line-height: 1.5;">{{ $messageText }}</p>
                </div>
                @endif

                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <h3 style="margin: 0 0 15px; color: #0f172a; font-size: 18px;">Prescription Details</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <p style="margin: 0 0 5px; color: #64748b; font-size: 14px;">Patient</p>
                            <p style="margin: 0; color: #0f172a; font-weight: 500;">{{ $patientName }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 5px; color: #64748b; font-size: 14px;">Date</p>
                            <p style="margin: 0; color: #0f172a; font-weight: 500;">{{ $prescribedDate }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 5px; color: #64748b; font-size: 14px;">Doctor</p>
                            <p style="margin: 0; color: #0f172a; font-weight: 500;">{{ $doctorName }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 5px; color: #64748b; font-size: 14px;">Status</p>
                            <p style="margin: 0; color: #0f172a; font-weight: 500; text-transform: capitalize;">{{ $status }}</p>
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $shareUrl }}" 
                       style="display: inline-block; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(2, 132, 199, 0.2);">
                        View Prescription
                    </a>
                </div>

                <p style="color: #64748b; margin: 25px 0 0; line-height: 1.6; font-size: 15px;">
                    Click the button above to view your prescription details, download as PDF, or share with others.
                </p>

                <p style="color: #64748b; margin: 15px 0 0; line-height: 1.6; font-size: 15px;">
                    If you're having trouble with the button, copy and paste this link into your browser:<br>
                    <a href="{{ $shareUrl }}" style="color: #0284c7; word-break: break-all;">{{ $shareUrl }}</a>
                </p>
            </div>

            <!-- Footer -->
            <div style="background-color: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0; color: #64748b; font-size: 14px;">
                    &copy; {{ date('Y') }} Geraye Home Care Services. All rights reserved.
                </p>
                <p style="margin: 8px 0 0; color: #94a3b8; font-size: 13px;">
                    This email was sent to you because a prescription was shared with you.
                </p>
            </div>
        </div>
    </div>
</body>
</html>