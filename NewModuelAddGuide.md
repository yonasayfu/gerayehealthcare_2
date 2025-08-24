## Project Context
- **Architecture**: Laravel 12 + Vue 3 + Inertia.js with Clean Architecture
- **Performance**: Must use CachedDropdownService, avoid N+1 queries
- **Standards**: Follow BaseController/BaseService patterns, use DTOs
- **Database**: PostgreSQL with required performance indexes

## Database Schema
[Your schema here]

## Requirements
1. Create optimized backend with proper caching
2. Follow existing patterns in app/Http/Controllers/Admin/
3. Add performance indexes for frequently queried columns
4. Use CachedDropdownService for dropdown data
5. Extend PerformanceOptimizedBaseService
6. Create proper DTOs extending BaseDTO