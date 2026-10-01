<div class="table-format-toolbar" role="group" aria-label="Table cell formatting" hidden>
    <span>Cell format</span>
    <button type="button" data-cell-format="bold" aria-label="Bold cell" aria-pressed="false"><strong>B</strong></button>
    <button type="button" data-cell-format="italic" aria-label="Italic cell" aria-pressed="false"><em>I</em></button>
    <button type="button" data-cell-format="underline" aria-label="Underline cell" aria-pressed="false"><u>U</u></button>
</div>
<style>
    .table-format-toolbar { position:fixed; bottom:24px; left:50%; transform:translateX(-50%); z-index:50; display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid #c0dda4; border-radius:12px; background:#fff; color:#24312b; box-shadow:0 8px 30px #0002; }
    .table-format-toolbar[hidden] { display:none; }
    .table-format-toolbar span { font-size:12px; white-space:nowrap; }
    .table-format-toolbar button { width:36px; height:36px; border:1px solid #d0d5dd; border-radius:6px; background:transparent; color:inherit; cursor:pointer; }
    .table-format-toolbar button[aria-pressed=true] { background:#edf6e3; color:#31531a; border-color:#81bd43; }
    html[data-theme=dark] .table-format-toolbar { background:#19263b; color:#e0e8f5; border-color:#40516c; }
    html[data-theme=dark] .table-format-toolbar button[aria-pressed=true] { background:#26391e; color:#a8d77a; }
    td.memo-row-end { position:relative; padding-right:34px !important; }
    .remove-memo-row, .memo-editor .remove-memo-row { position:absolute; right:5px; top:50%; transform:translateY(-50%); width:24px; height:24px; border:0; border-radius:4px; padding:0; background:transparent; color:#b42318; cursor:pointer; font-size:20px; line-height:24px; }
    html[data-theme=dark] .remove-memo-row { color:#ffb4ab; }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toolbar = document.querySelector('.table-format-toolbar');
    const selector = 'input[name*="[rows]"], textarea[name*="[rows]"]';
    const formats = new Map((@json(old('table_formats', $memo->table_formats ?? [])) || []).map(item => [item.cell, item]));
    let active = null;
    const style = (node, format = {}) => {
        node.style.fontWeight = Number(format.bold) ? 'bold' : '';
        node.style.fontStyle = Number(format.italic) ? 'italic' : '';
        node.style.textDecoration = Number(format.underline) ? 'underline' : '';
    };
    const paint = input => {
        const format = formats.get(input.name) || {};
        style(input, format);
        if (input.dataset.tableId) {
            const preview = document.getElementById('preview-table-' + input.dataset.tableId);
            preview?.querySelectorAll('[data-row][data-column]').forEach(cell => {
                if (cell.dataset.row === input.dataset.rowIndex && cell.dataset.column === input.dataset.columnName) style(cell, format);
            });
        }
    };
    const refresh = () => {
        toolbar.hidden = !active;
        toolbar.querySelectorAll('[data-cell-format]').forEach(button => {
            button.setAttribute('aria-pressed', String(Boolean(Number(formats.get(active?.name)?.[button.dataset.cellFormat]))));
        });
    };
    const toggle = key => {
        if (!active?.isConnected) return;
        const format = formats.get(active.name) || {cell:active.name, bold:0, italic:0, underline:0};
        format[key] = Number(format[key]) ? 0 : 1;
        formats.set(active.name, format);
        paint(active); refresh(); active.focus();
    };
    document.addEventListener('focusin', event => {
        if (toolbar.contains(event.target) || event.target.closest('[data-memo-format]')) return;
        active = event.target.matches(selector) ? event.target : null;
        refresh();
        if (active) queueMicrotask(() => document.querySelectorAll('[data-memo-format]').forEach(button => button.disabled = false));
    });
    toolbar.querySelectorAll('[data-cell-format]').forEach(button => {
        button.addEventListener('mousedown', event => event.preventDefault());
        button.addEventListener('click', () => toggle(button.dataset.cellFormat));
    });
    document.querySelectorAll('[data-memo-format]').forEach(button => button.addEventListener('click', () => {
        if (active) toggle(button.dataset.memoFormat);
    }));
    document.addEventListener('keydown', event => {
        if (!event.target.matches(selector) || !(event.ctrlKey || event.metaKey)) return;
        const key = {b:'bold', i:'italic', u:'underline'}[event.key.toLowerCase()];
        if (key) { event.preventDefault(); toggle(key); }
    });
    const enhanceRows = () => {
        document.querySelectorAll(selector).forEach(paint);
        document.querySelectorAll('#memoForm table').forEach(table => {
            if (!table.querySelector(selector)) return;
            [...table.tBodies].forEach(body => [...body.rows].forEach(row => {
                if (!row.querySelector(selector) || row.querySelector('[data-delete-memo-row]')) return;
                const cell = row.lastElementChild;
                cell.classList.add('memo-row-end');
                const button = document.createElement('button'); button.type = 'button'; button.className = 'remove-memo-row';
                button.dataset.deleteMemoRow = ''; button.textContent = '×';
                button.setAttribute('aria-label', 'Remove row'); button.title = 'Remove row';
                cell.append(button);
            }));
        });
    };
    document.getElementById('memoForm')?.addEventListener('click', event => {
        const button = event.target.closest('[data-delete-memo-row]');
        if (!button) return;
        const row = button.closest('tr');
        const inputs = [...row.querySelectorAll(selector)];
        const input = inputs[0];
        const body = row.parentElement;
        body.dataset.nextRowIndex = Math.max(Number(body.dataset.nextRowIndex || 0), ...inputs.map(cell => Number(cell.dataset.rowIndex) + 1));
        const preview = document.getElementById('preview-table-' + input.dataset.tableId);
        preview?.querySelectorAll('tbody tr').forEach(previewRow => {
            if (previewRow.querySelector('[data-row]')?.dataset.row === input.dataset.rowIndex) previewRow.remove();
        });
        inputs.forEach(cell => formats.delete(cell.name));
        row.remove(); active = null; refresh();
    });
    enhanceRows();
    const observer = new MutationObserver(enhanceRows);
    document.querySelectorAll('#memoForm, .memo-editor form').forEach(form => {
        observer.observe(form, {childList:true, subtree:true});
        form.addEventListener('submit', () => {
            form.querySelectorAll('[data-table-format-value]').forEach(input => input.remove());
            const append = (name, value) => {
                const input = document.createElement('input'); input.type = 'hidden'; input.name = name; input.value = value;
                input.dataset.tableFormatValue = ''; form.append(input);
            };
            append('table_formats', '');
            let index = 0;
            form.querySelectorAll(selector).forEach(input => {
                const format = formats.get(input.name);
                if (!format) return;
                Object.entries(format).forEach(([key, value]) => append(`table_formats[${index}][${key}]`, value));
                index++;
            });
        });
    });
});
</script>
