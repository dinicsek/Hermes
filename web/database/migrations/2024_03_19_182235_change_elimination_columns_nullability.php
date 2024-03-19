<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('tournament_matches')->whereNull('elimination_round')->update(['elimination_round' => 1]);
        DB::table('tournament_matches')->whereNull('elimination_level')->update(['elimination_level' => 1]);

        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->integer('elimination_round')->default(1)->nullable(false)->change();
            $table->integer('elimination_level')->default(1)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->integer('elimination_round')->nullable()->change();
            $table->integer('elimination_level')->nullable()->change();
        });
    }
};
