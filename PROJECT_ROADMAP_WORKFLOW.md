# 🏥 Geraye Healthcare - Project Roadmap & Workflow

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Technology Stack](#technology-stack)
4. [Features & Capabilities](#features--capabilities)
5. [Module Development Workflow](#module-development-workflow)
6. [Guidelines for New Developers](#guidelines-for-new-developers)
7. [Feature Location Guide](#feature-location-guide)

---

## 🎯 Project Overview

**Geraye Healthcare** is a comprehensive healthcare management system built with modern web technologies, following Clean Architecture principles. The system manages patients, staff, inventory, marketing, insurance claims, and financial operations.

### Core Business Domains

- **Patient Management**: Patient records, visit services, appointments
- **Staff Management**: Staff profiles, availability, payouts, assignments
- **Inventory Management**: Medical equipment, supplies, maintenance, alerts
- **Marketing**: Campaigns, leads, tasks, analytics, partner engagements
- **Financial**: Invoicing, insurance claims, payment reconciliation
- **Administrative**: User management, roles, permissions, reporting

---

## 🏗️ Architecture

### **Clean Architecture Implementation**

```
📁 app/
├── 📁 Http/Controllers/          # Thin controllers (handle HTTP requests only)
│   ├── 📁 Base/                  # BaseController, OptimizedBaseController
│   ├── 📁 Admin/                 # Administrative modules
│   ├── 📁 Staff/                 # Staff-specific controllers
│   ├── 📁 Insurance/             # Insurance-related controllers
│   └── 📁 Api/V1/                # API endpoints
├── 📁 Services/                  # Business logic layer
│   ├── 📁 Base/                  # BaseService, PerformanceOptimizedBaseService
│   ├── 📁 Validation/Rules/      # Custom validation rules
│   └── [Domain]Service.php       # Domain-specific services
├── 📁 DTOs/                      # Data Transfer Objects
│   ├── BaseDTO.php               # Base DTO with object pooling
│   └── [Create|Update]*.php      # Request/Response DTOs
├── 📁 Models/                    # Eloquent models (data layer)
├── 📁 Events/                    # Domain events
├── 📁 Listeners/                 # Event handlers
├── 📁 Providers/                 # Service providers
├── 📁 Policies/                  # Authorization policies
└── 📁 Enums/                     # Type-safe enumerations
```

### **Frontend Architecture (Vue 3 + Inertia.js)**

```
📁 resources/js/
├── 📁 pages/                     # Vue pages (following Laravel routes)
│   ├── 📁 Admin/                 # Administrative interfaces
│   ├── 📁 Staff/                 # Staff dashboards
│   └── 📁 Insurance/             # Insurance management
├── 📁 components/                # Reusable Vue components
│   ├── 📁 ui/                    # UI framework components
│   └── 📁 common/                # Business components
├── 📁 composables/               # Vue composition functions
├── 📁 types/                     # TypeScript type definitions
└── app.ts                        # Application entry point
```

---

## 🛠️ Technology Stack

### **Backend Stack**

- **Framework**: Laravel 12.x (PHP 8.4+)
- **Database**: PostgreSQL with performance indexes
- **Caching**: Redis with Predis client
- **Authentication**: Laravel Sanctum + Spatie Permission
- **File Storage**: Laravel Storage (supports S3)
- **PDF Generation**: Barryvdh/DomPDF
- **API**: RESTful with API versioning

### **Frontend Stack**

- **Framework**: Vue 3 with Composition API
- **Routing**: Inertia.js (SPA-like experience)
- **UI Library**: Custom components + Tailwind CSS
- **Build Tool**: Vite with optimized chunking
- **Type Safety**: TypeScript
- **Icons**: Lucide Vue Next
- **Charts**: Chart.js with Vue-Chartjs

### **DevOps & Tools**

- **Development**: Laravel Sail (Docker)
- **Deployment**: Laravel Cloud ready
- **Performance**: Laravel Debugbar, custom benchmarking
- **Code Quality**: PSR-12 standards, ESLint
- **Version Control**: Git with semantic commits

---

## 🚀 Features & Capabilities

### **🔧 Performance Features**

- ✅ **Multi-layer Caching**: Redis + Laravel cache
- ✅ **Database Optimization**: Strategic indexes on critical tables
- ✅ **Query Optimization**: Eliminated N+1 queries with CachedDropdownService
- ✅ **Frontend Optimization**: Code splitting, lazy loading, optimized bundles
- ✅ **Memory Management**: Object pooling in DTOs, efficient resource usage

### **🔐 Security Features**

- ✅ **Role-Based Access Control**: Spatie Permission with caching
- ✅ **API Authentication**: Sanctum tokens
- ✅ **Input Validation**: Custom validation rules + DTOs
- ✅ **XSS Protection**: Inertia.js built-in security
- ✅ **CSRF Protection**: Laravel built-in

### **📊 Business Features**

- ✅ **Real-time Updates**: Event-driven architecture
- ✅ **Export/Import**: PDF generation, data export
- ✅ **Search & Filtering**: Advanced search with indexing
- ✅ **Audit Trail**: Model events and listeners
- ✅ **Notification System**: Email + in-app notifications
- ✅ **Reporting**: Analytics, financial reports, KPIs

### **🔄 Integration Features**

- ✅ **Insurance Integration**: Claims processing, reconciliation
- ✅ **Payment Processing**: Invoice management, payment tracking
- ✅ **Marketing Integration**: Campaign tracking, lead management
- ✅ **Partner Management**: Commission tracking, engagement metrics

---

## 📋 Module Development Workflow

### **1. New Module Implementation Guide**

When adding a new module, follow this exact sequence:

#### **Step 1: Database Schema Design**

```sql
-- Example: Create migration for new module
php artisan make:migration create_[module_name]_table
```

#### **Step 2: Create Base Structure**

```bash
# Models
php artisan make:model [ModuleName] -m

# Service
mkdir app/Services/[ModuleName]
touch app/Services/[ModuleName]/[ModuleName]Service.php

# DTOs
touch app/DTOs/Create[ModuleName]DTO.php
touch app/DTOs/Update[ModuleName]DTO.php

# Controller
php artisan make:controller Admin/[ModuleName]Controller

# Validation Rules
touch app/Services/Validation/Rules/[ModuleName]Rules.php
```

#### **Step 3: Follow Performance Standards**

```php
// 1. Model with relationships and scopes
class ModuleName extends Model
{
    // Define relationships with proper indexing
    // Add scopes for common queries
    // Implement caching where appropriate
}

// 2. Service extending PerformanceOptimizedBaseService
class ModuleNameService extends PerformanceOptimizedBaseService
{
    protected $cachePrefix = 'module_name';
    protected $cacheTtl = 3600;
    // Implement business logic with caching
}

// 3. Controller extending BaseController
class ModuleNameController extends BaseController
{
    // Use CachedDropdownService for dropdowns
    // Implement proper eager loading
    // Follow thin controller principle
}

// 4. Add to CachedDropdownService if needed
public static function getModuleNames()
{
    return Cache::remember('dropdown_module_names', 600, function () {
        return ModuleName::select('id', 'name')->orderBy('name')->get();
    });
}
```

#### **Step 4: Frontend Implementation**

```typescript
// 1. Create TypeScript types
interface ModuleName {
    id: number;
    name: string;
    // ... other properties
}

// 2. Create Vue pages
// resources/js/pages/Admin/ModuleNames/Index.vue
// resources/js/pages/Admin/ModuleNames/Create.vue
// resources/js/pages/Admin/ModuleNames/Edit.vue

// 3. Add routes in web.php
Route::resource('module-names', ModuleNameController::class);
```

#### **Step 5: Performance Optimization**

```php
// Add database indexes in migration
$table->index('frequently_queried_column', 'idx_module_name_column');
$table->index(['status', 'created_at'], 'idx_module_name_status_created');

// Update CachedDropdownService cache keys
// Run performance tests
// Validate with Laravel Debugbar
```

---

## 👥 Guidelines for New Developers

### **🎯 AI Agent Instructions Template**

When requesting AI assistance for new modules, provide this context:

```markdown
## Project Context

- **Architecture**: Laravel 12 + Vue 3 + Inertia.js with Clean Architecture
- **Performance Requirements**: Must use CachedDropdownService, avoid N+1 queries
- **Standards**: Follow BaseController/BaseService patterns, use DTOs
- **Database**: PostgreSQL with required performance indexes

## Database Schema

[Provide your schema here]

## Requirements

1. Create optimized backend with proper caching
2. Implement frontend with TypeScript types
3. Follow existing patterns in app/Http/Controllers/Admin/
4. Add performance indexes for frequently queried columns
5. Use CachedDropdownService for dropdown data
6. Extend PerformanceOptimizedBaseService
7. Create proper DTOs extending BaseDTO

## Expected Deliverables

- Migration with indexes
- Model with relationships
- Service with caching
- Controller with optimization
- Vue pages with TypeScript
- Validation rules
- Routes
```

### **🔍 Code Review Checklist**

- ✅ Controller extends BaseController and is thin
- ✅ Service extends PerformanceOptimizedBaseService
- ✅ DTOs extend BaseDTO
- ✅ Database indexes on frequently queried columns
- ✅ No N+1 queries (use eager loading or cache)
- ✅ Proper error handling and validation
- ✅ TypeScript types defined
- ✅ Follows existing naming conventions

---

## 📍 Feature Location Guide

### **🔧 Where to Find/Modify Core Features**

#### **Authentication & Authorization**

- **Location**: `app/Http/Middleware/`, `app/Policies/`, `config/auth.php`
- **Key Files**:
    - `HandleInertiaRequests.php` - User data sharing
    - `spatie/permission` - Role/permission management
    - `app/Policies/` - Authorization logic

#### **Caching System**

- **Location**: `app/Services/CachedDropdownService.php`, `config/cache.php`
- **Configuration**: Redis settings in `.env`
- **Clear Cache**: `php artisan cache:clear`
- **Refresh Dropdowns**: `CachedDropdownService::refreshAll()`

#### **Database Performance**

- **Indexes**: `database/migrations/*_add_*_indexes*.php`
- **Query Optimization**: Check `app/Services/` for proper eager loading
- **N+1 Detection**: Use Laravel Debugbar in development

#### **Frontend Bundle Optimization**

- **Location**: `vite.config.ts`
- **Chunking Strategy**: Manual chunks for vue-core, inertia, charts, etc.
- **Build**: `npm run build`
- **Analysis**: `ANALYZE=true npm run build`

#### **PDF Generation**

- **Location**: Controllers using `Barryvdh\DomPDF\Facade\Pdf`
- **Templates**: Create blade views for PDF layouts
- **Usage**: `Pdf::loadView('template')->download()`

#### **Event System**

- **Events**: `app/Events/`
- **Listeners**: `app/Listeners/`
- **Registration**: `app/Providers/EventServiceProvider.php`

#### **Validation**

- **Rules**: `app/Services/Validation/Rules/`
- **DTOs**: `app/DTOs/` - Request validation and data transfer
- **Custom Rules**: Extend base validation classes

#### **Email System**

- **Mailables**: `app/Mail/`
- **Configuration**: `config/mail.php`, `.env`
- **Queue**: Background processing for performance

---

## 🎯 Quick Reference Commands

### **Development**

```bash
# Start development
php artisan serve
npm run dev

# Performance testing
curl http://localhost:8000/performance-test

# Clear all caches
php artisan optimize:clear
php artisan cache:clear

# Database operations
php artisan migrate
php artisan db:seed
```

### **Production Deployment**

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build

# Clear and warm caches
CachedDropdownService::refreshAll()
```

---

## 📊 Performance Monitoring

### **Key Metrics to Watch**

- **Page Load Time**: Target < 2 seconds
- **Database Queries**: Monitor N+1 patterns
- **Cache Hit Rate**: Should be > 90% for dropdowns
- **Memory Usage**: Keep stable around 32MB
- **Bundle Size**: Monitor chunk sizes in build output

### **Tools Available**

- **Laravel Debugbar**: Query analysis and performance metrics
- **Performance Test Route**: `/performance-test` endpoint
- **Vite Bundle Analyzer**: `ANALYZE=true npm run build`
- **Custom Benchmarking**: Available in service layer

---

This roadmap provides a complete guide for understanding and extending the Geraye Healthcare system. Follow these patterns for consistent, high-performance module development! 🚀
