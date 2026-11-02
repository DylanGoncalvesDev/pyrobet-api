<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
  #[OA\Get(
    path: "/api/admin/statistics",
    tags: ["Administración"],
    summary: "Obtener estadísticas del panel de administración",
    description: "Devuelve métricas generales y el número de predicciones por partido.",
    security: [["bearerAuth" => []]]
  )]
#[OA\Response(
    response: 200,
    description: "Estadísticas obtenidas correctamente",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "metrics",
                type: "object",
                properties: [
                    new OA\Property(property: "total_predictions", type: "integer", example: 250),
                    new OA\Property(property: "total_users", type: "integer", example: 35),
                    new OA\Property(property: "total_matches", type: "integer", example: 20),
                    new OA\Property(property: "total_teams", type: "integer", example: 32),
                    new OA\Property(property: "total_competitions", type: "integer", example: 3)
                ]
            ),
            new OA\Property(
                property: "match_statistics",
                type: "array",
                items: new OA\Items(
                    type: "object",
                    properties: [
                        new OA\Property(property: "match_id", type: "integer", example: 1),
                        new OA\Property(property: "predictions_count", type: "integer", example: 18)
                    ]
                )
            )
        ]
    )
)]
#[OA\Response(response: 401, description: "Usuario no autenticado")]
#[OA\Response(response: 403, description: "Acceso exclusivo para administradores")]
    public function statistics(): JsonResponse
    {
        $metrics = [
            'total_predictions' => DB::table('predictions')->count(),
            'total_users' => DB::table('users')->count(),
            'total_matches' => DB::table('matches')->count(),
            'total_teams' => DB::table('teams')->count(),
            'total_competitions' => DB::table('competitions')->count()
        ];

        $matchStatistics = DB::table('matches')
            ->leftJoin('predictions', 'matches.id', '=', 'predictions.match_id')
            ->select('matches.id as match_id')
            ->selectRaw('COUNT(predictions.id) as predictions_count')
            ->groupBy('matches.id')
            ->get();

        return response()->json([
            'metrics' => $metrics,
            'match_statistics' => $matchStatistics
        ]);
    }
}
