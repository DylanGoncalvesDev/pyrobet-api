<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportMatch;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;


class SportMatchController extends Controller
{
    #[OA\Post(
        path: "/api/matches",
        tags: ["Partidos"],
        summary: "Registrar un nuevo partido (Solo Administradores)",
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["competition_id", "home_team_id", "away_team_id", "date", "location", "stage", "status", "sport"],
            properties: [
                new OA\Property(property: "competition_id", type: "integer", example: 1),
                new OA\Property(property: "home_team_id", type: "integer", example: 1),
                new OA\Property(property: "away_team_id", type: "integer", example: 2),
                new OA\Property(property: "date", type: "string", example: "2026-10-24 21:00:00"),
                new OA\Property(property: "location", type: "string", example: "Santiago Bernabéu"),
                new OA\Property(property: "stage", type: "string", example: "Jornada 11"),
                new OA\Property(property: "status", type: "string", enum: ["upcoming", "live", "finished"], example: "upcoming"),
                new OA\Property(property: "sport", type: "string", example: "soccer football")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Partido creado correctamente")]
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
    
     #[OA\Get(
        path: "/api/matches",
        tags: ["Partidos"],
        summary: "Consultar listado general de partidos con relaciones"
    )]
    #[OA\Response(response: 200, description: "Colección hidratada de encuentros")]
    public function index(): JsonResponse
    {
        $matches = SportMatch::with(['competition', 'homeTeam', 'awayTeam'])->get();

        return response()->json([
            'matches' => $matches
        ], 200);
    }
    
     #[OA\Put(
        path: "/api/matches/{id}",
        tags: ["Partidos"],
        summary: "Actualizar un partido y disparar el cálculo de puntos (Solo Administradores)",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["competition_id", "home_team_id", "away_team_id", "date", "location", "stage", "status", "sport"],
            properties: [
                new OA\Property(property: "competition_id", type: "integer", example: 1),
                new OA\Property(property: "home_team_id", type: "integer", example: 1),
                new OA\Property(property: "away_team_id", type: "integer", example: 2),
                new OA\Property(property: "date", type: "string", example: "2026-10-24 21:00:00"),
                new OA\Property(property: "location", type: "string", example: "Santiago Bernabéu"),
                new OA\Property(property: "stage", type: "string", example: "Jornada 11"),
                new OA\Property(property: "status", type: "string", example: "finished"),
                new OA\Property(property: "home_team_score", type: "integer", example: 2),
                new OA\Property(property: "away_team_score", type: "integer", example: 1),
                new OA\Property(property: "sport", type: "string", example: "soccer football")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Partido actualizado y predicciones calificadas")]
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

          if ($match->status === 'finished') {
              $realResult = 'draw';

            if ($match->home_team_score > $match->away_team_score) {
                $realResult = 'home';
            } elseif ($match->home_team_score < $match->away_team_score) {
                      $realResult = 'away';
            }

            $predictions = $match->predictions;

            foreach ($predictions as $prediction) {
                $earnedPoints = 0;
                $status = 'incorrect';

                $hitWinner = ($prediction->prediction === $realResult);
                $hitHomeScore = ($prediction->home_score_prediction == $match->home_team_score);
                $hitAwayScore = ($prediction->away_score_prediction == $match->away_team_score);

                if ($hitWinner && $hitHomeScore && $hitAwayScore) {
                    $earnedPoints = 8;
                    $status = 'correct';
                } elseif ($hitHomeScore && $hitAwayScore) {
                    $earnedPoints = 6;
                    $status = 'correct';
                } elseif ($hitWinner && ($hitHomeScore || $hitAwayScore)) {
                    $earnedPoints = 4;
                    $status = 'correct';
                } elseif ($hitHomeScore || $hitAwayScore) {
                    $earnedPoints = 2;
                    $status = 'correct';
                } elseif ($hitWinner) {
                    $earnedPoints = 1;
                    $status = 'correct';
                }

                $prediction->update([
                    'points' => $earnedPoints,
                    'status' => $status,
                ]);
            }
          }
          
        return response()->json([
            'message' => 'Match updated successfully',
            'match' => $match
        ], 200);
    }
    
    #[OA\Delete(
        path: "/api/matches/{id}",
        tags: ["Partidos"],
        summary: "Eliminar un partido de la base de datos (Solo Administradores)",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\Response(response: 200, description: "Partido purgado con éxito")]
    public function destroy(string $id): JsonResponse
    {
        $match = SportMatch::findOrFail($id);

        $match->delete();

        return response()->json([
            'message' => 'Match deleted successfully'
        ], 200);
    }
}
