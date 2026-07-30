<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrat_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('cni'); // cni, autre...
            $table->string('file_path');
            $table->string('original_name');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->enum('status', ['en_attente', 'valide', 'refuse'])->default('en_attente');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrat_documents');
    }
};
