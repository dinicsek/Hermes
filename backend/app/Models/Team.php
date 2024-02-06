<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeMatches()
    {
        return $this->hasMany(TournamentMatch::class, 'home_team_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(TournamentMatch::class, 'away_team_id');
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class, 'home_team_id')->orWhere('away_team_id', $this->id);
    }
}
