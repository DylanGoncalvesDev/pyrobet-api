<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TeamsController extends Controller
{
    #[OA\Post(
        path: "/api/teams",
        tags: ["Equipos"],
        summary: "Registrar un nuevo equipo (Solo Administradores)",
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
           mediaType: "multipart/form-data",
           schema: new OA\Schema(
               type: "object",
               required: ["name", "country", "founded_at", "type", "sport"],
               properties: [
                  new OA\Property(property: "name", type: "string", example: "Real Madrid"),
                  new OA\Property(property: "logo", type: "string", format: "binary", nullable: true),
                  new OA\Property(property: "country", type: "string", example: "España"),
                  new OA\Property(property: "founded_at", type: "integer", example: 1902),
                  new OA\Property(property: "type", type: "string", enum: ["national", "club"], example: "club"),
                  new OA\Property(property: "sport", type: "string", example: "soccer football")
               ]
           )
        )
    )]
    #[OA\Response(response: 201, description: "Equipo guardado con éxito")]
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => [
             'required',
             'string',
             'max:255',
             'unique:teams',
             'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\_]+$/'
            ],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'country'  => 'required|string|max:100',
            'founded_at'  => 'required|integer|min:1800|max:' . date('Y'),
            'type'  => 'required|in:national,club',
            'sport' => 'required|in:american football,soccer football,rugby,basketball,baseball,cricket,softball,volleyball,hockey,handball,futsal'
        ]);

        if ($request->hasFile('logo')) {
            $validatedData['logo'] = $request->file('logo')->store('teams', 'public');
        }

        $team = Team::create($validatedData);

        return response()->json([
           'message' => 'Team created successfully',
           'team'  => $team
        ], 201);
    }
    
    #[OA\Get(
        path: "/api/teams",
        tags: ["Equipos"],
        summary: "Listar todos los equipos registrados",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Listado devuelto exitosamente")]
    public function index(): JsonResponse
    {
        $teams = Team::all();

        return response()->json([
           'teams' => $teams
        ], 200);  
    }
     
    #[OA\Post(
      path: "/api/teams/{id}",
      tags: ["Equipos"],
      summary: "Actualizar un equipo (POST con _method=PUT)",
      security: [["bearerAuth" => []]],
      parameters: [
         new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "string")
        )
    ]
   )]
#[OA\RequestBody(
    required: true,
    content: new OA\MediaType(
        mediaType: "multipart/form-data",
        schema: new OA\Schema(
            type: "object",
            required: ["_method", "name", "country", "founded_at", "type", "sport"],
            properties: [
                new OA\Property(property: "_method", type: "string", enum: ["PUT"], example: "PUT"),
                new OA\Property(property: "name", type: "string", example: "FC Barcelona"),
                new OA\Property(property: "logo", type: "string", format: "binary", nullable: true),
                new OA\Property(property: "country", type: "string", example: "España"),
                new OA\Property(property: "founded_at", type: "integer", example: 1899),
                new OA\Property(property: "type", type: "string", enum: ["national", "club"], example: "club"),
                new OA\Property(property: "sport", type: "string", example: "soccer football")
            ]
        )
    )
)]
    #[OA\Response(response: 200, description: "Equipo mutado con éxito")]
    public function update(Request $request, string $id ): JsonResponse
    {
        $team = Team::findOrFail($id);

        $validatedData = $request->validate([
            'name' => [
             'required',
             'string',
             'max:255',
             'unique:teams,name,' . $id,
             'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\_]+$/'
            ],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'country'  => 'required|string|max:100',
            'founded_at'  => 'required|integer|min:1800|max:' . date('Y'),
            'type'  => 'required|in:national,club',
            'sport' => 'required|in:american football,soccer football,rugby,basketball,baseball,cricket,softball,volleyball,hockey,handball,futsal'
        ]);

        $oldLogo = $team->logo;

        if ($request->hasFile('logo')) {
            $newLogoPath = $request->file('logo')->store('teams', 'public');

        if (!$newLogoPath) {
            return response()->json([
                'message' => 'Unable to store team logo.'
            ], 500);
        }

        $validatedData['logo'] = $newLogoPath;
        } else {
           unset($validatedData['logo']);
        }

        $team->update($validatedData);

        if ($request->hasFile('logo') && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return response()->json([
           'message' => 'Team updated successfully',
           'team' => $team
        ], 200);  
    }
    
    #[OA\Delete(
        path: "/api/teams/{id}",
        tags: ["Equipos"],
        summary: "Remover un equipo de la persistencia (Solo Administradores)",
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    )]
    #[OA\Response(response: 200, description: "Equipo removido exitosamente")]
    public function destroy(string $id): JsonResponse
    {
       $team = Team::findOrFail($id);

       $logoPath = $team->logo;

       $team->delete();

       if ($logoPath) {
           Storage::disk('public')->delete($logoPath);
       }

       return response()->json([
           'message' => 'Team deleted successfully',
       ]);
    }
}
