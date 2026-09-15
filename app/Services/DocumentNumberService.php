<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentNumberService
{
    public function next(string $documentType, string $date): string
    {
        $year = (int) date('Y', strtotime($date));
        $sequence = DB::table('document_sequences')
            ->where('document_type', $documentType)
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        if (!$sequence) {
            throw new RuntimeException("Document sequence [{$documentType}/{$year}] is not configured.");
        }

        $number = (int) $sequence->next_number;
        DB::table('document_sequences')->where('id', $sequence->id)->update([
            'next_number' => $number + 1,
            'updated_at' => now(),
        ]);

        return $sequence->prefix . '-' . $year . '-' . str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
