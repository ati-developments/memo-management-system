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

    public static function forWorkflow(?array $steps): array
    {
        $steps = array_values($steps ?? []);
        $defaults = self::forCount(count($steps));

        if (count($steps) + 1 <= 3) {
            return $defaults;
        }

        return array_map(function ($step, $index) use ($defaults) {
            $label = $step['signature_label'] ?? null;
            return in_array($label, ['Recommended by', 'Approved by'], true)
                ? $label
                : ($defaults[$index] ?? 'Approved by');
        }, $steps, array_keys($steps));
    }
}
