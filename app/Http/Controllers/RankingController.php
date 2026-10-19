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
        summary: "Consultar la tabla de posiciones global de PyroBet",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Ranking calculado de mayor a menor con éxito")]
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
