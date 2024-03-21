<?php

namespace Tests\Feature;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Country::factory()->create();
    }

    public function test_example(): void
    {
        $response = $this->get('/api/cities');

        $response->assertStatus(200);

    }
}
