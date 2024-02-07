<?php

namespace App\Models;

use App\Models\Data\RoundSetting;
use App\Models\Traits\HasEventStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelData\DataCollection;

class Tournament extends Model
{
    use SoftDeletes, HasFactory, HasEventStatus;

    protected $fillable = [
        'name',
        'description',
        'code',
        'registration_starts_at',
        'registration_ends_at',
        'starts_at',
        'ended_at',
        'max_team_size',
        'round_settings',
        'end_when_matches_concluded',
        'user_id'
    ];

    protected $casts = [
        'registration_starts_at' => 'datetime',
        'registration_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ended_at' => 'datetime',
        'round_settings' => DataCollection::class . ':' . RoundSetting::class,
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

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $this->calculateEventStatus(Carbon::make($attributes['starts_at']), Carbon::make($attributes['ended_at']))
        );
    }
}
