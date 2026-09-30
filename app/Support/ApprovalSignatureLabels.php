<?php

namespace App\Support;

class ApprovalSignatureLabels
{
    public static function forCount(int $count): array
    {
        return match ($count) {
            1 => ['Approved by'],
            2 => ['Checked by', 'Confirmed by'],
            4 => ['Recommended by', 'Approved by', 'Approved by', 'Approved by'],
            default => array_fill(0, $count, 'Approved by'),
        };
    }
}
