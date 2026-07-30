<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('signed_pdf_sha256', 64)->nullable()->after('signed_pdf_path');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->string('signed_pdf_sha256', 64)->nullable()->after('signed_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('signed_pdf_sha256');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('signed_pdf_sha256');
        });
    }
};
