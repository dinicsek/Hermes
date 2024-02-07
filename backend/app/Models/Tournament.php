<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'registration_starts_at',
        'registration_ends_at',
        'starts_at',
        'ended_at',
        'max_team_size',
        'end_when_matches_concluded',
        'user_id'
    ];

    protected $casts = [
        'registration_starts_at' => 'datetime',
        'registration_ends_at' => 'datetime',
        'starts_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
