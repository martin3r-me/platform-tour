<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft | active
            $table->string('share_token', 64)->nullable()->unique();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tour_steps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tour_id')->index();
            $table->integer('position')->default(1);
            $table->string('navigate_url')->nullable();       // z.B. /encounter/appointments/9
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('highlight_selector')->nullable(); // optionaler CSS-Selektor (später)
            $table->timestamps();
        });

        Schema::create('tour_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->integer('current_position')->default(1);
            $table->string('status')->default('running'); // running | done
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_runs');
        Schema::dropIfExists('tour_steps');
        Schema::dropIfExists('tours');
    }
};
