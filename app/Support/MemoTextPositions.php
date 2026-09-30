<?php

namespace App\Support;

use App\Models\MemoTemplate;

class MemoTextPositions
{
    public static function forTemplate(MemoTemplate $template): array
    {
        $positions = ['start' => 'Before memo details', 'after_subject' => 'After subject'];
        foreach ($template->fields->where('is_active', true)->sortBy('field_order') as $field) {
            if (!in_array(strtolower($field->field_name), ['to', 'from', 'through', 'date', 'subject'])) {
                $positions['field_'.$field->id] = 'Before '.$field->field_label;
            }
        }
        foreach ($template->tables->where('is_active', true)->sortBy('table_order') as $table) {
            $positions['table_'.$table->id] = 'Before table: '.$table->table_label;
        }
        return $positions + ['before_recommendation' => 'After tables', 'before_signatures' => 'Before signatures', 'end' => 'After signatures'];
    }
}
