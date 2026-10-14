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
            'match_id'              => 'required|exists:matches,id',
            'prediction'            => 'required|string|in:home,draw,away',
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
}
