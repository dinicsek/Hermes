<?php

namespace App\Http\Resources;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Tournament */
class TournamentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'registration_starts_at' => $this->registration_starts_at,
            'registration_ends_at' => $this->registration_ends_at,
            'starts_at' => $this->starts_at,
            'ended' => $this->ended,
            'max_team_size' => $this->max_team_size,
            'end_when_matches_concluded' => $this->end_when_matches_concluded,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
