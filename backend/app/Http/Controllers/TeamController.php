<?php

namespace App\Http\Controllers;

use App\Events\TeamApprovedEvent;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function store(Tournament $tournament, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array|max:' . $tournament->max_team_size . '|min:' . $tournament->min_team_size,
            'members.*' => 'required|string|max:255|distinct',
            'email' => 'array|max:' . $tournament->max_team_size,
            'email.*' => 'required|email|distinct',
        ]);

        if ($tournament->max_approved_teams !== null && $tournament->max_approved_teams <= $tournament->teams()->whereIsApproved(true)->count()) {
            return response()->json([
                'message' => 'Ez a verseny sajnos már megtelt.'
            ], 400);
        }

        $team = $tournament->teams()->create([
            'name' => $request->input('name'),
            'members' => $request->input('members'),
            'emails' => $request->input('email') ?? [],
            'is_approved' => $tournament->approve_by_default,
        ]);

        if ($team->is_approved) {
            TeamApprovedEvent::dispatch($team);
        }

        return response()->json([
            'data' => [
                'id' => $team->id,
                'name' => $team->name,
                'members' => $team->members,
                'emails' => $team->emails,
                'is_approved' => $team->is_approved,
                'created_at' => $team->created_at,
                'updated_at' => $team->updated_at,
            ]
        ], 201);
    }
}
