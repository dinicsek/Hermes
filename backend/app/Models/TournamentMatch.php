<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// This can't just be named Match because it's a reserved word in PHP
class TournamentMatch extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'status',
        'round',
        'is_final',
        'winner',
        'sort',
        'tournament_id',
    ];

    protected $with = [
        'tournament'
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

}
