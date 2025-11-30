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
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();

            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('rent_amount', 8, 2);
            $table->integer('rent_payment_day')->default(1);
            $table->decimal('deposit_amount', 10, 2)->nullable();

            $table->enum('status', ['actif', 'expiré', 'résilié'])->default('actif');

            // Foreign Keys
            $table->foreignId('tenant_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('appartement_id')
                  ->constrained('appartements')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
