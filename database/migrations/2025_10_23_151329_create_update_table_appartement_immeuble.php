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
        Schema::create('appartements', function (Blueprint $table) {
           $table->id();
            $table->string('name'); // nom ou numéro de l’appartement
            $table->text('description')->nullable();
            $table->string('type'); // T1, T2, Studio, Duplex
            $table->enum('status', ['disponible', 'occupe', 'en_renovation']);
            $table->decimal('area', 8, 2)->nullable(); // surface en m²
            $table->unsignedBigInteger('immeuble_id');
            $table->unsignedBigInteger('locataire_id')->nullable(); // peut être null si pas encore loué
            $table->timestamps();

            // Foreign keys
            $table->foreign('immeuble_id')->references('id')->on('immeubles')->onDelete('cascade');
            $table->foreign('locataire_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appartements');
    }
};
