@extends('layouts.app')

@section('title', 'Memo Templates | Memo')

@section('styles')
<style>
    .templates-page { --muted:#64748b; color:#243550; }
    .templates-page a { text-decoration:none; }
    .templates-page [hidden] { display:none; }
    .library-tools { padding:18px; margin-bottom:24px; border:1px solid #e2e8f1; border-radius:14px; background:#fff; box-shadow:0 2px 10px #172b4d03; }
    .library-tools-top { display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:14px; }
    .library-tools-title { margin:0 0 4px; font-size:14px; font-weight:650; }
    .library-tools-hint { margin:0; font-size:12px; color:var(--muted); line-height:1.5; }
    .template-search-label { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); }
    .template-search-wrap { position:relative; width: min(100%,360px); flex-shrink:0; }
    .template-search-wrap svg { position:absolute; width:17px; height:17px; left:13px; top:13px; pointer-events:none; }
    .template-search { width:100%; min-height:42px; padding:10px 12px 10px 39px; border:1px solid #d5dfee; border-radius:9px; font:inherit; font-size:12px; }
    .template-search:focus { outline:3px solid #315fe91a; border-color:#7596ee; }
    .department-filters { display:flex; flex-wrap:wrap; gap:6px; padding-top:14px; border-top:1px solid #edf1f6; }
    .department-filter { display:inline-flex; align-items:center; gap:8px; min-height:32px; padding:6px 10px; border:1px solid #e4eaf3; background:#fff; color:#5b6b82; font:inherit; font-size:11px; cursor:pointer; }
    .department-filter:hover { background:#f4f7ff; border-color:#b6c8f2; }
    .department-filter span { padding:2px 6px; border-radius:5px; background:#eff3f8; font-size:10px; font-variant-numeric:tabular-nums; }
    .department-filter.active span { background:#dce6ff; }
    .library-results-row { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px; }
    .template-results { margin:0; color:var(--muted); font-size:12px; }
    .library-clear { border:0; padding:4px 0; background:transparent; color:#315fe9; font:inherit; font-size:12px; cursor:pointer; }
    .department-section { margin-bottom:26px; }
    .department-heading { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px; }
    .department-heading h2 { margin:0; font-size:14px; font-weight:650; overflow-wrap:anywhere; }
    .department-heading::after { content:''; flex:1; height:1px; background:#e2e8f1; }
    .department-heading > span { padding:3px 7px; border-radius:6px; background:#e9eef6; color:#66768d; font-size:10px; }
    .template-list { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    .app .templates-page .template-item { display:grid; grid-template-columns:34px minmax(0,1fr); grid-template-rows:1fr auto; align-items:start; gap:12px; padding:16px; border:1px solid #e0e7f1; border-radius:12px; background:#fff; color:inherit; box-shadow:0 2px 5px #172b4d03; }
    .app .templates-page .template-item:hover { background:#fff; border-color:#a8bdf1; box-shadow:0 7px 20px #244a960c; transform:translateY(-2px); }
    .app .templates-page .template-file { display:grid; place-items:center; width:34px; height:38px; border-radius:9px; background:#edf2ff; color:#5275c6; }
    .template-file svg { width:20px; height:20px; }
    .template-copy { min-width:0; }
    .template-name { display:block; margin-bottom:5px; font-size:13px; font-weight:650; line-height:1.5; overflow-wrap:anywhere; }
    .template-description { display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden; color:var(--muted); font-size:12px; line-height:1.6; overflow-wrap:anywhere; }
    .template-card-footer { grid-column:1 / -1; display:flex; align-items:center; justify-content:space-between; gap:10px; padding-top:12px; border-top:1px solid #edf1f6; }
    .template-meta { color:#718098; font-size:10px; }
    .use-template { display:inline-flex; align-items:center; gap:8px; color:#315fe9; font-size:11px; font-weight:600; }
    .use-template > span { transition:transform .18s; }.template-item:hover .use-template > span { transform:translateX(3px); }
    .template-empty { padding:40px 20px; border:1px dashed #ccd8e9; border-radius:14px; background:#fff; text-align:center; }
    .template-empty h2 { margin:0 0 8px; font-size:17px; font-weight:650; }
    .template-empty p { max-width:400px; margin:0 auto; color:var(--muted); font-size:12px; line-height:1.7; }
    .template-reset { margin-top:18px; padding:9px 14px; border:1px solid #c9d7f8; border-radius:8px; background:#edf3ff; color:#315fe9; cursor:pointer; font:inherit; font-size:12px; }
    @media(max-width:1200px) { .template-list { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:850px) { .library-tools-top { align-items:stretch; flex-direction:column; gap:12px; }.template-search-wrap { width:100%; } }
    @media(max-width:600px) { .template-list { grid-template-columns:1fr; }.library-tools { padding:14px; }.template-search { font-size:16px; } }
</style>
@endsection

@section('content')
@php
    $templateCount = $departments->sum(fn ($department) => $department->memoTemplates->count());
    $departmentCount = $departments->filter(fn ($department) => $department->memoTemplates->isNotEmpty())->count();
@endphp
<div class="templates-page">
    @section('header-title')
Memo Templates
@endsection
@section('header-description')
{{ $templateCount }} {{ Str::plural('template', $templateCount) }} across {{ $departmentCount }} {{ Str::plural('department', $departmentCount) }}
@endsection
@section('header-eyebrow')
Template library
@endsection


    <div class="template-library">
    @if($templateCount > 0)
            <div class="library-tools"><div class="library-tools-top"><div><h2 class="library-tools-title">Find your starting point</h2><p class="library-tools-hint">Choose a template to create your next memo.</p></div>
            @if($templateCount > 0)
            <label class="template-search-label" for="templateSearch">Find a template</label>
            <div class="template-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                <input id="templateSearch" class="template-search" type="search" placeholder="Search any template..." autocomplete="off" aria-controls="templateSections">
            </div>
            @endif
</div><div class="department-filters" role="group" aria-label="Filter templates by department">
                <button type="button" class="department-filter active" data-department="all" aria-pressed="true">All departments <span>{{ $templateCount }}</span></button>
                @foreach($departments as $department)
                    @if($department->memoTemplates->isNotEmpty())
                        <button type="button" class="department-filter" data-department="department-{{ $department->id }}" aria-pressed="false">{{ $department->department_name }} <span>{{ $department->memoTemplates->count() }}</span></button>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="library-results-row"><p id="templateResults" class="template-results" role="status" aria-live="polite">Showing {{ $templateCount }} {{ Str::plural('template', $templateCount) }}</p><button type="button" class="library-clear" id="clearTemplateFilters" hidden>Clear filters</button></div>
        <div id="templateSections">
            @foreach($departments as $department)
                @if($department->memoTemplates->isNotEmpty())
                    <section class="department-section" data-department="department-{{ $department->id }}" aria-labelledby="department-heading-{{ $department->id }}">
                        <div class="department-heading"><h2 id="department-heading-{{ $department->id }}">{{ $department->department_name }}</h2><span class="department-visible-count">{{ $department->memoTemplates->count() }} available</span></div>
                        <div class="template-list">
                            @foreach($department->memoTemplates as $template)
                                <a href="{{ route('memos.create.template', $template) }}" class="template-item" data-search="{{ $template->template_name . ' ' . $department->department_name . ' ' . ($template->description ?? '') }}">
                                    <span class="template-file" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6zM14 3v5h5M9 12h7M9 16h5"/></svg></span>
                                    <span class="template-copy">
                                    <span class="template-name">{{ $template->template_name }}</span>
                                    <span class="template-description">{{ $template->description ?: 'Create a memo using this template.' }}</span>

                                    </span>
                                    <span class="template-card-footer"><span class="template-meta">{{ $template->fields_count }} {{ Str::plural('field', $template->fields_count) }}</span><span class="use-template">Use template <span aria-hidden="true">&rarr;</span></span></span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
        <div id="noResults" class="template-empty" hidden><h2>No matching templates</h2><p>Try a different search or choose another department to find the template you need.</p><button type="button" id="resetTemplateFilters" class="template-reset">Clear search and filters</button></div>
    @else
        <div class="template-empty"><h2>Your template library is getting started</h2><p>No templates are available yet. Once templates are added to an active department, you will find them here.</p></div>
    @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('templateSearch');
    if (!search) return;
    const filters = document.querySelectorAll('.department-filter');
    const sections = document.querySelectorAll('.department-section');
    const noResults = document.getElementById('noResults');
    const results = document.getElementById('templateResults');
    const clearFilters = document.getElementById('clearTemplateFilters');
    let department = 'all';
    const filterTemplates = () => {
        const query = search.value.trim().toLowerCase();
        let visibleTemplates = 0;
        sections.forEach(section => {
            const departmentMatches = department === 'all' || section.dataset.department === department;
            let sectionTemplates = 0;
            section.querySelectorAll('.template-item').forEach(item => {
                const visible = departmentMatches && item.dataset.search.toLowerCase().includes(query);
                item.hidden = !visible;
                if (visible) sectionTemplates++;
            });
            section.hidden = sectionTemplates === 0;
            section.querySelector('.department-visible-count').textContent = `${sectionTemplates} available`;
            visibleTemplates += sectionTemplates;
        });
        noResults.hidden = visibleTemplates > 0;
        clearFilters.hidden = !query && department === 'all';
        results.textContent = `Showing ${visibleTemplates} ${visibleTemplates === 1 ? 'template' : 'templates'}`;
        filters.forEach(filter => {
            const active = filter.dataset.department === department;
            filter.classList.toggle('active', active);
            filter.setAttribute('aria-pressed', String(active));
        });
    };
    search.addEventListener('input', filterTemplates);
    filters.forEach(filter => filter.addEventListener('click', () => {
        department = filter.dataset.department;
        filterTemplates();
    }));
    const resetFilters = () => {
        search.value = '';
        department = 'all';
        filterTemplates();
        search.focus();
    };
    clearFilters.addEventListener('click', resetFilters);
    document.getElementById('resetTemplateFilters').addEventListener('click', resetFilters);
    filterTemplates();
});
</script>
@endsection

