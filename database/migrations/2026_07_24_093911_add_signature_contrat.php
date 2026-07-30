<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            // Signature electronique (Documenso)
            $table->string('documenso_envelope_id')->nullable()->unique()->after('status');
            $table->enum('signature_status', ['non_envoye', 'en_attente', 'signe', 'refuse'])
                ->default('non_envoye')->after('documenso_envelope_id');
            $table->string('signed_pdf_path')->nullable()->after('signature_status');
            $table->timestamp('sent_for_signature_at')->nullable()->after('signed_pdf_path');

            // Piece jointe locataire (ex: CNI) requise avant signature
            $table->enum('document_status', ['non_demande', 'demande', 'soumis', 'valide', 'refuse'])
                ->default('non_demande')->after('sent_for_signature_at');
            $table->timestamp('document_requested_at')->nullable()->after('document_status');
            $table->foreignId('document_requested_by')->nullable()->constrained('users')->nullOnDelete()->after('document_requested_at');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->string('documenso_envelope_id')->nullable()->unique()->after('generated_by');
            $table->enum('signature_status', ['non_envoye', 'en_attente', 'signe', 'refuse'])
                ->default('non_envoye')->after('documenso_envelope_id');
            $table->string('signed_pdf_path')->nullable()->after('signature_status');
            $table->timestamp('sent_for_signature_at')->nullable()->after('signed_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_requested_by');
            $table->dropColumn([
                'documenso_envelope_id', 'signature_status', 'signed_pdf_path', 'sent_for_signature_at',
                'document_status', 'document_requested_at',
            ]);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn([
                'documenso_envelope_id', 'signature_status', 'signed_pdf_path', 'sent_for_signature_at',
            ]);
        });
    }
};
