<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PredictionsController extends Controller
{
    //
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

        $prediction = Prediction::create($validatedData);

        return response()->json([
            'message' => 'Prediction created successfully',
            'prediction' => $prediction
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $predictions = Prediction::where('user_id', $request->user()->id)->with('match')->get();

        return response()->json([
            'message' => 'Predictions retrieved successfully',
            'predictions' => $predictions
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $prediction = Prediction::where('user_id', $request->user()->id)->findOrFail($id);

        $validatedData = $request->validate([
            'prediction' => 'required|string|in:home,draw,away',
            'home_score_prediction' => 'required|integer|min:0',
            'away_score_prediction' => 'required|integer|min:0',
        ]);

        $prediction->update($validatedData);

        return response()->json([
            'message' => 'Prediction updated successfully',
            'prediction' => $prediction
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $prediction = Prediction::findOrFail($id);

        $prediction->delete();

        return response()->json([
            'message' => 'Prediction deleted successfully'
        ], 200);
    }
}
