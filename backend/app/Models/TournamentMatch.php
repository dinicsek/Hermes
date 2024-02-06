<?php

namespace App\Models;

use App\Models\Enums\TournamentMatchStatus;
use App\Models\Enums\TournamentMatchWinner;
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
        'home_team_score',
        'away_team_score',
        'status',
        'round',
        'is_final',
        'winner',
        'sort',
        'stakeless',
        'group',
        'is_advancing',
        'tournament_id',
    ];

    protected $with = [
        'tournament'
    ];

    protected $casts = [
        'status' => TournamentMatchStatus::class,
        'winner' => TournamentMatchWinner::class,
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
