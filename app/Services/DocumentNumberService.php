<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentNumberService
{
    public function next(string $documentType, string $date): string
    {
        $sequence = DB::table('document_sequences')->where('document_type', $documentType)->lockForUpdate()->first();
        if (!$sequence) {
            throw new RuntimeException("Document sequence [{$documentType}] is not configured.");
        }

        $number = (int) $sequence->next_number;
        DB::table('document_sequences')->where('id', $sequence->id)->update([
            'next_number' => $number + 1,
            'updated_at' => now(),
        ]);

        return $sequence->prefix . '-' . date('Y', strtotime($date)) . '-' . str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
