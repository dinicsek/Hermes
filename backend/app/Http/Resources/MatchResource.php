<?php

namespace App\Http\Resources;

use App\Models\TournamentMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TournamentMatch */
class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'round' => $this->round,
            'is_final' => $this->is_final,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
