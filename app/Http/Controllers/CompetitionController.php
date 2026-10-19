<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "PyroBet API Documentation",
    description: "Documentación oficial e interactiva de los endpoints REST de PyroBet en Laravel 13"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor Local de Desarrollo"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Pega tu Bearer Token obtenido en el Login para interactuar con las rutas"
)]
#[OA\PathItem(path: "/api")] 
class CompetitionController extends Controller
{
    #[OA\Post(
        path: "/api/competitions",
        tags: ["Competiciones"],
        summary: "Crear una nueva competición (Solo Administradores)",
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "status", "start_date", "end_date"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Champions League"),
                new OA\Property(property: "description", type: "string", example: "Torneo europeo"),
                new OA\Property(property: "status", type: "string", enum: ["not_started", "in_progress", "finished"], example: "not_started"),
                new OA\Property(property: "start_date", type: "string", example: "2026-08-25 00:00:00"),
                new OA\Property(property: "end_date", type: "string", example: "2027-07-15 00:00:00")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Competición creada con éxito")]
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

     #[OA\Get(
        path: "/api/competitions",
        tags: ["Competiciones"],
        summary: "Listar todas las competiciones",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Colección de competiciones devuelta con éxito")]
    public function index(): JsonResponse
    {
       $competitions = Competition::all();

       return response()->json([
           'competitions' => $competitions
       ]);  
    }
    
     #[OA\Put(
        path: "/api/competitions/{id}",
        tags: ["Competiciones"],
        summary: "Actualizar una competición existente (Solo Administradores)",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "status", "start_date", "end_date"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "La Liga EA Sports"),
                new OA\Property(property: "status", type: "string", enum: ["not_started", "in_progress", "finished"], example: "in_progress"),
                new OA\Property(property: "start_date", type: "string", example: "2026-08-25 00:00:00"),
                new OA\Property(property: "end_date", type: "string", example: "2027-07-15 00:00:00")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Competición modificada correctamente")]
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
    
    #[OA\Delete(
        path: "/api/competitions/{id}",
        tags: ["Competiciones"],
        summary: "Eliminar una competición (Solo Administradores)",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\Response(response: 200, description: "Competición eliminada correctamente")]
    public function destroy(string $id): JsonResponse
    {
       $competition = Competition::findOrFail($id);

       $competition->delete();

       return response()->json([
          'message' => 'Competition deleted successfully',
       ]);
    }
}
