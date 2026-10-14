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
}
