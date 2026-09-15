<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentNumberService
{
    private const PREFIXES = [
        'quotation' => 'QTN', 'invoice' => 'INV', 'purchase' => 'PUR',
        'delivery_challan' => 'DC', 'customer_payment' => 'RCPT',
        'supplier_payment' => 'SPAY', 'stock_adjustment' => 'ADJ',
    ];

    public function next(string $documentType, string $date): string
    {
        $year = (int) date('Y', strtotime($date));
        $prefix = self::PREFIXES[$documentType] ?? null;
        if (!$prefix) throw new RuntimeException("Unknown document sequence [{$documentType}].");

        DB::table('document_sequences')->insertOrIgnore([
            'document_type' => $documentType, 'year' => $year, 'prefix' => $prefix,
            'next_number' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $sequence = DB::table('document_sequences')
            ->where('document_type', $documentType)->where('year', $year)->lockForUpdate()->first();
        if (!$sequence) throw new RuntimeException("Document sequence [{$documentType}/{$year}] could not be created.");

        $number = (int) $sequence->next_number;
        DB::table('document_sequences')->where('id', $sequence->id)->update(['next_number' => $number + 1, 'updated_at' => now()]);
        return $sequence->prefix . '-' . $year . '-' . str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
