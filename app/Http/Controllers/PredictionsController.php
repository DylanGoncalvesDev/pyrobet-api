<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\SportMatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class PredictionsController extends Controller
{
    #[OA\Post(
        path: "/api/predictions",
        tags: ["Predicciones"],
        summary: "Registrar una nueva predicción",
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["match_id", "prediction", "home_score_prediction", "away_score_prediction"],
            properties: [
                new OA\Property(property: "match_id", type: "integer", example: 1),
                new OA\Property(property: "prediction", type: "string", enum: ["home", "draw", "away"], example: "home"),
                new OA\Property(property: "home_score_prediction", type: "integer", example: 2),
                new OA\Property(property: "away_score_prediction", type: "integer", example: 1)
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Predicción creada con éxito")]
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'prediction' => 'required|string|in:home,draw,away',
            'home_score_prediction' => 'required|integer|min:0',
            'away_score_prediction' => 'required|integer|min:0',
        ]);

        $validatedData['user_id'] = $request->user()->id;
        $validatedData['status'] = 'pending';
        $validatedData['points'] = 0;

        $predict = $validatedData['prediction'];
        $homeScore = (int)$validatedData['home_score_prediction'];
        $awayScore = (int)$validatedData['away_score_prediction'];

        if ($predict === 'home' && $homeScore <= $awayScore) {
            return response()->json([
               'message' => 'Contradictory values: If prediction is [home], home score must be greater than away score.'
            ], 422);
        }

        if ($predict === 'away' && $awayScore <= $homeScore) {
            return response()->json([
                'message' => 'Contradictory values: If prediction is [away], away score must be greater than home score.'
            ], 422);
        }

        if ($predict === 'draw' && $homeScore !== $awayScore) {
            return response()->json([
                  'message' => 'Contradictory values: If prediction is [draw], both scores must be exactly equal.'
            ], 422);
        }

        $existingPrediction = Prediction::where('user_id', $request->user()->id)
            ->where('match_id', $validatedData['match_id'])
            ->first();

        if ($existingPrediction) {
            return response()->json([
                'message' => 'You have already submitted a prediction for this match. Please update your existing prediction instead.'
            ], 403);
        }

        $match = SportMatch::findOrFail($validatedData['match_id']);
        if ($match->status !== 'upcoming') {
            return response()->json([
                'message' => 'Predictions are closed. This match is already live or finished.'
            ], 403);
        }

        $prediction = Prediction::create($validatedData);

        return response()->json([
            'message' => 'Prediction created successfully',
            'prediction' => $prediction
        ], 201);
    }
    
     #[OA\Get(
        path: "/api/predictions",
        tags: ["Predicciones"],
        summary: "Listar las predicciones del usuario autenticado",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Colección devuelta correctamente")]
    public function index(Request $request): JsonResponse
    {
        $predictions = Prediction::where('user_id', $request->user()->id)->with('match')->get();

        return response()->json([
            'message' => 'Predictions retrieved successfully',
            'predictions' => $predictions
        ], 200);
    }

    #[OA\Put(
        path: "/api/predictions/{id}",
        tags: ["Predicciones"],
        summary: "Modificar un pronóstico propio",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["prediction", "home_score_prediction", "away_score_prediction"],
            properties: [
                new OA\Property(property: "prediction", type: "string", enum: ["home", "draw", "away"], example: "draw"),
                new OA\Property(property: "home_score_prediction", type: "integer", example: 1),
                new OA\Property(property: "away_score_prediction", type: "integer", example: 1)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Modificación transaccional exitosa")]
    public function update(Request $request, string $id): JsonResponse
    {
        $prediction = Prediction::where('user_id', $request->user()->id)->findOrFail($id);

        $validatedData = $request->validate([
            'prediction' => 'required|string|in:home,draw,away',
            'home_score_prediction' => 'required|integer|min:0',
            'away_score_prediction' => 'required|integer|min:0',
        ]);

        $predict = $validatedData['prediction'];
        $homeScore = (int)$validatedData['home_score_prediction'];
        $awayScore = (int)$validatedData['away_score_prediction'];

        if ($predict === 'home' && $homeScore <= $awayScore) {
            return response()->json([
               'message' => 'Contradictory values: If prediction is [home], home score must be greater than away score.'
            ], 422);
        }

        if ($predict === 'away' && $awayScore <= $homeScore) {
            return response()->json([
                'message' => 'Contradictory values: If prediction is [away], away score must be greater than home score.'
            ], 422);
        }

        if ($predict === 'draw' && $homeScore !== $awayScore) {
            return response()->json([
                  'message' => 'Contradictory values: If prediction is [draw], both scores must be exactly equal.'
            ], 422);
        }

        $prediction->update($validatedData);

        return response()->json([
            'message' => 'Prediction updated successfully',
            'prediction' => $prediction
        ], 200);
    }
    
   #[OA\Delete(
        path: "/api/predictions/{id}",
        tags: ["Predicciones"],
        summary: "Eliminar una predicción activa propia",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\Response(response: 200, description: "Registro eliminado")]
    public function destroy(Request $request, string $id): JsonResponse
    {
        $prediction = Prediction::where('user_id', $request->user()->id)->findOrFail($id);

        $prediction->delete();

        return response()->json([
            'message' => 'Prediction deleted successfully'
        ], 200);
    }
}
