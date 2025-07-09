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
        Schema::create('senha_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->integer('numero');
            $table->integer('id_user_criador');
            $table->integer('id_user_resolvedor')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senha_tickets');
    }
};
