<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id(); $table->string('company_name'); $table->string('contact_person')->nullable(); $table->text('address')->nullable(); $table->string('phone')->nullable(); $table->string('email')->nullable(); $table->string('ntn')->nullable(); $table->string('strn')->nullable(); $table->string('payment_terms')->nullable(); $table->boolean('is_active')->default(true); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id(); $table->string('name')->unique(); $table->text('address')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('purchases', function (Blueprint $table) {
            $table->id(); $table->string('purchase_number')->unique()->nullable(); $table->foreignId('supplier_id')->constrained()->restrictOnDelete(); $table->foreignId('warehouse_id')->constrained()->restrictOnDelete(); $table->date('purchase_date'); $table->string('supplier_invoice_number')->nullable(); $table->string('reference')->nullable(); $table->string('status')->default('received'); $table->string('currency',3)->default('PKR'); $table->decimal('subtotal',18,2)->default(0); $table->decimal('tax_total',18,2)->default(0); $table->decimal('total_amount',18,2)->default(0); $table->longText('notes')->nullable(); $table->timestamps();
        });
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('purchase_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); $table->string('description'); $table->decimal('quantity',18,4); $table->string('unit',30)->default('Unit'); $table->decimal('unit_cost',18,2)->default(0); $table->decimal('tax_rate',8,3)->default(0); $table->decimal('tax_amount',18,2)->default(0); $table->decimal('total_cost',18,2)->default(0); $table->string('serial_number')->nullable(); $table->timestamps();
        });
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id(); $table->foreignId('warehouse_id')->constrained()->restrictOnDelete(); $table->foreignId('product_id')->constrained()->restrictOnDelete(); $table->foreignId('purchase_item_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete(); $table->string('transaction_type'); $table->dateTime('transaction_date'); $table->decimal('quantity_in',18,4)->default(0); $table->decimal('quantity_out',18,4)->default(0); $table->decimal('unit_cost',18,2)->default(0); $table->string('reference')->nullable(); $table->text('notes')->nullable(); $table->timestamps();
            $table->index(['warehouse_id','product_id','transaction_date']);
        });
        DB::table('warehouses')->insert(['name'=>'Main Warehouse','is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions'); Schema::dropIfExists('purchase_items'); Schema::dropIfExists('purchases'); Schema::dropIfExists('warehouses'); Schema::dropIfExists('suppliers');
    }
};
