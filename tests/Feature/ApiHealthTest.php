<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ApiHealthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user with staff record
        $this->user = User::factory()->create();
        $this->staff = Staff::factory()->create(['user_id' => $this->user->id]);
        
        // Assign role
        $this->user->assignRole('admin');
    }

    /** @test */
    public function test_api_authentication_works()
    {
        // Test unauthenticated request
        $response = $this->getJson('/api/v1/patients');
        $response->assertStatus(401);

        // Test authenticated request
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/v1/patients');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_patient_api_endpoints()
    {
        Sanctum::actingAs($this->user);

        // Create test patient
        $patient = Patient::factory()->create();

        // Test GET patients
        $response = $this->getJson('/api/v1/patients');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'full_name', 'email', 'phone_number']
                    ]
                ]);

        // Test GET specific patient
        $response = $this->getJson("/api/v1/patients/{$patient->id}");
        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $patient->id,
                        'full_name' => $patient->full_name
                    ]
                ]);

        // Test POST create patient
        $patientData = [
            'full_name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone_number' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date(),
            'gender' => 'male',
            'address' => $this->faker->address,
        ];

        $response = $this->postJson('/api/v1/patients', $patientData);
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['id', 'full_name', 'email']
                ]);
    }

    /** @test */
    public function test_marketing_api_endpoints()
    {
        Sanctum::actingAs($this->user);

        // Test GET campaigns
        $response = $this->getJson('/api/v1/marketing/campaigns');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'campaigns',
                        'pagination'
                    ]
                ]);

        // Test POST create campaign
        $campaignData = [
            'name' => 'Test Campaign',
            'description' => 'Test campaign description',
            'utm_source' => 'test_source',
            'budget' => 1000,
            'start_date' => now()->format('Y-m-d'),
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/marketing/campaigns', $campaignData);
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'campaign' => ['id', 'name', 'utm_source']
                    ]
                ]);

        // Test GET leads
        $response = $this->getJson('/api/v1/marketing/leads');
        $response->assertStatus(200);

        // Test GET analytics
        $response = $this->getJson('/api/v1/marketing/analytics');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'campaigns',
                        'leads',
                        'top_campaigns',
                        'lead_sources'
                    ]
                ]);
    }

    /** @test */
    public function test_inventory_api_endpoints()
    {
        Sanctum::actingAs($this->user);

        // Test GET inventory items
        $response = $this->getJson('/api/v1/inventory/items');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'items',
                        'pagination'
                    ]
                ]);

        // Test POST create inventory item
        $itemData = [
            'name' => 'Test Item',
            'sku' => 'TEST-' . rand(1000, 9999),
            'category' => 'Medical Supplies',
            'unit_of_measure' => 'pieces',
            'unit_cost' => 10.50,
            'current_stock' => 100,
            'reorder_level' => 10,
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/inventory/items', $itemData);
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'item' => ['id', 'name', 'sku']
                    ]
                ]);

        // Test GET analytics
        $response = $this->getJson('/api/v1/inventory/analytics');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'summary',
                        'categories',
                        'low_stock_alerts'
                    ]
                ]);
    }

    /** @test */
    public function test_insurance_api_endpoints()
    {
        Sanctum::actingAs($this->user);

        // Test GET insurance companies
        $response = $this->getJson('/api/v1/insurance/companies');
        $response->assertStatus(200);

        // Test GET policies
        $response = $this->getJson('/api/v1/insurance/policies');
        $response->assertStatus(200);

        // Test GET claims
        $response = $this->getJson('/api/v1/insurance/claims');
        $response->assertStatus(200);

        // Test GET analytics
        $response = $this->getJson('/api/v1/insurance/analytics');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'summary',
                        'claims_by_status',
                        'top_companies'
                    ]
                ]);
    }

    /** @test */
    public function test_analytics_api_endpoints()
    {
        Sanctum::actingAs($this->user);

        // Test dashboard analytics
        $response = $this->getJson('/api/v1/analytics/dashboard');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'overview',
                        'patients',
                        'visits',
                        'revenue',
                        'staff',
                        'marketing'
                    ]
                ]);

        // Test patient analytics
        $response = $this->getJson('/api/v1/analytics/patients');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'summary',
                        'demographics',
                        'acquisition',
                        'trends'
                    ]
                ]);

        // Test visit analytics
        $response = $this->getJson('/api/v1/analytics/visits');
        $response->assertStatus(200);

        // Test revenue analytics
        $response = $this->getJson('/api/v1/analytics/revenue');
        $response->assertStatus(200);

        // Test staff analytics
        $response = $this->getJson('/api/v1/analytics/staff');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_bulk_operations_api_endpoints()
    {
        Sanctum::actingAs($this->user);

        // Test bulk create patients
        $patientsData = [
            'patients' => [
                [
                    'full_name' => 'Test Patient 1',
                    'email' => 'test1@example.com',
                    'phone_number' => '+251911111111',
                    'gender' => 'male'
                ],
                [
                    'full_name' => 'Test Patient 2',
                    'email' => 'test2@example.com',
                    'phone_number' => '+251911111112',
                    'gender' => 'female'
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/bulk/patients', $patientsData);
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'created_patients',
                        'success_count',
                        'error_count'
                    ]
                ]);

        // Test bulk export
        $exportData = [
            'model' => 'patients',
            'format' => 'json'
        ];

        $response = $this->postJson('/api/v1/bulk/export', $exportData);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'data',
                        'count',
                        'format'
                    ]
                ]);
    }

    /** @test */
    public function test_api_validation_errors()
    {
        Sanctum::actingAs($this->user);

        // Test invalid patient creation
        $response = $this->postJson('/api/v1/patients', []);
        $response->assertStatus(422)
                ->assertJsonStructure(['errors']);

        // Test invalid campaign creation
        $response = $this->postJson('/api/v1/marketing/campaigns', [
            'name' => '', // Invalid empty name
        ]);
        $response->assertStatus(422);

        // Test invalid inventory item creation
        $response = $this->postJson('/api/v1/inventory/items', [
            'name' => 'Test',
            'sku' => '', // Invalid empty SKU
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function test_api_search_and_filtering()
    {
        Sanctum::actingAs($this->user);

        // Create test data
        $patient = Patient::factory()->create(['full_name' => 'John Doe']);

        // Test patient search
        $response = $this->getJson('/api/v1/patients?search=John');
        $response->assertStatus(200);

        // Test marketing campaigns with filters
        $response = $this->getJson('/api/v1/marketing/campaigns?status=active');
        $response->assertStatus(200);

        // Test inventory items with category filter
        $response = $this->getJson('/api/v1/inventory/items?category=Medical');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_api_pagination()
    {
        Sanctum::actingAs($this->user);

        // Test pagination parameters
        $response = $this->getJson('/api/v1/patients?page=1&per_page=5');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [],
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }
}
