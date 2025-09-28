# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

This is **Geraye Healthcare**, a comprehensive healthcare management system built with Laravel 11 (PHP backend) and Vue.js 3 with TypeScript (frontend). The system includes patient management, staff scheduling, messaging, prescriptions, inventory, marketing, insurance management, and more.

## Common Development Commands

### Backend (Laravel)
```bash
# Development server
php artisan serve

# Run migrations
php artisan migrate

# Run migrations with fresh database
php artisan migrate:fresh --seed

# Clear application cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear

# Generate application key
php artisan key:generate

# Run background queue worker
php artisan queue:work

# Run tests
php artisan test

# Run single test file
php artisan test tests/Feature/MessageTest.php

# Generate models, controllers, migrations
php artisan make:model ModelName -mcr
php artisan make:controller Api/V1/ControllerName
php artisan make:migration create_table_name
```

### Frontend (Vue.js + Vite)
```bash
# Install dependencies
npm install

# Development server
npm run dev

# Build for production
npm run build

# Run linting
npm run lint

# Format code
npm run format
```

### Testing & Quality
```bash
# Run PHPStan static analysis
vendor/bin/phpstan analyse

# Run PHP CodeSniffer
vendor/bin/phpcs

# Run tests with coverage
php artisan test --coverage
```

## Architecture Overview

### Backend Structure
- **Laravel 12** with API-first approach
- **Database**: PostgreSQL with comprehensive migrations
- **Authentication**: Laravel Sanctum for API tokens
- **Real-time**: Laravel Echo with Pusher for WebSocket broadcasting
- **File Storage**: Laravel filesystem with public disk
- **Permissions**: Spatie Laravel Permission package

### Key Backend Components
- **API Controllers**: All in `app/Http/Controllers/Api/V1/` namespace
- **Services**: Business logic in `app/Services/` (e.g., MessageService)
- **Events**: Real-time broadcasting events in `app/Events/`
- **Models**: Eloquent models with relationships in `app/Models/`
- **Middleware**: Custom middleware for authentication, permissions, last seen tracking

### Frontend Structure
- **Vue.js 3** with Composition API and TypeScript
- **Vite** for fast development and building
- **Inertia.js** for seamless Laravel-Vue integration
- **Pinia** for state management
- **TailwindCSS** for styling
- **Radix Vue** and **Reka UI** for component library

### Messaging System Architecture
The messaging system is a core feature with these components:
- **Direct messaging** between users with file attachments
- **Group messaging** with admin controls
- **Real-time features**: typing indicators, online status, read receipts
- **Voice messages** with duration tracking
- **Message reactions** with emoji support
- **File sharing** with type categorization (image, video, audio, file)
- **Message editing** and forwarding capabilities

### Database Key Relationships
- `User` ↔ `Message` (sender/receiver)
- `Message` ↔ `MessageReaction` (one-to-many)
- `Message` → `Message` (reply_to_id for threading)
- `User` → `Patient` (one-to-one for patient users)
- `User` → `CaregiverAssignment` (many-to-many with patients)

### API Structure
All API endpoints are versioned under `/api/v1/` with:
- **Authentication**: Bearer token via Sanctum
- **Rate limiting**: Different limits per endpoint type
- **Permissions**: Role-based access control
- **Validation**: Form request classes for complex validation
- **Resources**: API resource classes for consistent JSON responses

### Real-time Features
- **Broadcasting**: Uses Laravel Echo with Pusher
- **Private channels**: User-specific channels (user.{id})
- **Events**: `UserTyping`, `MessageRead`, `MessageSent`
- **Frontend**: WebSocket listeners in Vue components

### File Handling
- **Storage**: `storage/app/public/` with symlink to `public/storage/`
- **Upload paths**: 
  - Messages: `messages/attachments/`
  - Voice messages: `messages/voice/`
  - Documents: `documents/`
  - Profile images: `profiles/`

## Development Workflows

### Adding New API Endpoints
1. Create controller method in appropriate `Api/V1/` controller
2. Add route in `routes/api.php` with proper middleware
3. Create Form Request class for validation if needed
4. Update API Resource class for response formatting
5. Add permissions if required

### Adding Real-time Features
1. Create Event class implementing `ShouldBroadcast`
2. Add broadcasting logic in controller/service
3. Update frontend to listen for the event
4. Test with broadcasting enabled

### Database Changes
1. Create migration: `php artisan make:migration description`
2. Update model fillable/casts/relationships
3. Update factory and seeder if applicable
4. Run migration: `php artisan migrate`

### Frontend Component Development
1. Components in `resources/js/Pages/` (Inertia pages) or `resources/js/Components/`
2. Use TypeScript interfaces for type safety
3. Leverage Pinia stores for state management
4. Follow TailwindCSS utility-first approach

## Environment Configuration

### Required Environment Variables
```env
# Database
DB_CONNECTION=pgsql
DB_DATABASE=geraye_healthcare

# Broadcasting
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=us2

# Mail
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@geraye.com

# File Storage
FILESYSTEM_DISK=public
```

### Development Setup
1. Copy `.env.example` to `.env`
2. Run `composer install && npm install`
3. Generate key: `php artisan key:generate`
4. Run migrations: `php artisan migrate --seed`
5. Link storage: `php artisan storage:link`
6. Start servers: `php artisan serve` and `npm run dev`

## Key Code Patterns

### API Response Pattern
```php
// Success with data
return response()->json(['data' => $data]);

// Success with resource
return new ResourceClass($model);

// Error responses use HTTP status codes + message
return response()->json(['message' => 'Error'], 400);
```

### Service Layer Pattern
Business logic lives in service classes:
```php
// In controller
$message = app(MessageService::class)->sendDirectMessage($user, $data);

// Service handles complex logic, database operations, file handling
```

### Real-time Broadcasting Pattern
```php
// In controller/service
broadcast(new EventClass([
    'user_id' => $user->id,
    'data' => $payload
]));
```

### Frontend API Pattern
```typescript
// Use Inertia for page navigation
router.post('/api/v1/messages', data);

// Use direct HTTP for API calls
const response = await fetch('/api/v1/messages/typing', {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` },
  body: JSON.stringify(data)
});
```

## Testing Guidelines

### Backend Testing
- Feature tests in `tests/Feature/` for API endpoints
- Unit tests in `tests/Unit/` for services and utilities
- Use factories for model creation
- Test authentication, permissions, and validation

### Frontend Testing
- Component tests using Vue Test Utils
- E2E tests for critical user flows
- Test real-time features with mock WebSocket

## Security Considerations

- **Authentication**: All API routes require Sanctum authentication
- **Authorization**: Role/permission-based access control
- **File uploads**: Validated file types and sizes
- **Rate limiting**: Applied to all API endpoints
- **SQL injection**: Use Eloquent ORM and parameter binding
- **XSS**: Vue.js templates auto-escape content