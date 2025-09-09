# 🔐 Authentication System Deployment Guide

This guide provides detailed instructions for deploying the authentication system in production environments, covering Redis configuration, email setup, security considerations, and performance optimization.

## 🛠️ Redis Configuration for Production

### 1. Redis Server Setup
```bash
# Install Redis server
sudo apt-get update
sudo apt-get install redis-server

# Configure Redis for production
sudo nano /etc/redis/redis.conf
```

Key Redis configuration settings for production:
```
# Bind to localhost only for security
bind 127.0.0.1

# Enable persistence
save 900 1
save 300 10
save 60 10000

# Set memory policy
maxmemory-policy allkeys-lru

# Enable AOF (Append Only File)
appendonly yes

# Set password for Redis
requirepass your_redis_password_here
```

### 2. Laravel Redis Configuration
Update your `.env` file:
```env
# Redis Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password_here
REDIS_PORT=6379

# Cache Configuration
CACHE_STORE=redis
CACHE_PREFIX=your_app_name_cache_

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

### 3. Redis Connection Pooling
Configure connection pooling in `config/database.php`:
```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        'persistent' => env('REDIS_PERSISTENT', true), // Enable persistent connections
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
],
```

## 📧 Email Configuration for Production

### 1. SMTP Configuration
Update your `.env` file with production SMTP settings:
```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_email@your-domain.com
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Supported Email Providers

#### Gmail
1. Enable 2-Factor Authentication
2. Generate an App Password (16 characters)
3. Use the App Password in MAIL_PASSWORD (not your regular Gmail password)

#### Amazon SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your_mailgun_key
```

### 3. Email Verification and Password Reset
Ensure your `config/fortify.php` has the correct features enabled:
```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

## 🔒 Security Considerations

### 1. Rate Limiting
Configure rate limiting in `App\Providers\RouteServiceProvider`:
```php
protected function configureRateLimiting()
{
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->email.$request->ip());
    });

    RateLimiter::for('password-reset', function (Request $request) {
        return Limit::perMinute(3)->by($request->email.$request->ip());
    });

    RateLimiter::for('two-factor', function (Request $request) {
        return Limit::perMinute(5)->by($request->session()->get('login.id'));
    });
}
```

### 2. Password Security
Configure password requirements in `App\Actions\Fortify\PasswordValidationRules`:
```php
public function __construct()
{
    $this->passwordRules = ['required', 'string', new Password, 'confirmed'];
}
```

### 3. Session Security
Configure session settings in `config/session.php`:
```php
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true, // Prevent XSS
'same_site' => 'lax', // CSRF protection
```

## ⚡ Performance Optimization

### 1. Caching Strategy
Our authentication system uses Redis for caching. Configure cache TTL in services:
```php
// In app/Services/PerformanceOptimizedBaseService.php
protected int $cacheTtl = 300; // 5 minutes

// For dropdowns and frequently accessed data
const CACHE_TTL = 600; // 10 minutes
```

### 2. Database Indexing
Ensure proper indexing on the users table:
```php
// In migration file
Schema::table('users', function (Blueprint $table) {
    $table->index(['email']);
    $table->index(['remember_token']);
    $table->index(['email_verified_at']);
});
```

### 3. Queue Configuration
Configure queues for email sending in `.env`:
```env
QUEUE_CONNECTION=redis
REDIS_QUEUE=default
```

Start queue workers:
```bash
php artisan queue:work --queue=default,emails --sleep=3 --tries=3
```

## 🔄 Environment-Specific Configuration

### 1. Development Environment
```env
APP_ENV=local
APP_DEBUG=true
MAIL_MAILER=log # For development email testing
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=sync # Immediate processing
```

### 2. Staging Environment
```env
APP_ENV=staging
APP_DEBUG=false
MAIL_MAILER=smtp # Real email testing
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Production Environment
```env
APP_ENV=production
APP_DEBUG=false
MAIL_MAILER=smtp # Production email provider
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 🧪 Testing Authentication in Production

### 1. Pre-Deployment Checklist
- [ ] Verify Redis connection and authentication
- [ ] Test email configuration with real credentials
- [ ] Confirm SSL/TLS certificates are properly configured
- [ ] Verify rate limiting is working correctly
- [ ] Test all authentication flows (registration, login, password reset, email verification)
- [ ] Check session persistence across requests
- [ ] Validate password strength requirements
- [ ] Test two-factor authentication if enabled

### 2. Post-Deployment Monitoring
- [ ] Monitor authentication logs for failed attempts
- [ ] Track email delivery success rates
- [ ] Monitor Redis memory usage and performance
- [ ] Watch for rate limiting triggers
- [ ] Check session expiration and cleanup

## 🚨 Troubleshooting Common Issues

### 1. Redis Connection Issues
```bash
# Check if Redis is running
sudo systemctl status redis

# Test Redis connection
redis-cli ping

# Check Redis configuration
redis-cli config get requirepass
```

### 2. Email Delivery Problems
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test email sending via Tinker
php artisan tinker
>>> Mail::raw('Test email', function ($message) { $message->to('test@example.com')->subject('Test'); });
```

### 3. Session Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check Redis for session data
redis-cli
> SELECT 0
> KEYS laravel_database_laravel_cache:*
```

## 📊 Performance Monitoring

### 1. Redis Monitoring
```bash
# Monitor Redis performance
redis-cli info
redis-cli info stats
redis-cli info memory
redis-cli info clients
```

### 2. Laravel Telescope
Enable Telescope for authentication monitoring:
```php
// In config/telescope.php
'watchers' => [
    Watchers\CacheWatcher::class => true,
    Watchers\RequestWatcher::class => true,
    Watchers\QueryWatcher::class => true,
    Watchers\JobWatcher::class => true,
],
```

## 🛡️ Best Practices

### 1. Security Best Practices
- Never commit sensitive credentials to version control
- Use environment variables for all configuration
- Regularly rotate passwords and API keys
- Implement proper input validation and sanitization
- Use HTTPS for all authentication flows
- Regularly update dependencies and apply security patches

### 2. Performance Best Practices
- Use Redis for session and cache storage
- Implement proper database indexing
- Use queues for email sending and other background tasks
- Monitor and optimize database queries
- Implement appropriate rate limiting

### 3. Maintenance Best Practices
- Regularly backup Redis data
- Monitor authentication logs for suspicious activity
- Keep documentation up to date
- Test deployment procedures regularly
- Have a rollback plan for critical updates

This guide ensures your authentication system is properly configured, secure, and performant in production environments.