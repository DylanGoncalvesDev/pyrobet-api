<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    //
    public function index(): JsonResponse
    {
        $teams = Team::all();

        return response()->json([
           'teams' => $teams
        ]);  
    }
}
