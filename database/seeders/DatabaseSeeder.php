<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Prediction;
use App\Models\SportMatch;
use App\Models\Team;
use App\Models\Competition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       User::create([
            'name'     => 'player123',
            'email'    => 'player@pyrobet.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

       User::create([
            'name'     => 'Admin PyroBet',
            'email'    => 'admin@pyrobet.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

         $champions = Competition::create([
            'name'        => 'UEFA Champions League',
            'description' => 'El torneo de clubes más importante de Europa.',
            'status'      => 'in_progress',
            'start_date'  => '2026-09-15 00:00:00',
            'end_date'    => '2027-05-30 23:59:59',
        ]);

        $laliga = Competition::create([
            'name'        => 'LaLiga EA Sports',
            'description' => 'Primera división del fútbol español.',
            'status'      => 'in_progress',
            'start_date'  => '2026-08-15 00:00:00',
            'end_date'    => '2027-05-25 23:59:59',
        ]);

        $madrid = Team::create([
            'name'       => 'Real Madrid',
            'logo'       => 'logos/real_madrid.png',
            'country'    => 'España',
            'founded_at' => 1902,
            'type'       => 'club',
            'sport'      => 'soccer football',
        ]);

        $barcelona = Team::create([
            'name'       => 'FC Barcelona',
            'logo'       => 'logos/barcelona.png',
            'country'    => 'España',
            'founded_at' => 1899,
            'type'       => 'club',
            'sport'      => 'soccer football',
        ]);

        $city = Team::create([
            'name'       => 'Manchester City',
            'logo'       => 'logos/man_city.png',
            'country'    => 'Inglaterra',
            'founded_at' => 1880,
            'type'       => 'club',
            'sport'      => 'soccer football',
        ]);

        $psg = Team::create([
            'name'       => 'Paris Saint-Germain',
            'logo'       => 'logos/psg.png',
            'country'    => 'Francia',
            'founded_at' => 1970,
            'type'       => 'club',
            'sport'      => 'soccer football',
        ]);

        SportMatch::create([
            'competition_id' => $champions->id,
            'home_team_id'    => $madrid->id,
            'away_team_id'    => $city->id,
            'date'            => '2026-11-04 21:00:00',
            'location'        => 'Estadio Santiago Bernabéu',
            'stage'           => 'Group Stage - Matchday 4',
            'status'          => 'upcoming',
            'sport'           => 'soccer football',
        ]);

        SportMatch::create([
            'competition_id' => $champions->id,
            'home_team_id'    => $barcelona->id,
            'away_team_id'    => $psg->id,
            'date'            => '2026-11-05 21:00:00',
            'location'        => 'Estadi Olímpic Lluís Companys',
            'stage'           => 'Group Stage - Matchday 4',
            'status'          => 'upcoming',
            'sport'           => 'soccer football',
        ]);

        SportMatch::create([
            'competition_id' => $laliga->id,
            'home_team_id'    => $madrid->id,
            'away_team_id'    => $barcelona->id,
            'date'            => '2026-12-20 16:15:00',
            'location'        => 'Estadio Santiago Bernabéu',
            'stage'           => 'Jornada 17',
            'status'          => 'upcoming',
            'sport'           => 'soccer football',
        ]);
    }
}