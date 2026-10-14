<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Team;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_can_create_teams(): void
    {
       $user = User::factory()->create(['role' => 'admin']);

       Passport::actingAs($user);

       $response = $this->postJson('/api/teams', [
            'name' => 'barcelona',
            'logo' => 'logos/real_madrid.png',
            'country' => 'España',
            'founded_at' => '1999',
            'type' => 'club',
            'sport'  => 'soccer football'
       ]);

       $response->assertStatus(201);

       $response->assertJsonStructure([
         'message',
         'team'
       ]);
    }

    public function test_user_can_list_teams(): void
    {
        $user = User::factory()->create();

        $team = Team::create([
            'name' => 'barcelona',
            'logo' => 'logos/real_madrid.png',
            'country' => 'España',
            'founded_at' => '1999',
            'type' => 'club',
            'sport'  => 'soccer football'
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/teams');

        $response->assertStatus(200);

        $response->assertJsonStructure([
         'teams'
        ]);
    }

    public function test_user_can_update_teams(): void
    {
       $user = User::factory()->create(['role' => 'admin']);

       $team = Team::create([
            'name' => 'barcelona',
            'logo' => 'logos/real_madrid.png',
            'country' => 'España',
            'founded_at' => '1999',
            'type' => 'club',
            'sport'  => 'soccer football'
       ]);
       
       Passport::actingAs($user);

       $response = $this->putJson('/api/teams/' . $team->id, [
          'name' => 'madrid',
          'logo' => 'logos/real_madrid.png',
          'country' => 'España',
          'founded_at' => '1999',
          'type' => 'club',
          'sport'  => 'soccer football'
       ]);

       $response->assertStatus(200);

       $response->assertJsonStructure([
           'message',
           'team'
       ]);
    }

}
