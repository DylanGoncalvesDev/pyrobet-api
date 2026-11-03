<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class RankingController extends Controller
{
   #[OA\Get(
    path: "/api/ranking",
    tags: ["Ranking"],
    summary: "Consultar la clasificación global de PyroBet",
    description: "Returns all regular users ordered by total points in descending order. Users without predictions are included with zero points. Administrator accounts are excluded.",
    security: [["bearerAuth" => []]]
   )]
   #[OA\Response(
    response: 200,
    description: "Global ranking retrieved successfully."
   )]
    public function index(): JsonResponse
    {
      $ranking = User::query()->where('users.role', 'user')
                         ->leftJoin('predictions', 'users.id', '=', 'predictions.user_id')
                         ->select('users.id', 'users.name')
                         ->selectRaw('COALESCE(SUM(predictions.points), 0) as total_points')
                         ->groupBy('users.id', 'users.name')
                         ->orderByDesc('total_points')
                         ->get();

    return response()->json([
        'message' => 'Global ranking retrieved successfully',
        'ranking' => $ranking
    ], 200);
}
}
