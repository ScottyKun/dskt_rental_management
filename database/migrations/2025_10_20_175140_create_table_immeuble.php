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
        Schema::create('immeubles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('town');
            $table->text('description')->nullable();
            $table->integer('nb_apartments')->default(0);
            $table->integer('nb_available')->default(0);
            $table->integer('nb_occupied')->default(0);
            $table->enum('status', ['actif', 'inactif', 'en_maintenance'])->default('actif');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->timestamps();


            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immeubles');
    }
};
