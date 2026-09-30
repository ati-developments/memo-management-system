<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Memo extends Model
{
    protected $fillable = [
        'memo_number',
        'template_id',
        'department_id',
        'created_by',
        'subject',
        'status',
        'creation_type',
        'content',
        'text_blocks',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'text_blocks' => 'array',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MemoTemplate::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(MemoFieldValue::class);
    }

    public function tableRows(): HasMany
    {
        return $this->hasMany(MemoTableRow::class)->orderBy('row_order');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(MemoApproval::class);
    }

    public function documentTables(): \Illuminate\Support\Collection
    {
        $groups = $this->tableRows->groupBy('template_table_id');
        foreach ($this->template?->tables ?? [] as $table) {
            if ($table->is_active && !$groups->has($table->id)) {
                $groups->put($table->id, collect());
            }
        }
        return $groups->sortBy(fn ($rows, $id) => ($rows->first()?->templateTable ?? $this->template?->tables->firstWhere('id', $id))?->table_order ?? 0);
    }

    public function documentFields(): \Illuminate\Support\Collection
    {
        $fields = collect($this->fieldValues->all());
        foreach ($this->template?->fields ?? [] as $field) {
            if ($field->is_active && !$fields->contains('template_field_id', $field->id)) {
                $value = new MemoFieldValue(['template_field_id' => $field->id, 'field_name' => $field->field_name]);
                $value->setRelation('templateField', $field);
                $fields->push($value);
            }
        }
        return $fields->sortBy(fn ($value) => $value->templateField?->field_order ?? 0);
    }
}
