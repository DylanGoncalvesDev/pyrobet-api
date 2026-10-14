<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportMatch;
use Illuminate\Http\JsonResponse;


class SportMatchController extends Controller
{
    //
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'competition_id'  => 'required|exists:competitions,id',
            'home_team_id'    => 'required|exists:teams,id|different:away_team_id',
            'away_team_id'    => 'required|exists:teams,id',
            'date'            => 'required|date|date_format:Y-m-d H:i:s',
            'location'        => 'required|string|max:255',
            'stage'           => 'required|string|max:255',
            'status'          => 'required|string|in:upcoming,live,finished',
            'home_team_score' => 'nullable|integer|min:0',
            'away_team_score' => 'nullable|integer|min:0',
            'sport'           => 'required|in:american football,soccer football,rugby,basketball,baseball,cricket,softball,volleyball,hockey,handball,futsal',
        ]);

        $match = SportMatch::create($validated);

        return response()->json([
            'message' => 'Match created successfully',
            'match' => $match
        ], 201);
    }

    public function index(): JsonResponse
    {
        $matches = SportMatch::with(['competition', 'homeTeam', 'awayTeam'])->get();

        return response()->json([
            'matches' => $matches
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $match = SportMatch::findOrFail($id);

        $validated = $request->validate([
            'competition_id'  => 'required|exists:competitions,id',
            'home_team_id'    => 'required|exists:teams,id|different:away_team_id',
            'away_team_id'    => 'required|exists:teams,id',
            'date'            => 'required|date|date_format:Y-m-d H:i:s',
            'location'        => 'required|string|max:255',
            'stage'           => 'required|string|max:255',
            'status'          => 'required|string|in:upcoming,live,finished',
            'home_team_score' => 'nullable|integer|min:0',
            'away_team_score' => 'nullable|integer|min:0',
            'sport'           => 'required|in:american football,soccer football,rugby,basketball,baseball,cricket,softball,volleyball,hockey,handball,futsal',
        ]);

        $match->update($validated);

        return response()->json([
            'message' => 'Match updated successfully',
            'match' => $match
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $match = SportMatch::findOrFail($id);
        
        $match->delete();

        return response()->json([
            'message' => 'Match deleted successfully'
        ], 200);
    }
}
