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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // CASH, CARD, OM, MOMO
            $table->string('label');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('users');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->foreignId('payment_method_id')->constrained();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('CFA');

            $table->enum('status', ['PENDING', 'CONFIRMED', 'FAILED', 'CANCELLED'])->default('PENDING');

            $table->string('external_reference')->nullable(); // Stripe payment_intent_id
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_number')->unique();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users');

            $table->decimal('total_amount', 10, 2);
            $table->foreignId('generated_by')->constrained('users');

            $table->timestamp('generated_at');

            $table->timestamps();
        });

        Schema::create('receipt_periods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receipt_id')->constrained()->onDelete('cascade');

            $table->date('period_start');
            $table->date('period_end');

            $table->timestamps();
        });

        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();

            $table->string('provider'); // cinetpay
            $table->string('event_type');
            $table->string('external_event_id')->unique();

            $table->json('payload');

            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->unique(['provider','external_event_id']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('receipt_periods');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
    }
};
