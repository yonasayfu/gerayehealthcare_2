# 📧 Prescription Email Configuration Guide

This guide explains how to configure email settings for prescription sharing in both development and production environments to ensure patients can easily receive their prescriptions.

## 📋 Table of Contents
1. [Development Environment Setup](#development-environment-setup)
2. [Production Environment Configuration](#production-environment-configuration)
3. [Gmail SMTP Configuration](#gmail-smtp-configuration)
4. [Other Email Providers](#other-email-providers)
5. [Testing Email Functionality](#testing-email-functionality)
6. [Troubleshooting Common Issues](#troubleshooting-common-issues)

## 🛠️ Development Environment Setup

In local development, emails are configured to use the `log` mailer by default, which writes emails to the log file instead of sending them.

### Current Configuration (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="no-reply@geraye.local"
MAIL_FROM_NAME="Geraye Healthcare"
```

### Viewing Development Emails
During development, emails are logged to `storage/logs/laravel.log`. You can monitor them with:
```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
```
[2025-09-19 15:30:45] local.DEBUG: Message-ID: <...>
To: patient@example.com
Subject: Prescription for John Doe from Dr. Smith
```

## 🚀 Production Environment Configuration

For production, you'll need to configure a real email service. The most common options are:

### Option 1: Gmail SMTP (Easiest for Small Scale)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Geraye Healthcare"
```

### Option 2: SendGrid (Recommended for Scale)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@domain.com
MAIL_FROM_NAME="Geraye Healthcare"
```

### Option 3: Mailgun
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_smtp_username
MAIL_PASSWORD=your_mailgun_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@domain.com
MAIL_FROM_NAME="Geraye Healthcare"
```

## 📧 Gmail SMTP Configuration

### Prerequisites
1. Enable 2-Factor Authentication on your Google account
2. Generate an App Password for Gmail

### Steps to Generate App Password
1. Go to your [Google Account](https://myaccount.google.com/)
2. Navigate to **Security** > **2-Step Verification** > **App passwords**
3. If you don't see "App passwords", you may need to:
   - Enable 2-Step Verification first
   - Or go directly to: https://myaccount.google.com/apppasswords
4. Select **Mail** as the app
5. Select your device (or choose "Other" and name it "Geraye Healthcare")
6. Click **Generate**
7. Copy the 16-character password (without spaces)

### .env Configuration for Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_16_character_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Geraye Healthcare"
```

### Example Configuration
If your app password is `abcd efgh ijkl mnop`, configure it as:
```env
MAIL_PASSWORD=abcdefghijklmnop
```

## 🌐 Other Email Providers

### SendGrid Configuration
1. Sign up at [SendGrid](https://sendgrid.com/)
2. Create an API key
3. Verify your sender identity
4. Configure .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=verified@yourdomain.com
MAIL_FROM_NAME="Geraye Healthcare"
```

### Mailgun Configuration
1. Sign up at [Mailgun](https://www.mailgun.com/)
2. Add your domain
3. Get SMTP credentials
4. Configure .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_smtp_username
MAIL_PASSWORD=your_mailgun_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=verified@yourdomain.com
MAIL_FROM_NAME="Geraye Healthcare"
```

## ✅ Testing Email Functionality

### Testing with Laravel Artisan Commands
You can test email functionality using artisan commands:

```bash
# Test mail configuration
php artisan tinker
>>> Mail::send('emails.prescription-share', [
...     'shareUrl' => 'https://example.com/prescription/abc123',
...     'patientName' => 'John Doe',
...     'doctorName' => 'Dr. Smith',
...     'prescribedDate' => 'September 19, 2025',
...     'status' => 'final',
...     'messageText' => 'Please take as directed.'
... ], function ($message) {
...     $message->to('test@example.com')->subject('Test Prescription Email');
... });
```

### Testing Prescription Email Sharing
1. Log in to the admin panel
2. Navigate to Prescriptions
3. View a prescription
4. Click "Share" > "Email"
5. Enter a test email address
6. Send the email
7. Check the recipient's inbox

## 🔧 Troubleshooting Common Issues

### Common Issues and Solutions

#### 1. Authentication Failed
```
Swift_TransportException: Expected response code 250 but got code "535"
```
**Solution**: Check that you're using an App Password (for Gmail), not your regular password.

#### 2. Connection Refused
```
Connection could not be established with host smtp.gmail.com
```
**Solution**: Check your firewall settings and ensure the SMTP port is not blocked.

#### 3. Encryption Error
```
SSL operation failed with code 1
```
**Solution**: Try changing `MAIL_ENCRYPTION=tls` to `MAIL_ENCRYPTION=ssl` and adjust the port accordingly:
- For TLS: `MAIL_PORT=587`
- For SSL: `MAIL_PORT=465`

#### 4. Emails Not Arriving
**Solutions**:
- Check your spam/junk folder
- Verify the MAIL_FROM_ADDRESS is verified with your email provider
- Wait a few minutes (some providers have delays)
- Check that your domain has proper SPF/DKIM records

#### 5. Gmail "Less Secure Apps" Error
**Solution**: Gmail no longer supports "less secure app access". You must use App Passwords as described above.

### Debugging Steps

1. **Check Configuration**
   ```bash
   php artisan tinker
   >>> config('mail')
   ```

2. **Test Mail Configuration**
   ```bash
   php artisan tinker
   >>> Mail::raw('Test email from Geraye Healthcare', function ($message) {
   >>>     $message->to('test@example.com')->subject('Test Email');
   >>> });
   ```

3. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Clear Caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

### Production Best Practices

1. **Use Environment Variables**: Never hardcode credentials in your source code
2. **Verify Sender Addresses**: Ensure your MAIL_FROM_ADDRESS is verified with your email provider
3. **Monitor Email Deliverability**: Use services like SendGrid or Mailgun that provide delivery analytics
4. **Implement Rate Limiting**: Prevent abuse of the email sharing feature
5. **Use Queues**: Configure Laravel queues for sending emails asynchronously
6. **Set Up Webhooks**: Monitor email delivery status with provider webhooks

### Security Considerations

1. **App Passwords**: Always use app-specific passwords rather than account passwords
2. **Environment Files**: Never commit .env files to version control
3. **Rate Limiting**: Implement rate limits on email sending endpoints
4. **Input Validation**: Validate all email inputs to prevent abuse
5. **Logging**: Log email sending attempts for security monitoring

This configuration guide ensures that patients will receive professional, well-formatted prescription emails that are easy to understand and act upon.