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

class RankingTest extends TestCase
{
   use RefreshDatabase;

    private Competition $competition;
    private SportMatch $match;

    protected function setUp(): void
    {
        parent::setUp();

        $this->competition = Competition::create([
            'name' => 'La Liga', 
            'status' => 'not_started', 
            'start_date' => '2026-08-25 00:00:00', 
            'end_date' => '2027-07-15 00:00:00'
        ]);

        $teamHome = Team::create([
            'name' => 'Real Madrid', 
            'logo' => 'logos/rm.png', 
            'country' => 'España', 
            'founded_at' => 1902, 
            'type' => 'club', 
            'sport' => 'soccer football'
        ]);
        $teamAway = Team::create([
            'name' => 'Barcelona', 
            'logo' => 'logos/fcb.png', 
            'country' => 'España', 
            'founded_at' => 1899, 
            'type' => 'club', 
            'sport' => 'soccer football'
        ]);

        $this->match = SportMatch::create([
            'competition_id' => $this->competition->id, 
            'home_team_id' => $teamHome->id, 
            'away_team_id' => $teamAway->id,
            'date' => '2026-10-24 21:00:00', 
            'location' => 'Santiago Bernabéu', 
            'stage' => 'Jornada 11', 
            'status' => 'finished', 
            'sport' => 'soccer football'
        ]);
    }

     public function test_user_can_get_ranking_ordered_by_points(): void
    {
        $user1 = User::factory()->create(['name' => 'User 1']);

        Prediction::create([
            'user_id' => $user1->id, 
            'match_id' => $this->match->id, 
            'status' => 'correct',
            'prediction' => 'home', 
            'home_score_prediction' => 3, 
            'away_score_prediction' => 1, 
            'points' => 10
        ]);

        $user2 = User::factory()->create(['name' => 'User 2']);

        Prediction::create([
            'user_id' => $user2->id, 
            'match_id' => $this->match->id, 
            'status' => 'correct',
            'prediction' => 'home', 
            'home_score_prediction' => 3, 
            'away_score_prediction' => 1, 
            'points' => 30
        ]);

        Passport::actingAs($user1);

        $response = $this->getJson('/api/ranking');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'ranking' => [
                '*' => ['id', 'name', 'total_points']
            ]
        ]);

        $this->assertEquals('User B', $response->json('ranking.0.name'));
        
        $this->assertEquals(30, $response->json('ranking.0.total_points'));
    }
}
