<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CompetitionsTest extends TestCase
{
   use RefreshDatabase;

    public function test_user_can_create_competitions(): void
    {
       $user = User::factory()->create(['role' => 'admin']);

       Passport::actingAs($user);

       $response = $this->postJson('/api/competitions', [
           'name' => 'La Liga',
           'description' => 'Spain top-tier professional football league',
           'status' => 'not_started',
           'start_date' => '2026-08-25 00:00:00',
           'end_date' => '2027-07-15 00:00:00'
       ]);

       $response->assertStatus(201);

       $response->assertJsonStructure([
            'message',
       ]);
    }
}
