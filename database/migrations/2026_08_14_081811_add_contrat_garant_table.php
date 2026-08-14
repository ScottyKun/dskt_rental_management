<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrat_garants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('cni_number')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('lieu_residence')->nullable();
            $table->string('profession')->nullable();
            $table->timestamps();

            $table->unique('contrat_id'); // un seul garant par contrat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrat_garants');
    }
};
