<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Team::class, 'home_team_id')->nullable()->constrained('teams')->cascadeOnDelete();
            $table->foreignIdFor(Team::class, 'away_team_id')->nullable()->constrained('teams')->cascadeOnDelete();

            $table->integer('home_team_score')->nullable();
            $table->integer('away_team_score')->nullable();

            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();

            $table->integer('round')->default(1);
            $table->boolean('is_final')->default(false);

            $table->enum('winner', ['home', 'away'])->nullable();

            $table->integer('sort')->nullable();

            $table->boolean('stakeless')->default(false);
            
            $table->integer('elimination_round')->nullable();
            $table->integer('elimination_level')->nullable();

            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
