<?php

namespace Tests\Feature\Insurance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Assuming this controller handles the conversions

class DateConversionApiTest extends TestCase
{
    use RefreshDatabase; // Use RefreshDatabase if you need a clean database state for tests

    /** @test */
    public function it_can_convert_gregorian_to_ethiopian_date()
    {
        $gregorianDate = [
            'year' => 2024,
            'month' => 9,
            'day' => 11,
        ]; // September 11, 2024 Gregorian is Meskerem 1, 2017 Ethiopian

        $response = $this->postJson('/api/v1/convert-gregorian-to-ethiopian', $gregorianDate);

        $response->assertStatus(200)
            ->assertJson([
                'year' => 2017,
                'month' => 1,
                'day' => 1,
            ]);
    }

    /** @test */
    public function it_can_convert_ethiopian_to_gregorian_date()
    {
        $ethiopianDate = [
            'year' => 2017,
            'month' => 1,
            'day' => 1,
        ]; // Meskerem 1, 2017 Ethiopian is September 11, 2024 Gregorian

        $response = $this->postJson('/api/v1/convert-ethiopian-to-gregorian', $ethiopianDate);

        $response->assertStatus(200)
            ->assertJson([
                'year' => 2024,
                'month' => 9,
                'day' => 11,
            ]);
    }

    /** @test */
    public function it_can_convert_gregorian_to_ethiopian_date_via_v1_api()
    {
        $gregorianDateString = '2024-09-11'; // September 11, 2024 Gregorian is Meskerem 1, 2017 Ethiopian

        $response = $this->postJson('/api/v1/convert-to-ethiopian', ['date' => $gregorianDateString]);

        $response->assertStatus(200)
            ->assertJson([
                'ethiopian_date' => '2017-01-01',
            ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_gregorian_date_v1_api()
    {
        $invalidDateString = '2024-13-01'; // Invalid month

        $response = $this->postJson('/api/v1/convert-to-ethiopian', ['date' => $invalidDateString]);

        $response->assertStatus(422) // Unprocessable Entity for validation errors
            ->assertJsonValidationErrors(['date']);
    }

    /** @test */
    public function it_returns_error_for_invalid_ethiopian_date()
    {
        $invalidEthiopianDate = [
            'year' => 2017,
            'month' => 14, // Invalid month
            'day' => 1,
        ];

        $response = $this->postJson('/api/v1/convert-ethiopian-to-gregorian', $invalidEthiopianDate);

        $response->assertStatus(422) // Unprocessable Entity for validation errors
            ->assertJsonValidationErrors(['month']);
    }
}
