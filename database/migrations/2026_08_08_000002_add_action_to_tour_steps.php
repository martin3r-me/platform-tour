<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_steps', function (Blueprint $table) {
            // Aktiver Schritt: führt beim "Weiter" ein Tool im Kontext des Zuschauers aus.
            $table->string('action_tool')->nullable()->after('highlight_selector');
            $table->json('action_arguments')->nullable()->after('action_tool');
        });
    }

    public function down(): void
    {
        Schema::table('tour_steps', function (Blueprint $table) {
            $table->dropColumn(['action_tool', 'action_arguments']);
        });
    }
};
