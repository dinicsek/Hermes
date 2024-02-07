<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_generated',
        'round',
    ];

    protected $with = [
        'tournament'
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
