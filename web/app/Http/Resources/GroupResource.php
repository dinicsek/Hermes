<?php

namespace App\Http\Resources;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Group */
class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_generated' => $this->is_generated,
            'round' => $this->round,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
