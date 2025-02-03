<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date_of_beginning');
            $table->integer('number_of_sessions');
            $table->integer('days_per_session');
            $table->integer('number_of_instructors');
            $table->integer('number_of_assistants');
            $table->integer('number_of_groups');
            $table->decimal('fees', 8, 2);
            $table->decimal('insurance', 8, 2);
            $table->text('session_dates')->nullable();
            $table->timestamps();
        });
        
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->boolean('volunteer')->default(false);
            $table->decimal('pocket', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
        Schema::dropIfExists('students');
    }
};
