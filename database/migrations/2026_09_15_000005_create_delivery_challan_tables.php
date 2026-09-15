<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_number')->unique()->nullable();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->date('challan_date');
            $table->date('delivery_date')->nullable();
            $table->date('shipped_date')->nullable();
            $table->string('delivery_challan_for')->nullable();
            $table->string('shipping_to')->nullable();
            $table->string('status')->default('draft');
            $table->longText('terms')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_challan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_challan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('line_no');
            $table->string('item_name');
            $table->string('serial_number')->nullable();
            $table->decimal('quantity',18,4);
            $table->string('unit',30)->default('Unit');
            $table->string('received_by')->nullable();
            $table->string('received_comment')->nullable();
            $table->date('received_date')->nullable();
            $table->string('delivered_by')->nullable();
            $table->string('delivered_comment')->nullable();
            $table->date('delivered_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_challan_items');
        Schema::dropIfExists('delivery_challans');
    }
};
