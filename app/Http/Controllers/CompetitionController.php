<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    //
    public function store(Request $request): JsonResponse
    {
       $validatedData = $request->validate([
           'name' => [
             'required',
             'string',
             'max:255',
             'unique:competitions',
             'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\_]+$/'
           ],
           'description' => 'nullable|string|max:2000',
           'status' => 'required|string|in:not_started,in_progress,finished',
           'start_date'  => 'required|date|date_format:Y-m-d H:i:s',
           'end_date'    => 'required|date|date_format:Y-m-d H:i:s|after:start_date'
       ]);

       $competition = Competition::create($validatedData);

       return response()->json([
          'message' => 'Competition successfully created',
          'competition' => $competition
       ], 201);
    }

    public function index(): JsonResponse
    {
       $competitions = Competition::all();

       return response()->json([
           'competitions' => $competitions
       ]);  
    }

    public function update(Request $request , string $id): JsonResponse
    {
       $competition = Competition::findOrFail($id);

       $validatedData = $request->validate([
          'name' => [
             'required',
             'string',
             'max:255',
             'unique:competitions,name,' . $id,
             'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\_]+$/'
          ],
          'description' => 'nullable|string|max:1000',
          'status'      => 'required|string|in:not_started,in_progress,finished',
          'start_date'  => 'required|date|date_format:Y-m-d H:i:s',
          'end_date'    => 'required|date|date_format:Y-m-d H:i:s|after:start_date'
       ]);

       $competition->update($validatedData);

       return response()->json([
            'message'     => 'Competition updated successfully',
            'competition' => $competition
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
       $competition = Competition::findOrFail($id);

       $competition->delete();

       return response()->json([
          'message' => 'Competition deleted successfully',
       ]);
    }
}
