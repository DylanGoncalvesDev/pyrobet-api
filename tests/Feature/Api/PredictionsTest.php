<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Team;
use App\Models\Competition;
use App\Models\SportMatch;
use App\Models\Prediction;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PredictionsTest extends TestCase
{
    use RefreshDatabase;

    private Competition $competition;
    private Team $teamHome;
    private Team $teamAway;
    private SportMatch $match;

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

        $this->match = SportMatch::create([
            'competition_id' => $this->competition->id,
            'home_team_id'    => $this->teamHome->id,
            'away_team_id'    => $this->teamAway->id,
            'date'            => '2026-10-24 21:00:00',
            'location'        => 'Santiago Bernabéu',
            'stage'           => 'Jornada 11',
            'status'          => 'upcoming',
            'sport'           => 'soccer football'
        ]);
    }

    public function test_user_can_create_predictions(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        Passport::actingAs($user);

        $response = $this->postJson('/api/predictions', [
            'match_id' => $this->match->id,
            'prediction' => 'home',
            'home_score_prediction' => 3,
            'away_score_prediction' => 1
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'prediction' => ['id', 'user_id', 'match_id', 'prediction', 'home_score_prediction', 'status']
        ]);
    }

    public function test_user_can_list_predictions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        Prediction::create([
            'user_id' => $user->id,
            'match_id' => $this->match->id,
            'status' => 'pending',
            'prediction' => 'home',
            'home_score_prediction' => 2,
            'away_score_prediction' => 0,
            'points' => 0
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/predictions');

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'message',
            'predictions'
        ]);
    }
}
