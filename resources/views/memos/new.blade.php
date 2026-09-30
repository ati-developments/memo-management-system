@extends('layouts.app')

@section('title', 'New Memo')

@section('content')

<style>
    /* =========================================================
       NEW MEMO - TEMPLATE SELECTION
    ========================================================= */

    .new-memo-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 32px 36px 50px;
    }


    /* =========================================================
       PAGE HEADER
    ========================================================= */

    .new-memo-header {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 28px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        color: #667085;
        text-decoration: none;

        font-size: 13px;
        font-weight: 500;

        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #1d2939;
    }

    .new-memo-header h1 {
        margin: 0;

        color: #182230;

        font-family: var(--font-display, Georgia, serif);
        font-size: 30px;
        font-weight: 600;
        line-height: 1.2;
    }


    /* =========================================================
       MAIN CARD
    ========================================================= */

    .template-selection-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8faff 100%);
        border: 1px solid #e1e7f5;
        border-radius: 18px;
        box-shadow: 0 14px 35px rgba(22, 38, 83, 0.07);
        padding: 30px;
    }


    /* =========================================================
       CARD TITLE
    ========================================================= */

    .selection-title {
        margin: 0 0 6px;

        color: #182230;

        font-size: 15px;
        font-weight: 600;
    }


    /* =========================================================
       TEMPLATE GRID
    ========================================================= */

    .selection-description { margin: 0 0 23px; color: #667085; font-size: 13px; }

    .template-grid {
        display: grid;

        grid-template-columns: repeat(3, minmax(0, 1fr));

        gap: 13px;
    }

    .department-card { overflow: hidden; border: 1px solid #e1e6ef; border-radius: 12px; background: #fff; box-shadow: 0 2px 5px rgba(16, 24, 40, .02); transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
    .department-card:hover { border-color: #b8c7fa; box-shadow: 0 7px 18px rgba(38, 75, 183, .09); transform: translateY(-2px); }
    .department-card[open] { grid-column: 1 / -1; border-color: #9db1f8; box-shadow: 0 8px 22px rgba(38, 75, 183, .07); transform:none; }
    .template-grid { align-items:start; }
    .department-card summary:focus-visible,.department-template:focus-visible { outline:3px solid #82aaff; outline-offset:-3px; }
    .department-card[open] summary { background:#f1f5ff; border-bottom:1px solid #e1e8f7; }
    .department-name { overflow-wrap:anywhere; }
    .department-card summary { display: flex; align-items: center; gap: 13px; min-height: 78px; padding: 15px 16px; cursor: pointer; list-style: none; }
    .department-card summary::-webkit-details-marker { display: none; }
    .department-card summary:hover { background: #fbfcff; }
    .department-icon { display: flex; width: 44px; height: 44px; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 12px; background: linear-gradient(145deg, #eaf0ff, #dce6ff); color: #3155e7; }
    .department-card:nth-child(3n + 2) .department-icon { background: linear-gradient(145deg, #f3edff, #e9ddff); color: #7846b5; }
    .department-card:nth-child(3n + 3) .department-icon { background: linear-gradient(145deg, #e5f8ef, #d4f1e3); color: #16854b; }
    .department-info { flex: 1; min-width: 0; }
    .department-name { margin: 0 0 4px; color: #182230; font-size: 14px; font-weight: 700; }
    .department-count { margin: 0; color: #98a2b3; font-size: 11px; }
    .department-toggle { display: flex; width: 28px; height: 28px; align-items: center; justify-content: center; border-radius: 50%; background: #f0f3fa; color: #52627c; font-size: 19px; transition: transform .2s ease, background .2s ease; }
    .department-card[open] .department-toggle { background: #3155e7; color: #fff; }
    .department-card[open] .department-toggle { transform: rotate(90deg); }
    .department-template-panel { padding:16px; background:#f8faff; }
    .department-template-toolbar { display:flex; align-items:center; gap:14px; margin-bottom:12px; flex-wrap:wrap; }
    .department-template-search { flex:1; min-width:0; width:100%; padding:10px 12px; border:1px solid #d9e1ef; border-radius:8px; background:#fff; color:#24324a; font:inherit; font-size:13px; }
    .department-template-search:focus { outline:2px solid #82aaff; outline-offset:1px; }
    .department-template-results { margin:0; color:#667085; font-size:11px; white-space:nowrap; }
    .department-search-label { display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#344054; }
    .department-templates { max-height:380px; overflow-y:auto; border:1px solid #e1e6ef; border-radius:9px; background:#fff; scrollbar-gutter:stable; }
    .department-template { display:flex; align-items:center; gap:11px; min-width:0; min-height:57px; padding:10px 13px; border-bottom:1px solid #edf0f5; color:#344054; text-decoration:none; transition:background .15s; }
    .department-template:last-child { border-bottom:0; }
    .department-template:hover { background:#f0f5ff; }
    .template-document-icon { display:grid; width:29px; height:33px; flex-shrink:0; place-items:center; border-radius:6px; background:#edf2ff; color:#3155e7; }
    .template-document-icon svg { width:17px; height:17px; }
    .department-template-copy { flex:1; min-width:0; }
    .department-template-name { display:block; color:#24324a; font-size:13px; font-weight:600; line-height:1.5; overflow-wrap:anywhere; }
    .department-template-description { display:block; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#667085; font-size:11px; line-height:1.5; }
    .department-template-action { display:inline-flex; align-items:center; gap:7px; flex-shrink:0; color:#3155e7; font-size:11px; font-weight:600; }
    .department-empty { margin:0; padding:24px 16px; color:#667085; font-size:12px; text-align:center; line-height:1.7; }
    .department-template-panel [hidden] { display:none; }
    @media(max-width:650px) { .department-template-panel { padding:12px; } .department-template-search { font-size:16px; flex-basis:100%; } .department-templates { max-height:340px; } .template-document-icon { display:none; } }
    @media(prefers-reduced-motion:reduce) { .department-card,.department-toggle,.department-template { transition:none; } }


    /* =========================================================
       CREATE NEW TEMPLATE
    ========================================================= */

    .create-template-card {
        display: flex;
        align-items: center;

        min-height: 68px;

        padding: 13px 15px;

        background: #f5f7ff;

        border: 1px dashed #3155e7;
        border-radius: 9px;

        text-decoration: none;

        transition:
            background 0.2s ease,
            box-shadow 0.2s ease;
    }

    .create-template-card:hover {
        background: #eef1ff;

        box-shadow:
            0 4px 12px rgba(49, 85, 231, 0.08);
    }

    .create-template-icon {
        width: 38px;
        height: 38px;

        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-right: 13px;

        border-radius: 8px;

        background: #3155e7;

        color: #ffffff;

        font-size: 21px;
        line-height: 1;
    }

    .create-template-title {
        margin: 0 0 4px;

        color: #3155e7;

        font-size: 13px;
        font-weight: 700;
    }

    .create-template-description {
        margin: 0;

        color: #7180b0;

        font-size: 11px;
    }


    /* =========================================================
       EMPTY STATE
    ========================================================= */

    .no-templates {
        grid-column: 1 / -1;

        padding: 45px 20px;

        text-align: center;

        border: 1px dashed #d9dde5;
        border-radius: 9px;

        background: #fafbfc;
    }

    .no-templates-icon {
        margin-bottom: 10px;

        color: #98a2b3;

        font-size: 25px;
    }

    .no-templates h3 {
        margin: 0 0 5px;

        color: #344054;

        font-size: 14px;
        font-weight: 600;
    }

    .no-templates p {
        margin: 0;

        color: #98a2b3;

        font-size: 12px;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 950px) {

        .template-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

    }

    @media (max-width: 650px) {

        .new-memo-page {
            padding: 22px 16px 40px;
        }

        .new-memo-header {
            gap: 12px;
        }

        .new-memo-header h1 {
            font-size: 26px;
        }

        .template-selection-card {
            padding: 20px;
        }

        .template-grid {
            grid-template-columns: 1fr;
        }

    }
</style>


<div class="new-memo-page">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    @section('header-title')
Create a memo
@endsection
@section('header-description')
Choose a department to find the right template for your memo.
@endsection
@section('header-eyebrow')
Memo builder
@endsection
@section('header-back')
<x-back-link :fallback="route('dashboard')" />
@endsection



    {{-- =====================================================
         TEMPLATE SELECTION CARD
    ====================================================== --}}

    <div class="template-selection-card">

        <h2 class="selection-title">
            Choose a department
        </h2>

        <p class="selection-description">
            Open a department to select the memo template you need.
        </p>


        <div class="template-grid">
            <a
                href="{{ route('templates.create') }}"
                class="create-template-card"
            >

                <div class="create-template-icon">
                    +
                </div>

                <div>

                    <p class="create-template-title">
                        Create New Template
                    </p>

                    <p class="create-template-description">
                        Define custom fields
                    </p>

                </div>

            </a>

            @forelse($departments as $department)

                <details class="department-card" name="memo-department">
                    <summary>
                        <div class="department-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18"/><path d="M5 21V6l7-3 7 3v15"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/><path d="M14 9h1"/><path d="M14 13h1"/><path d="M14 17h1"/>
                            </svg>
                        </div>
                        <div class="department-info">
                            <p class="department-name">{{ $department->department_name }}</p>
                            <p class="department-count">{{ $department->memoTemplates->count() }} {{ Str::plural('template', $department->memoTemplates->count()) }}</p>
                        </div>
                        <span class="department-toggle">&rsaquo;</span>
                    </summary>

                    <div class="department-template-panel">
                    @if($department->memoTemplates->isNotEmpty())
                        <label class="department-search-label" for="template-search-{{ $department->id }}">Find a template in {{ $department->department_name }}</label>
                        <div class="department-template-toolbar">
                            <input type="search" id="template-search-{{ $department->id }}" class="department-template-search" placeholder="Search templates..." autocomplete="off" aria-controls="department-templates-{{ $department->id }}">
                            <p class="department-template-results" role="status" aria-live="polite">{{ $department->memoTemplates->count() }} templates</p>
                        </div>
                    @endif
                    <div class="department-templates" id="department-templates-{{ $department->id }}" role="region" aria-label="{{ $department->department_name }} templates" tabindex="0">
                        @forelse($department->memoTemplates as $template)
                            <a href="{{ route('memos.create.template', $template->id) }}" class="department-template">
                                <span class="template-document-icon" aria-hidden="true">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/>
                                    </svg>
                                </span>
                                <span class="department-template-copy">
                                    <span class="department-template-name">{{ $template->template_name }}</span>
                                    <span class="department-template-description">{{ $template->description ?: 'Start a new memo with this template.' }}</span>
                                </span>
                                <span class="department-template-action">Use <span aria-hidden="true">&rarr;</span></span>
                            </a>
                        @empty
                            <p class="department-empty">No active templates are available for this department.</p>
                        @endforelse
                        <p class="department-empty department-no-matches" hidden>No templates match your search. Try another name or clear the search.</p>
                    </div>
                    </div>
                </details>

            @empty

                <div class="no-templates">

                    <div class="no-templates-icon">
                        ▤
                    </div>

                    <h3>
                        No memo templates available
                    </h3>

                    <p>
                        There are currently no active memo templates.
                    </p>

                </div>

            @endforelse


            

        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.department-card').forEach(card => {
        const search = card.querySelector('.department-template-search');
        if (search) {
            const templates = Array.from(card.querySelectorAll('.department-template'));
            const entries = templates.map(template => ({
                element: template,
                text: template.querySelector('.department-template-copy').textContent.toLowerCase()
            }));
            search.addEventListener('input', () => {
                const query = search.value.trim().toLowerCase();
                let matches = 0;
                entries.forEach(entry => {
                    entry.element.hidden = !entry.text.includes(query);
                    if (!entry.element.hidden) matches++;
                });
                card.querySelector('.department-template-results').textContent = `${matches} of ${templates.length} templates`;
                card.querySelector('.department-no-matches').hidden = matches > 0;
                card.querySelector('.department-templates').scrollTop = 0;
            });
        }
        card.addEventListener('toggle', () => {
            if (!card.open) return;
            document.querySelectorAll('.department-card').forEach(other => {
                if (other !== card) other.open = false;
            });
        });
    });
</script>
@endsection

