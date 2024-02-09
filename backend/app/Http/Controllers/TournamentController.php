<?php

namespace App\Http\Controllers;

use App\Models\Tournament;

class TournamentController extends Controller
{
    public function show(Tournament $tournament)
    {
        return response()->json([
            'data' => [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'description' => $tournament->description,
                'registration_start' => $tournament->registration_starts_at,
                'registration_end' => $tournament->registration_ends_at,
                'starts_at' => $tournament->starts_at,
                'ended_at' => $tournament->ended_at,
                'status' => $tournament->status,
                'max_teams' => $tournament->max_teams,
                'min_team_size' => $tournament->min_team_size,
                'max_team_size' => $tournament->max_team_size,
                'can_create_team' => $tournament->max_teams === null || $tournament->max_teams > $tournament->teams()->count(),
            ]
        ]);
    }
}
