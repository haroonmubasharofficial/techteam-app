<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); $table->string('company_name'); $table->string('contact_person')->nullable(); $table->text('address')->nullable(); $table->string('city')->nullable(); $table->string('phone')->nullable(); $table->string('mobile')->nullable(); $table->string('email')->nullable(); $table->string('ntn')->nullable(); $table->string('strn')->nullable(); $table->string('payment_terms')->nullable(); $table->decimal('credit_limit', 18, 2)->default(0); $table->string('tax_treatment')->nullable(); $table->boolean('is_active')->default(true); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('product_categories', function (Blueprint $table) { $table->id(); $table->string('name')->unique(); $table->timestamps(); });
        Schema::create('units', function (Blueprint $table) { $table->id(); $table->string('name')->unique(); $table->string('symbol', 20)->nullable(); $table->timestamps(); });
        Schema::create('products', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->nullable()->unique(); $table->string('name'); $table->text('description')->nullable(); $table->enum('item_type',['product','service'])->default('product'); $table->string('brand')->nullable(); $table->string('model')->nullable(); $table->string('warranty')->nullable(); $table->decimal('purchase_cost',18,2)->default(0); $table->decimal('default_selling_price',18,2)->default(0); $table->decimal('tax_rate',8,3)->default(0); $table->boolean('is_active')->default(true); $table->boolean('track_serial')->default(false); $table->timestamps();
        });
        Schema::create('quotations', function (Blueprint $table) {
            $table->id(); $table->string('quotation_number')->unique()->nullable(); $table->foreignId('customer_id')->constrained()->restrictOnDelete(); $table->date('quote_date'); $table->date('valid_until')->nullable(); $table->string('reference')->nullable(); $table->string('project_name')->nullable(); $table->string('summary')->nullable(); $table->string('status')->default('draft'); $table->string('currency',3)->default('PKR'); $table->decimal('subtotal',18,2)->default(0); $table->decimal('discount_total',18,2)->default(0); $table->decimal('tax_total',18,2)->default(0); $table->decimal('total_amount',18,2)->default(0); $table->decimal('estimated_cost_total',18,2)->default(0); $table->decimal('estimated_profit',18,2)->default(0); $table->decimal('estimated_margin_percent',8,3)->default(0); $table->longText('terms')->nullable(); $table->timestamps();
        });
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('quotation_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); $table->unsignedTinyInteger('line_no'); $table->text('description'); $table->decimal('quantity',18,4); $table->string('unit',30)->default('Unit'); $table->decimal('purchase_cost',18,2)->default(0); $table->decimal('delivery_cost',18,2)->default(0); $table->decimal('other_cost',18,2)->default(0); $table->decimal('selling_price',18,2)->default(0); $table->decimal('discount',18,2)->default(0); $table->decimal('tax_rate',8,3)->default(0); $table->decimal('tax_amount',18,2)->default(0); $table->decimal('estimated_cost_total',18,2)->default(0); $table->decimal('estimated_profit',18,2)->default(0); $table->decimal('estimated_margin_percent',8,3)->default(0); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('quotation_items'); Schema::dropIfExists('quotations'); Schema::dropIfExists('products'); Schema::dropIfExists('units'); Schema::dropIfExists('product_categories'); Schema::dropIfExists('customers'); }
};
