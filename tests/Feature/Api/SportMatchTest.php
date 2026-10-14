<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Team;
use App\Models\Competition;
use App\Models\SportMatch;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SportMatchTest extends TestCase
{
    use RefreshDatabase;

    private Competition $competition;
    private Team $teamHome;
    private Team $teamAway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->competition = Competition::create([
            'name'       => 'La Liga',
            'status'     => 'not_started',
            'start_date' => '2026-08-25 00:00:00',
            'end_date'   => '2027-07-15 00:00:00'
        ]);

        $this->teamHome = Team::create([
            'name'       => 'Real Madrid',
            'logo'       => 'logos/rm.png',
            'country'    => 'España',
            'founded_at' => 1902,
            'type'       => 'club',
            'sport'      => 'soccer football'
        ]);

        $this->teamAway = Team::create([
            'name'       => 'Barcelona',
            'logo'       => 'logos/fcb.png',
            'country'    => 'España',
            'founded_at' => 1899,
            'type'       => 'club',
            'sport'      => 'soccer football'
        ]);
    }

    public function test_user_can_create_matches(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Passport::actingAs($user);

        $response = $this->postJson('/api/matches', [
            'competition_id' => $this->competition->id,
            'home_team_id'    => $this->teamHome->id,
            'away_team_id'    => $this->teamAway->id,
            'date'            => '2026-10-24 21:00:00',
            'location'        => 'Santiago Bernabéu',
            'stage'           => 'Jornada 11',
            'status'          => 'upcoming',
            'sport'           => 'soccer football'
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'match' => ['id', 'location', 'stage', 'date', 'status', 'sport']
        ]);
    }

    public function test_user_can_list_matches(): void
    {
        $user = User::factory()->create();

        SportMatch::create([
            'competition_id' => $this->competition->id,
            'home_team_id'    => $this->teamHome->id,
            'away_team_id'    => $this->teamAway->id,
            'date'            => '2026-10-24 21:00:00',
            'location'        => 'Santiago Bernabéu',
            'stage'           => 'Jornada 11',
            'status'          => 'upcoming',
            'sport'           => 'soccer football'
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/matches');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'matches'
        ]);
    }
}
