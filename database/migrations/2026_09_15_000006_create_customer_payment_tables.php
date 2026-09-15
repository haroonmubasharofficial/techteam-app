<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 30)->unique()->nullable();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('payment_date');
            $table->string('payment_method', 30)->default('Cash');
            $table->string('reference', 100)->nullable();
            $table->decimal('amount', 18, 2);
            $table->string('status', 20)->default('posted');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'payment_date']);
        });

        Schema::create('customer_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_payment_id')->constrained('customer_payments')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->decimal('amount', 18, 2);
            $table->timestamps();
            $table->unique(['customer_payment_id', 'invoice_id']);
        });

        Schema::create('party_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->date('entry_date');
            $table->string('entry_type', 30);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->string('description', 255)->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'entry_date']);
            $table->index(['supplier_id', 'entry_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('party_ledger_entries');
        Schema::dropIfExists('customer_payment_allocations');
        Schema::dropIfExists('customer_payments');
    }
};
