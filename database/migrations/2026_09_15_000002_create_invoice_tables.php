<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->nullable();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('quotation_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->date('invoice_date');
            $table->string('reference')->nullable();
            $table->string('summary')->nullable();
            $table->string('status')->default('draft');
            $table->string('currency', 3)->default('PKR');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_total', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('actual_cost_total', 18, 2)->default(0);
            $table->decimal('actual_profit', 18, 2)->default(0);
            $table->decimal('actual_margin_percent', 8, 3)->default(0);
            $table->string('fbr_status')->nullable();
            $table->string('fbr_invoice_number')->nullable();
            $table->string('fbr_uuid')->nullable();
            $table->timestamp('fbr_submission_date')->nullable();
            $table->json('fbr_response')->nullable();
            $table->longText('terms')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('line_no');
            $table->text('description');
            $table->decimal('quantity', 18, 4);
            $table->string('unit', 30)->default('Unit');
            $table->decimal('actual_cost_unit', 18, 2)->default(0);
            $table->decimal('selling_price_unit', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('tax_rate', 8, 3)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('cost_total', 18, 2)->default(0);
            $table->decimal('actual_profit', 18, 2)->default(0);
            $table->decimal('actual_margin_percent', 8, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
