<?php

namespace App\Models;

use App\Models\Enums\TournamentMatchWinner;
use App\Models\Traits\HasEventStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\SortableTrait;

// This can't just be named Match because it's a reserved word in PHP
class TournamentMatch extends Model
{
    use SoftDeletes, HasFactory, HasEventStatus, SortableTrait;

    public $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];
    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'started_at',
        'ended_at',
        'round',
        'is_final',
        'winner',
        'sort',
        'stakeless',
        'group_id',
        'elimination_round',
        'elimination_level',
        'tournament_id',
    ];
    protected $with = [
        'tournament'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
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

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $this->calculateEventStatus(Carbon::make($attributes['started_at']), Carbon::make($attributes['ended_at']))
        );
    }
}
