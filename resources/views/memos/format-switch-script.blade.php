<style>
.memo-format-choice { border:0; padding:0 0 14px; margin:0; min-width:0; }
.memo-format-choice legend { font-size:12px; font-weight:700; margin-bottom:8px; }
.memo-format-choice p { color:#667085; font-size:13px; margin:6px 0 12px; }
.memo-format-options { display:flex; flex-wrap:wrap; gap:8px; }
.memo-format-options label { display:flex; align-items:center; gap:6px; padding:7px 10px; margin:0; font-size:12px; line-height:1.4; border:1px solid #cbd5df; border-radius:6px; cursor:pointer; }
.memo-format-options label:has(input:checked) { border-color:#167451; background:#edf7f1; color:#163f2b; box-shadow:0 0 0 1px #167451; }
.memo-format-options input { width:13px; height:13px; margin:0; accent-color:#167451; }
.memo-format-options strong,.memo-format-options small { display:block; }
.memo-format-options small { margin-top:5px; font-size:12px; line-height:1.5; }
.freeform-format-tools { display:flex; align-items:center; flex-wrap:wrap; gap:4px; margin:8px 0; }
.freeform-format-tools button, .freeform-format-tools select { padding:4px 6px; border:1px solid #cbd5df; border-radius:5px; cursor:pointer; background:#f8fafb; color:#243448; font-size:11px; }
.freeform-format-tools .freeform-font-size-label { margin:0 0 0 4px; font-size:11px; }
#freeform-rich-editor { min-height:320px; padding:16px; background:#fff; color:#111; border:1px solid #cbd5df; border-radius:6px; font:15px/1.6 Georgia,serif; overflow-wrap:anywhere; }
#freeform-rich-editor:focus { outline:2px solid #167451; outline-offset:2px; }
.freeform-preview-details { display:grid; grid-template-columns:75px minmax(0,1fr); gap:12px; margin:18px 0 24px; }
#documentPreview .freeform-preview-header { display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:20px; }
#documentPreview .freeform-preview-header h2 { flex:1; min-width:0; margin:0; font-size:24px !important; line-height:1.3; font-weight:700; color:#111; overflow-wrap:anywhere; }
#documentPreview .freeform-preview-header img { width:150px; max-width:32%; height:auto; flex-shrink:0; }
.freeform-preview-details dd { margin:0; white-space:pre-line; overflow-wrap:anywhere; }
.freeform-preview-details dt { display:flex; justify-content:space-between; gap:8px; white-space:nowrap; margin:0; font-weight:700; }
.freeform-table-tools { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:0 0 12px; font-size:12px; }
.freeform-table-tools label { display:flex; align-items:center; gap:6px; margin:0; }
.freeform-table-tools input { width:54px; padding:6px; border:1px solid #cbd5df; border-radius:5px; background:#fff; color:#243448; }
.freeform-table-tools button { padding:5px 7px; border:1px solid #cbd5df; border-radius:5px; background:#f8fafb; color:#243448; cursor:pointer; font-size:11px; }
.freeform-table-tools button:disabled { opacity:.45; cursor:default; }
.freeform-editor-wrap { position:relative; }
.freeform-selected-table-tools { position:absolute; z-index:2; right:8px; display:flex; gap:3px; padding:3px; border:1px solid #cbd5df; border-radius:6px; background:#fff; box-shadow:0 2px 6px #0002; }
.freeform-selected-table-tools button { display:grid; place-items:center; width:26px; height:26px; padding:4px; border:0; border-radius:4px; background:transparent; color:#243448; cursor:pointer; }
.freeform-selected-table-tools button:hover { background:#edf2f7; }
.freeform-selected-table-tools button[data-body-table-action="remove"] { color:#c62828; }
.freeform-selected-table-tools button[data-body-table-action="remove"]:hover { background:#fdecec; }
.freeform-selected-table-tools svg { width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:1.4; stroke-linecap:round; stroke-linejoin:round; }
.freeform-selected-table-tools[hidden] { display:none; }
#freeform-rich-editor table, #freeform-live-content table { width:100%; table-layout:fixed; border-collapse:collapse; margin:14px 0; }
#freeform-rich-editor td, #freeform-rich-editor th, #freeform-live-content td, #freeform-live-content th { border:1px solid #9aa7b7; padding:8px; min-width:30px; overflow-wrap:anywhere; vertical-align:top; }
html[data-theme=dark] #freeform-rich-editor { background:#172238; color:#e0e8f5; border-color:#40516c; }
html[data-theme=dark] .freeform-format-tools button, html[data-theme=dark] .freeform-format-tools select, html[data-theme=dark] .freeform-table-tools button, html[data-theme=dark] .freeform-table-tools input { background:#22314a; color:#e0e8f5; border-color:#40516c; }
html[data-theme=dark] #freeform-body-fields > label, html[data-theme=dark] .freeform-table-tools { color:#e0e8f5; }
html[data-theme=dark] #freeform-rich-editor td, html[data-theme=dark] #freeform-rich-editor th { border-color:#63758e; }
html[data-theme=dark] #freeform-rich-editor:focus { outline-color:#6dc99a; }
#freeform-live-content { line-height:1.6; overflow-wrap:anywhere; margin-bottom:32px; }
#freeform-live-content p { margin:0 0 12px; }
#freeform-live-content ul,#freeform-rich-editor ul { list-style:disc; padding-left:25px; }
#freeform-live-content ol,#freeform-rich-editor ol { list-style:decimal; padding-left:25px; }
#memoForm [hidden], #documentPreview [hidden], .memo-insert-bar [hidden] { display:none !important; }
@media(max-width:600px) { .memo-format-options { grid-template-columns:1fr; } }
</style>
<script>
(() => {
    const form = document.getElementById('memoForm');
    const editor = document.getElementById('freeform-rich-editor');
    const source = document.getElementById('freeform-source');
    const preview = document.getElementById('freeform-live-content');
    // Rebuild pasted markup using the same formatting tags supported by saved memos.
    const clean = html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const allowedSizes = new Set(['12px','14px','16px','18px','24px']);
        const allowed = new Set(['STRONG','EM','U','P','DIV','UL','OL','LI','H2','H3','BR','TABLE','THEAD','TBODY','TFOOT','TR','TH','TD']);
        const walk = node => {
            if (node.nodeType === Node.TEXT_NODE) return document.createTextNode(node.textContent);
            const fragment = document.createDocumentFragment();
            if (node.nodeType !== Node.ELEMENT_NODE || ['SCRIPT','STYLE','IFRAME','OBJECT','SVG','MATH'].includes(node.tagName)) return fragment;
            const tag = ({B:'STRONG', I:'EM'})[node.tagName] || node.tagName;
            const result = allowed.has(tag) || tag === 'SPAN' ? document.createElement(tag) : fragment;
            if (tag === 'SPAN' && allowedSizes.has(node.style.fontSize)) result.style.fontSize = node.style.fontSize;
            node.childNodes.forEach(child => result.append(walk(child)));
            return result;
        };
        const wrapper = document.createElement('div');
        doc.body.childNodes.forEach(child => wrapper.append(walk(child)));
        return wrapper.innerHTML;
    };
    const sync = () => {
        source.value = clean(editor.innerHTML);
        preview.innerHTML = source.value;
        preview.querySelectorAll('span[style]').forEach(span => {
            if (['12px','14px','16px','18px','24px'].includes(span.style.fontSize)) {
                span.style.setProperty('font-size', span.style.fontSize, 'important');
            }
        });
        document.querySelectorAll('[data-freeform-detail]').forEach(element => {
            element.textContent = form.elements.namedItem(element.dataset.freeformDetail)?.value || '—';
        });
    };
    const switchFormat = () => {
        const freeform = form.elements.creation_type.value === 'freeform';
        ['template-body-fields','template-text-fields'].forEach(id => {
            const fieldset = document.getElementById(id);
            fieldset.hidden = freeform;
            fieldset.disabled = freeform;
        });
        document.getElementById('freeform-body-fields').hidden = !freeform;
        document.getElementById('freeform-body-fields').disabled = !freeform;
        document.getElementById('template-document-body').hidden = freeform;
        document.getElementById('freeform-document-body').hidden = !freeform;
        document.querySelectorAll('.memo-insert-bar [data-insert], .memo-insert-bar [data-add-text-at], .memo-insert-bar [data-memo-format]').forEach(button => { button.hidden = freeform; });
        sync();
    };
    form.querySelectorAll('[name="creation_type"]').forEach(radio => radio.addEventListener('change', switchFormat));
    form.addEventListener('input', sync);
    form.addEventListener('submit', sync);
    editor.addEventListener('paste', event => {
        event.preventDefault();
        const html = event.clipboardData.getData('text/html');
        document.execCommand(html ? 'insertHTML' : 'insertText', false, html ? clean(html) : event.clipboardData.getData('text/plain'));
        sync();
    });
    document.querySelectorAll('[data-freeform-command]').forEach(button => {
        button.addEventListener('mousedown', event => event.preventDefault());
        button.addEventListener('click', () => {
            editor.focus(); document.execCommand(button.dataset.freeformCommand, false, button.dataset.value || null); sync();
        });
    });
    document.getElementById('freeform-font-size').addEventListener('change', event => {
        editor.focus();
        const selection = window.getSelection();
        if (!selection.rangeCount || selection.isCollapsed || !editor.contains(selection.anchorNode)) return;
        const range = selection.getRangeAt(0);
        const span = document.createElement('span');
        span.style.fontSize = event.target.value;
        try { span.append(range.extractContents()); range.insertNode(span); }
        catch (_) { return; }
        const next = document.createRange(); next.selectNodeContents(span); next.collapse(false);
        selection.removeAllRanges(); selection.addRange(next);
        sync();
    });
    let bodySelection = null;
    let selectedTable = null;
    const tableButtons = [...document.querySelectorAll('[data-body-table-action]')];
    const tableTools = document.querySelector('.freeform-selected-table-tools');
    const selectTable = table => {
        selectedTable = table && editor.contains(table) ? table : null;
        tableTools.hidden = !selectedTable;
        if (selectedTable) tableTools.style.top = Math.max(0, selectedTable.offsetTop - 34) + 'px';
    };
    editor.addEventListener('click', event => selectTable(event.target.closest('table')));
    editor.addEventListener('input', () => {
        if (selectedTable && !editor.contains(selectedTable)) selectTable(null);
    });
    const focusCell = cell => {
        editor.focus();
        const range = document.createRange();
        range.selectNodeContents(cell); range.collapse(true);
        const selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(range);
        bodySelection = range.cloneRange();
    };
    tableButtons.forEach(button => {
        button.addEventListener('mousedown', event => event.preventDefault());
        button.addEventListener('click', () => {
            if (!selectedTable || !editor.contains(selectedTable)) { selectTable(null); return; }
            const table = selectedTable;
            const status = document.getElementById('body-table-status');
            const columns = Math.max(1, ...Array.from(table.rows, row => row.cells.length));
            if (button.dataset.bodyTableAction === 'row') {
                if (table.rows.length >= 20) { status.textContent = 'A table can have up to 20 rows.'; return; }
                const row = (table.tBodies[0] || table.createTBody()).insertRow();
                for (let index = 0; index < columns; index++) row.insertCell().append(document.createElement('br'));
                focusCell(row.cells[0]);
                status.textContent = 'Row added.';
            } else if (button.dataset.bodyTableAction === 'column') {
                if (columns >= 8) { status.textContent = 'A table can have up to 8 columns.'; return; }
                if (!table.rows.length) table.createTBody().insertRow();
                Array.from(table.rows).forEach(row => {
                    const cell = document.createElement(row.parentElement.tagName === 'THEAD' ? 'th' : 'td');
                    cell.append(document.createElement('br')); row.append(cell);
                });
                focusCell(table.rows[0].lastElementChild);
                status.textContent = 'Column added.';
            } else {
                const paragraph = document.createElement('p'); paragraph.append(document.createElement('br'));
                table.replaceWith(paragraph);
                selectTable(null);
                focusCell(paragraph);
                status.textContent = 'Table removed.';
            }
            sync();
        });
    });
    document.addEventListener('selectionchange', () => {
        const selection = window.getSelection();
        if (selection.rangeCount && editor.contains(selection.anchorNode) && editor.contains(selection.focusNode)) {
            bodySelection = selection.getRangeAt(0).cloneRange();
            const node = selection.anchorNode.nodeType === Node.ELEMENT_NODE ? selection.anchorNode : selection.anchorNode.parentElement;
            selectTable(node.closest('table'));
        }
    });
    document.getElementById('body-insert-table').addEventListener('click', () => {
        const rowsInput = document.getElementById('body-table-rows');
        const columnsInput = document.getElementById('body-table-columns');
        if (!rowsInput.reportValidity() || !columnsInput.reportValidity()) return;
        const rows = Number(rowsInput.value), columns = Number(columnsInput.value);
        if (!Number.isInteger(rows) || rows < 1 || rows > 20 || !Number.isInteger(columns) || columns < 1 || columns > 8) return;
        const table = document.createElement('table');
        const tbody = table.createTBody();
        for (let row = 0; row < rows; row++) {
            const tr = tbody.insertRow();
            for (let column = 0; column < columns; column++) tr.insertCell().append(document.createElement('br'));
        }
        const range = bodySelection && editor.contains(bodySelection.commonAncestorContainer) ? bodySelection.cloneRange() : document.createRange();
        if (!bodySelection || !editor.contains(range.commonAncestorContainer)) { range.selectNodeContents(editor); range.collapse(false); }
        // Insert after the current paragraph or table so tables never become nested.
        let block = range.startContainer.nodeType === Node.ELEMENT_NODE ? range.startContainer : range.startContainer.parentElement;
        while (block.parentElement && block.parentElement !== editor) block = block.parentElement;
        if (block !== editor && block.parentElement === editor) range.setStartAfter(block);
        range.collapse(true);
        const trailing = document.createElement('p'); trailing.append(document.createElement('br'));
        const fragment = document.createDocumentFragment(); fragment.append(table, trailing);
        range.insertNode(fragment);
        editor.focus();
        range.selectNodeContents(tbody.rows[0].cells[0]); range.collapse(true);
        const selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(range);
        bodySelection = range.cloneRange();
        selectTable(table);
        sync();
        document.getElementById('body-table-status').textContent = `${rows} × ${columns} table inserted. Type in each cell to edit.`;
    });
    switchFormat();
})();
</script>
