<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class RankingController extends Controller
{
    //
    public function index(): JsonResponse
    {
        $ranking = User::select('users.id', 'users.name')
            ->join('predictions', 'users.id', '=', 'predictions.user_id')
            ->selectRaw('SUM(predictions.points) as total_points')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_points', 'desc')
            ->get();

        return response()->json([
            'message' => 'Global ranking retrieved successfully',
            'ranking' => $ranking
        ], 200);
    }
}
