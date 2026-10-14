<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    //
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => [
             'required',
             'string',
             'max:255',
             'unique:teams',
             'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\_]+$/'
            ],
            'logo'  => 'nullable|string|max:255',
            'country'  => 'required|string|max:100',
            'founded_at'  => 'required|integer|min:1800|max:' . date('Y'),
            'type'  => 'required|in:national,club',
            'sport' => 'required|in:american football,soccer football,rugby,basketball,baseball,cricket,softball,volleyball,hockey,handball,futsal'
        ]);

        $team = Team::create($validatedData);

        return response()->json([
           'message' => 'Team created successfully',
           'team'  => $team
        ]);
    }

    public function index(): JsonResponse
    {
        $teams = Team::all();

        return response()->json([
           'teams' => $teams
        ]);  
    }

    public function update(Request $request, string $id ): JsonResponse
    {
        $team = Team::findOrFail($id);

        $validatedData = $request->validate([
            'name' => [
             'required',
             'string',
             'max:255',
             'unique:teams,name,' . $id,
             'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\_]+$/'
            ],
            'logo'  => 'nullable|string|max:255',
            'country'  => 'required|string|max:100',
            'founded_at'  => 'required|integer|min:1800|max:' . date('Y'),
            'type'  => 'required|in:national,club',
            'sport' => 'required|in:american football,soccer football,rugby,basketball,baseball,cricket,softball,volleyball,hockey,handball,futsal'
        ]);

        $team->update($validatedData);

        return response()->json([
           'message' => 'Team updated successfully',
           'team' => $team
        ]);  
    }

    public function destroy(string $id): JsonResponse
    {
        $team = Team::findOrFail($id);

        $team->delete();

        return response()->json([
           'message' => 'Team deleted successfully',
        ]);  
    }


}
