<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 30);
            $table->unsignedSmallInteger('year');
            $table->string('prefix', 20);
            $table->unsignedBigInteger('next_number')->default(1);
            $table->timestamps();
            $table->unique(['document_type', 'year']);
        });

        $year = (int) now()->format('Y');
        $now = now();
        DB::table('document_sequences')->insert(array_map(
            fn ($type, $prefix) => ['document_type' => $type, 'year' => $year, 'prefix' => $prefix, 'next_number' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['quotation', 'invoice', 'purchase', 'delivery_challan', 'customer_payment', 'supplier_payment', 'stock_adjustment'],
            ['QTN', 'INV', 'PUR', 'DC', 'RCPT', 'SPAY', 'ADJ'],
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
