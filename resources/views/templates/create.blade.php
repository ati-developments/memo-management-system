@extends('layouts.app')

@section('title', 'Create Memo Template')
@section('styles')
    @include('templates.builder-styles')
@endsection

@section('content')
<div class="template-editor">
    @section('header-title')
Create a memo template
@endsection
@section('header-description')
Start with the essentials. Build a reusable template that helps your team create consistent memos.
@endsection
@section('header-eyebrow')
Template builder
@endsection
@section('header-back')
<x-back-link :fallback="route('templates.index')" />
@endsection

    <div class="editor-body">
        <ol class="editor-steps" aria-label="Template setup">
            <li class="current" aria-current="step"><span>1</span> Template details</li>
            <li><span>2</span> Fields &amp; tables</li>
            <li><span>3</span> Approval workflow</li>
        </ol>
        <div class="create-layout">
            <div class="builder-card">
                <h2 class="section-title">Template information</h2>
                <p class="section-description">Give your template a name and choose the department it belongs to.</p>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Please check your template details.</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form action="{{ route('templates.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="template_name" class="form-label">Template name</label>
                            <input id="template_name" type="text" name="template_name" class="form-control" placeholder="e.g. General Memo" value="{{ old('template_name') }}" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label for="template_code" class="form-label">Template code</label>
                            <input id="template_code" type="text" name="template_code" class="form-control" placeholder="e.g. GEN-MEMO" value="{{ old('template_code') }}" maxlength="255" required aria-describedby="code-help">
                            <span class="field-help" id="code-help">A short reference to identify this template.</span>
                        </div>
                        <div class="form-group">
                            <label for="department_id" class="form-label">Department</label>
                            <select id="department_id" name="department_id" class="form-control" required>
                                <option value="">Select a department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-control" aria-describedby="status-help">
                                <option value="1" @selected(old('status', '1') == '1')>Active</option>
                                <option value="0" @selected(old('status', '1') == '0')>Inactive</option>
                            </select>
                            <span class="field-help" id="status-help">Only active templates appear in the template library.</span>
                        </div>
                        <div class="form-group full">
                            <label for="description" class="form-label">Description <span class="optional">Optional</span></label>
                            <textarea id="description" name="description" class="form-control" placeholder="Describe when your team should use this template...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('templates.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create &amp; continue <span aria-hidden="true">&rarr;</span></button>
                    </div>
                </form>
            </div>
            <aside class="editor-guide">
                <div class="guide-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/></svg></div>
                <h2>A foundation for every memo</h2>
                <p>Save these details first, then make the template work for your team.</p>
                <ul class="guide-list">
                    <li><strong>Define memo fields</strong><p>Add the information employees need to provide, such as subject, date, and purpose.</p></li>
                    <li><strong>Organize details in tables</strong><p>Include structured rows for items, costs, or other repeating information.</p></li>
                    <li><strong>Set the approval workflow</strong><p>Choose who reviews and approves memos created with this template.</p></li>
                </ul>
                <p class="guide-note">Tip: use a clear, specific name so your team can find the right template quickly.</p>
            </aside>
        </div>
    </div>
</div>
@endsection
