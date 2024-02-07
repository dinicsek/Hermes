<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'members',
        'emails',
        'tournament_id',
    ];

    protected $casts = [
        'members' => 'array',
        'emails' => 'array',
    ];

    protected $with = [
        'tournament'
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'away_team_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'home_team_id')->orWhere('away_team_id', $this->id);
    }

    public function groups(): Builder
    {
        return $this->groupsAsHomeTeam()->getQuery()->union($this->groupsAsAwayTeam()->getQuery())->distinct();
    }

    public function groupsAsHomeTeam(): HasManyThrough
    {
        return $this->hasManyThrough(Group::class, TournamentMatch::class, 'home_team_id', 'group_id', 'id', 'id');
    }

    public function groupsAsAwayTeam(): HasManyThrough
    {
        return $this->hasManyThrough(Group::class, TournamentMatch::class, 'away_team_id', 'group_id', 'id', 'id');
    }
}
