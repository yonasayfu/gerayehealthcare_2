# 🚀 API Implementation Guide - Geraye Healthcare

## 📋 Table of Contents

1. [API Implementation Strategy](#api-implementation-strategy)
2. [Module Selection for Mobile](#module-selection-for-mobile)
3. [API Architecture Guidelines](#api-architecture-guidelines)
4. [Performance Optimization](#performance-optimization)
5. [Authentication & Security](#authentication--security)
6. [API Documentation](#api-documentation)
7. [Testing Strategy](#testing-strategy)
8. [AI Agent Instructions Template](#ai-agent-instructions-template)

---

## 🎯 API Implementation Strategy

### **Current Project Status Assessment**

Based on your Laravel healthcare project:

- ✅ **Web modules completed**: Patients, Staff, Inventory, Marketing, Insurance, Events
- ✅ **Clean Architecture implemented**: Controllers, Services, DTOs, Models
- ✅ **Performance optimized**: Database indexes, caching, N+1 query elimination
- 🎯 **Next Phase**: API layer for mobile Flutter integration

### **API-First Approach Benefits**

```
🌐 Web App (Inertia.js) ←→ Laravel Backend ←→ API Layer ←→ 📱 Flutter Mobile App
                                    ↓
                            Shared Business Logic
                         (Services, DTOs, Models)
```

---

## 📱 Module Selection for Mobile

### **Priority 1: Core Mobile Modules (MVP)**

```
Essential for mobile healthcare app:
✅ Authentication & User Management
✅ Patient Management (view, search, basic info)
✅ Staff Dashboard (assignments, schedule)
✅ Visit Services (check-in, notes, status updates)
✅ Notifications (real-time updates)
```

### **Priority 2: Extended Mobile Features**

```
Advanced mobile functionality:
📋 Inventory Management (mobile scanning)
📊 Reports & Analytics (offline capable)
💰 Invoice & Billing (mobile payments)
📅 Event Management (mobile registration)
📄 Document Management (camera integration)
```

### **Priority 3: Admin Features (Web-Only Initially)**

```
Complex admin operations (keep web-only):
⚙️ System Configuration
📈 Advanced Analytics & Reporting
🔧 User Role Management
💾 Data Export/Import
🎯 Marketing Campaign Management
```

### **Module Selection Criteria**

```php
// Evaluate each module for mobile suitability:
$mobileReadiness = [
    'user_interaction' => 'touch-friendly',
    'data_complexity' => 'simple to moderate',
    'offline_capability' => 'possible',
    'real_time_needs' => 'high for core features',
    'performance_critical' => 'yes',
    'security_sensitive' => 'very high'
];
```

---

## 🏗️ API Architecture Guidelines

### **1. API Versioning Strategy**

```php
// Route structure for versioned APIs
Route::prefix('api/v1')->group(function () {
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('patients', PatientController::class);
        Route::apiResource('staff', StaffController::class);
        Route::apiResource('visits', VisitServiceController::class);
    });
});

// Future version
Route::prefix('api/v2')->group(function () {
    // Enhanced features, breaking changes
});
```

### **2. RESTful API Design Principles**

```
HTTP Method | Endpoint              | Purpose
GET         | /api/v1/patients      | List patients (paginated)
GET         | /api/v1/patients/123  | Get specific patient
POST        | /api/v1/patients      | Create new patient
PUT         | /api/v1/patients/123  | Update entire patient
PATCH       | /api/v1/patients/123  | Partial patient update
DELETE      | /api/v1/patients/123  | Delete patient
```

### **3. Response Structure Standard**

```php
// Success Response Format
{
    "success": true,
    "data": {
        "id": 123,
        "full_name": "John Doe",
        "age": 45
    },
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    },
    "links": {
        "first": "/api/v1/patients?page=1",
        "last": "/api/v1/patients?page=7",
        "next": "/api/v1/patients?page=2"
    }
}

// Error Response Format
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid.",
        "details": {
            "email": ["The email field is required."],
            "phone_number": ["The phone number format is invalid."]
        }
    }
}
```

### **4. API Resource Classes**

```php
// Create API-specific resource classes
class PatientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'age' => $this->age,
            'gender' => $this->gender,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'patient_code' => $this->patient_code,
            'last_visit' => $this->whenLoaded('latestVisit', function () {
                return new VisitServiceResource($this->latestVisit);
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

// Collection resource for listings
class PatientCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage()
            ]
        ];
    }
}
```

---

## ⚡ Performance Optimization

### **1. Mobile-Optimized Query Patterns**

```php
// Lightweight mobile queries
class MobilePatientService extends PatientService
{
    public function getMobilePatientList(Request $request): Collection
    {
        return Cache::remember(
            "mobile_patients_page_{$request->page}",
            300, // 5 minutes cache
            function () use ($request) {
                return Patient::select([
                        'id', 'full_name', 'age', 'gender',
                        'phone_number', 'patient_code'
                    ])
                    ->with('latestVisit:id,patient_id,status,scheduled_at')
                    ->when($request->search, function ($query, $search) {
                        return $query->where('full_name', 'ILIKE', "%{$search}%")
                                   ->orWhere('patient_code', 'ILIKE', "%{$search}%");
                    })
                    ->orderBy('updated_at', 'desc')
                    ->paginate(20); // Smaller pages for mobile
            }
        );
    }
}
```

### **2. Mobile-Specific Caching Strategy**

```php
// Mobile cache with shorter TTL for real-time data
class MobileCacheService
{
    const CACHE_TTL = [
        'patient_list' => 300,      // 5 minutes
        'patient_detail' => 600,    // 10 minutes
        'staff_schedule' => 120,    // 2 minutes (real-time)
        'visit_status' => 60,       // 1 minute (critical)
        'notifications' => 30,      // 30 seconds (urgent)
    ];

    public static function rememberForMobile(string $key, string $type, callable $callback)
    {
        $ttl = self::CACHE_TTL[$type] ?? 300;
        return Cache::remember("mobile_{$key}", $ttl, $callback);
    }
}
```

### **3. Data Pagination for Mobile**

```php
// Mobile-optimized pagination
class MobilePaginationService
{
    public static function paginateForMobile($query, $perPage = 20)
    {
        return $query->paginate($perPage, ['*'], 'page', request('page', 1));
    }

    public static function getCursorPagination($query, $perPage = 20)
    {
        // For infinite scrolling in mobile apps
        return $query->cursorPaginate($perPage);
    }
}
```

---

## 🔐 Authentication & Security

### **1. Laravel Sanctum Implementation**

```php
// API Authentication Controller
class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create token with abilities
        $token = $user->createToken($request->device_name, [
            'patient:read',
            'patient:create',
            'visit:read',
            'visit:update'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token->plainTextToken,
                'user' => new UserResource($user),
                'abilities' => $token->accessToken->abilities,
                'expires_at' => now()->addDays(30)
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
```

### **2. API Security Middleware**

```php
// Rate limiting for mobile APIs
class MobileApiRateLimit
{
    public function handle($request, Closure $next)
    {
        $limits = [
            'auth' => '10,1',      // 10 attempts per minute
            'read' => '100,1',     // 100 requests per minute
            'write' => '30,1',     // 30 writes per minute
        ];

        $type = $this->getRequestType($request);
        RateLimiter::hit($request->ip() . ':' . $type, $limits[$type]);

        return $next($request);
    }
}

// API-specific CORS
class ApiCorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        return $response;
    }
}
```

---

## 📚 API Documentation

### **1. OpenAPI/Swagger Documentation**

```php
// Install Laravel API Documentation
composer require darkaonline/l5-swagger

// Document your API endpoints
/**
 * @OA\Get(
 *     path="/api/v1/patients",
 *     summary="Get list of patients",
 *     tags={"Patients"},
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search term",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/Patient")
 *             )
 *         )
 *     )
 * )
 */
public function index(Request $request)
{
    // Implementation
}
```

### **2. API Response Documentation**

```json
// Document response formats in /docs/api-responses.md
{
    "patients_list": {
        "success": true,
        "data": [
            {
                "id": 123,
                "full_name": "John Doe",
                "patient_code": "PT20240001",
                "age": 45,
                "gender": "Male",
                "phone_number": "+1234567890",
                "last_visit": {
                    "id": 456,
                    "status": "Completed",
                    "scheduled_at": "2024-01-15T10:30:00Z"
                }
            }
        ],
        "meta": {
            "current_page": 1,
            "per_page": 20,
            "total": 150
        }
    }
}
```

---

## 🧪 Testing Strategy

### **1. API Testing Structure**

```php
// API Test Base Class
abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    protected function apiGet(string $uri): TestResponse
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson($uri);
    }

    protected function apiPost(string $uri, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson($uri, $data);
    }
}

// Example API Test
class PatientApiTest extends ApiTestCase
{
    public function test_can_list_patients()
    {
        Patient::factory()->count(5)->create();

        $response = $this->apiGet('/api/v1/patients');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'full_name',
                            'patient_code',
                            'age'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }

    public function test_can_create_patient()
    {
        $patientData = [
            'full_name' => 'Jane Doe',
            'date_of_birth' => '1980-05-15',
            'gender' => 'Female',
            'phone_number' => '+1987654321',
            'email' => 'jane@example.com'
        ];

        $response = $this->apiPost('/api/v1/patients', $patientData);

        $response->assertStatus(201)
                ->assertJsonFragment([
                    'success' => true,
                    'full_name' => 'Jane Doe'
                ]);

        $this->assertDatabaseHas('patients', [
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com'
        ]);
    }
}
```

---

## 🤖 AI Agent Instructions Template

### **For API Implementation Requests**

```markdown
## Project Context - Healthcare API Development

- **Architecture**: Laravel 12 + Clean Architecture (Controllers → Services → Models)
- **Current Status**: Web modules completed, implementing mobile API layer
- **Technology**: Laravel Sanctum auth, PostgreSQL, Redis caching
- **Performance**: Must use existing CachedDropdownService, avoid N+1 queries
- **Security**: Role-based permissions, rate limiting, input validation

## API Requirements

- **Version**: v1 (/api/v1/\*)
- **Auth**: Laravel Sanctum token-based
- **Response Format**: Standardized JSON with success/error structure
- **Pagination**: 20 items per page for mobile optimization
- **Caching**: Mobile-specific cache with shorter TTL
- **Testing**: Full API test coverage required

## Module to Convert: [MODULE_NAME]

[Provide specific module details]

## Expected Deliverables

1. **API Controller** extending base API patterns
2. **API Resource Classes** for response formatting
3. **API Routes** with proper versioning and middleware
4. **API Tests** covering CRUD operations
5. **Mobile-Optimized Service** with caching
6. **Documentation** with request/response examples
7. **Rate Limiting** and security middleware
8. **Validation Rules** for API requests

## Performance Requirements

- Response time < 200ms for simple queries
- Implement mobile-specific caching
- Use cursor pagination for large datasets
- Minimize payload size for mobile networks
- Follow existing database index patterns

## Code Standards

- Extend existing Service classes
- Use API Resource transformations
- Follow Laravel API conventions
- Implement proper error handling
- Add comprehensive test coverage
- Document all endpoints
```

### **Quality Checklist for API Implementation**

```bash
✅ RESTful endpoint design
✅ Proper HTTP status codes
✅ Standardized response format
✅ Input validation and sanitization
✅ Rate limiting implemented
✅ Authentication/authorization working
✅ API resources for data transformation
✅ Comprehensive test coverage
✅ Documentation with examples
✅ Mobile-optimized performance
✅ Error handling and logging
✅ Security headers and CORS
```

---

## 🚀 Next Steps

### **1. Implementation Phase Planning**

```
Week 1-2: Core Authentication & User Management API
Week 3-4: Patient Management API
Week 5-6: Staff & Visit Services API
Week 7-8: Notifications & Real-time Features
Week 9-10: Testing, Documentation, Performance Optimization
```

### **2. API Development Workflow**

```bash
# 1. Select module for API conversion
# 2. Create API routes and controller
# 3. Implement API resources and services
# 4. Add comprehensive tests
# 5. Update documentation
# 6. Performance testing and optimization
# 7. Security review and rate limiting
# 8. Mobile app integration testing
```

### **3. Mobile Integration Preparation**

- API documentation ready for Flutter team
- Authentication flow tested and documented
- Sample API calls and responses provided
- Error handling patterns established
- Performance benchmarks documented

This guide ensures your Laravel API will be optimized, secure, and ready for Flutter mobile integration! 🏥📱
