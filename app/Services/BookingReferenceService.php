<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Str;

class BookingReferenceService
{
    /**
     * Generate a unique booking reference code.
     * Format: SPS-XXXX-YYY where X is numeric and Y is alphanumeric
     *
     * @return string
     */
    public function generateUniqueReference(): string
    {
        $attempts = 0;
        $maxAttempts = 5;
        
        do {
            // Generate numeric part (4 digits)
            $numericPart = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Generate alphanumeric part (3 chars)
            $alphaPart = strtoupper(Str::random(3));
            
            // Combine parts
            $reference = "SPS-{$numericPart}-{$alphaPart}";
            
            // Check if reference already exists
            $exists = Booking::where('booking_reference', $reference)->exists();
            
            $attempts++;
            
        } while ($exists && $attempts < $maxAttempts);
        
        // If we've tried too many times and still have conflicts
        if ($exists) {
            // Fall back to timestamp-based reference
            $timestamp = now()->format('YmdHis');
            $random = strtoupper(Str::random(3));
            $reference = "SPS-{$timestamp}-{$random}";
        }
        
        return $reference;
    }
    
    /**
     * Validate a booking reference format.
     *
     * @param string $reference
     * @return bool
     */
    public function isValidReferenceFormat(string $reference): bool
    {
        // Check if reference matches our pattern: SPS-XXXX-YYY
        return preg_match('/^SPS-\d{4}-[A-Z0-9]{3}$/', $reference) === 1;
    }
}