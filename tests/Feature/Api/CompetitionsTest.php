<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Competition;
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
            'competition'
       ]);
    }

    public function test_user_can_list_competitions(): void
    {
      $user = User::factory()->create();

      $competition = Competition::create([
          'name' => 'Champions League',
          'status' => 'not_started',
          'start_date' => '2026-08-25 00:00:00',
          'end_date' => '2027-07-15 00:00:00'
      ]);

      Passport::actingAs($user);

      $response = $this->getJson('/api/competitions');

      $response->assertStatus(200);
      
      $response->assertJsonStructure([
         'competitions'
      ]);
    }

    public function test_user_can_update_competitions(): void
    {
       $user = User::factory()->create(['role' => 'admin']);

       $competition = Competition::create([
          'name' => 'Champions League',
          'status' => 'not_started',
          'start_date' => '2026-08-25 00:00:00',
          'end_date' => '2027-07-15 00:00:00'
       ]);

       Passport::actingAs($user);

       $response = $this->putJson('/api/competitions' . $competition->id, [
           'name' => 'La Liga',
           'status' => 'in_progress'
       ]);

       $response->assertStatus(200);

       $response->assertJsonStructure([
         'message',
         'competition'
       ]);
    }
}
