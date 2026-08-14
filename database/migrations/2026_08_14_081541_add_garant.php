<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('numero')->nullable()->unique()->after('id');
            $table->string('nature_bail')->nullable()->after('numero'); // ex: habitation, commercial...
            $table->date('deposit_due_date')->nullable()->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['numero', 'nature_bail', 'deposit_due_date']);
        });
    }
};
