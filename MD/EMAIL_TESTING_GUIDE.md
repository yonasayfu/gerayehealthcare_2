# 📧 Email Testing Guide

This guide explains how to test email features (verification, password reset) with your real email address in both development and production environments.

## 📋 Table of Contents
1. [Gmail SMTP Configuration](#gmail-smtp-configuration)
2. [Other Email Providers](#other-email-providers)
3. [Testing in Development](#testing-in-development)
4. [Testing in Production](#testing-in-production)
5. [Verifying Email Functionality](#verifying-email-functionality)
6. [Troubleshooting](#troubleshooting)

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
5. Select your device (or choose "Other" and name it "Laravel")
6. Click **Generate**
7. Copy the 16-character password (without spaces)

### .env Configuration
Update your `.env` file with these settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yonasayfu28@gmail.com
MAIL_PASSWORD=your_16_character_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yonasayfu28@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Example Configuration
If your app password is `abcd efgh ijkl mnop`, configure it as:

```env
MAIL_PASSWORD=abcdefghijklmnop
```

## 📨 Other Email Providers

### Mailgun
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Amazon SES
```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your_ses_access_key
MAIL_PASSWORD=your_ses_secret_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🔧 Testing in Development

### 1. Configure Environment
Update your `.env` file with real mail settings:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8001

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yonasayfu28@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yonasayfu28@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Clear Configuration Cache
```bash
php artisan config:clear
```

### 3. Start Development Server
```bash
php artisan serve
```

### 4. Test Email Features
1. Visit `/register` and create a new account with your email
2. Check your email for the verification message
3. Visit `/forgot-password` to test password reset
4. Check your email for the reset link

## 🏭 Testing in Production

### 1. Configure Environment
Update your `.env` file for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yonasayfu28@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yonasayfu28@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Clear and Cache Configuration
```bash
php artisan config:clear
php artisan config:cache
```

### 3. Restart Services
```bash
# If using supervisor or similar process manager
sudo supervisorctl restart all

# Or restart your web server
sudo systemctl restart nginx
# or
sudo systemctl restart apache2
```

## ✅ Verifying Email Functionality

### Email Verification Test
1. Register a new user account
2. You should be redirected to the email verification notice page
3. Check your email for a verification message
4. Click the verification link in the email
5. You should be redirected to the dashboard with a success message

### Password Reset Test
1. Visit `/forgot-password`
2. Enter your email address
3. Check your email for a password reset message
4. Click the reset link in the email
5. Enter and confirm a new password
6. You should be redirected to the login page with a success message

### Checking Email Logs (Alternative Method)
If you prefer to use the log mailer for development:

```env
MAIL_MAILER=log
```

Then check the log file:
```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
```
[2025-08-28 15:30:45] local.DEBUG: Message-ID: <...>
To: yonasayfu28@gmail.com
Subject: Verify Email Address
```

## 🛠️ Troubleshooting

### Common Issues

#### 1. Authentication Failed
```
Swift_TransportException: Expected response code 250 but got code "535"
```
**Solution**: Check that you're using an App Password, not your regular Gmail password.

#### 2. Connection Refused
```
Connection could not be established with host smtp.gmail.com
```
**Solution**: Check your firewall settings and ensure port 587 is not blocked.

#### 3. Encryption Error
```
SSL operation failed with code 1
```
**Solution**: Try changing `MAIL_ENCRYPTION=tls` to `MAIL_ENCRYPTION=ssl` and `MAIL_PORT=465`.

#### 4. Emails Not Arriving
**Solutions**:
- Check your spam/junk folder
- Verify the MAIL_FROM_ADDRESS is correct
- Wait a few minutes (some providers have delays)

### Debugging Steps

1. **Check Configuration**
   ```bash
   php artisan tinker
   >>> config('mail')
   ```

2. **Test Mail Configuration**
   ```bash
   php artisan tinker
   >>> Mail::raw('Test email', function ($message) {
   >>>     $message->to('yonasayfu28@gmail.com')->subject('Test');
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

### Testing with Laravel Artisan Commands

You can also test email functionality using artisan commands:

```bash
# Test mail configuration
php artisan tinker
>>> Mail::raw('This is a test email', function ($message) {
>>>     $message->to('yonasayfu28@gmail.com')->subject('Test Email');
>>> });
```

This guide should help you successfully test email verification and password reset features with your real email address.