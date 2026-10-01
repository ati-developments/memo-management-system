<section class="memo-insert-bar" aria-label="Insert into memo">
    <div class="memo-insert-actions">
        <strong>Insert</strong>
        <button type="button" class="btn btn-secondary" data-insert="field">+ Field</button>
        <button type="button" class="btn btn-secondary" data-insert="table">+ Table</button>
        <button type="button" class="btn btn-secondary" data-add-text-at="after_subject">+ Text</button>
        <button type="button" class="btn btn-secondary" data-add-attachments>+ Attachments</button>
        <button type="button" class="btn btn-secondary" data-memo-format="bold" aria-label="Bold" title="Select words in a text block, then apply bold" disabled><strong>B</strong></button>
        <button type="button" class="btn btn-secondary" data-memo-format="italic" aria-label="Italic" title="Select words in a text block, then apply italic" disabled><em>I</em></button>
        <button type="button" class="btn btn-secondary" data-memo-format="underline" aria-label="Underline" title="Select words in a text block, then apply underline" disabled><u>U</u></button>
        <span>Changes apply to this memo only. Use Edit Template for permanent changes.</span>
    </div>
    <p id="memo-insert-status" role="status" aria-live="polite"></p>
</section>
<dialog id="memo-insert-dialog" aria-labelledby="memo-insert-title" aria-describedby="memo-insert-description">
    <form id="memo-insert-form">
        <header class="insert-dialog-header">
            <div>
                <span class="insert-dialog-badge">THIS MEMO ONLY</span>
                <h2 id="memo-insert-title">Insert field</h2>
                <p id="memo-insert-description">Add a detail to your memo without changing the template.</p>
            </div>
            <button type="button" class="insert-dialog-close" data-cancel-insert aria-label="Close insert dialog">&times;</button>
        </header>
        <div class="insert-dialog-body">
        <label><span data-name-label>Field name</span> <input class="form-control" name="label" required maxlength="255" placeholder="e.g. Purchase reference" autocomplete="off"></label>
        <label data-field-type>Field type <select class="form-control" name="type">
            <option value="text">Text</option><option value="textarea">Paragraph</option>
            <option value="number">Number</option><option value="date">Date</option>
        </select></label>
        <section data-table-columns hidden>
            <div class="insert-columns-heading"><h3>Table columns</h3><span data-column-count>0 / 20</span></div>
            <p class="insert-dialog-hint">Choose a name and format for each column.</p>
            <div data-column-list></div>
            <button type="button" class="btn btn-secondary insert-add-column" data-add-column>+ Add column</button>
        </section>
        <p data-insert-error role="alert"></p>
        </div>
        <footer class="insert-dialog-footer">
            <button type="button" class="btn btn-secondary" data-cancel-insert>Cancel</button>
            <button type="submit" class="btn btn-primary">Add field</button>
        </footer>
    </form>
</dialog>
<style>
    .memo-insert-bar { position:sticky; top:0; z-index:20; background:#fff; border:1px solid #ddd; border-radius:10px; padding:12px 18px; margin-bottom:20px; box-shadow:0 3px 12px #00000008; }
    .memo-insert-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .memo-insert-actions > span { font-size:12px; color:#666; }
    #memo-insert-status:empty { display:none; }
    #memo-insert-status { margin:10px 0 0; font-size:13px; }
    #memo-insert-dialog { --insert-bg:#fff; --insert-soft:#f6f8f4; --insert-ink:#24312b; --insert-muted:#68766c; --insert-border:#dde5da; box-sizing:border-box; width:min(640px, calc(100% - 32px)); max-height:90vh; max-height:90dvh; overflow:auto; border:1px solid var(--insert-border); border-radius:20px; padding:0; background:var(--insert-bg); color:var(--insert-ink); box-shadow:0 24px 80px #13251640; }
    #memo-insert-dialog::backdrop { background:#101b29a6; backdrop-filter:blur(4px); }
    #memo-insert-form { margin:0; }
    #memo-insert-dialog .insert-dialog-header { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; padding:26px 28px 22px; border-bottom:1px solid var(--insert-border); background:var(--insert-soft); }
    #memo-insert-dialog .insert-dialog-badge { display:inline-block; padding:5px 9px; border-radius:6px; background:var(--primary-soft); color:var(--primary-link); font-size:10px; font-weight:700; letter-spacing:.09em; }
    #memo-insert-dialog h2 { margin:14px 0 8px; font-size:24px; line-height:1.2; color:var(--insert-ink); }
    #memo-insert-dialog p { margin:0; font-size:13px; line-height:1.6; color:var(--insert-muted); }
    #memo-insert-dialog .insert-dialog-close { display:grid; place-items:center; flex:none; width:34px; height:34px; padding:0; border:1px solid var(--insert-border); border-radius:50%; background:var(--insert-bg); color:var(--insert-muted); font-size:24px; cursor:pointer; }
    #memo-insert-dialog .insert-dialog-close:hover { color:var(--insert-ink); background:var(--primary-soft); }
    #memo-insert-dialog .insert-dialog-body { padding:24px 28px; }
    #memo-insert-dialog label { display:block; margin:0 0 20px; font-size:13px; font-weight:600; color:var(--insert-ink); }
    #memo-insert-dialog [hidden] { display:none; }
    #memo-insert-dialog .form-control { box-sizing:border-box; width:100%; min-width:0; min-height:44px; padding:10px 12px; margin-top:8px; border:1px solid var(--insert-border); border-radius:9px; background:var(--insert-bg); color:var(--insert-ink); font:inherit; font-size:14px; font-weight:400; }
    #memo-insert-dialog .form-control::placeholder { color:var(--insert-muted); opacity:.85; }
    #memo-insert-dialog :is(button,input,select):focus-visible { outline:2px solid var(--primary); outline-offset:3px; }
    #memo-insert-dialog .insert-columns-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    #memo-insert-dialog h3 { margin:0; font-size:14px; color:var(--insert-ink); }
    #memo-insert-dialog [data-column-count] { color:var(--insert-muted); font-size:12px; font-variant-numeric:tabular-nums; }
    #memo-insert-dialog .insert-dialog-hint { margin:6px 0 14px; }
    #memo-insert-dialog .insert-column { display:grid; grid-template-columns:minmax(0,1fr) 140px 36px; gap:10px; align-items:end; margin:10px 0; padding:14px; border:1px solid var(--insert-border); border-radius:12px; background:var(--insert-soft); }
    #memo-insert-dialog .insert-column label { margin:0; font-size:11px; }
    #memo-insert-dialog .insert-column .form-control { margin-top:6px; }
    #memo-insert-dialog .insert-remove-column { height:44px; padding:0; border:0; border-radius:8px; background:transparent; color:var(--insert-muted); font-size:22px; cursor:pointer; }
    #memo-insert-dialog .insert-remove-column:hover { background:#b4231812; color:#d04438; }
    #memo-insert-dialog .btn { min-height:42px; padding:10px 18px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; }
    #memo-insert-dialog .btn-secondary { background:var(--insert-bg); color:var(--insert-ink); border:1px solid var(--insert-border); }
    #memo-insert-dialog .btn-secondary:hover { background:var(--insert-soft); }
    #memo-insert-dialog .btn-primary { background:var(--primary); color:var(--primary-ink); border:1px solid var(--primary); }
    #memo-insert-dialog .btn-primary:hover { background:var(--primary-hover); }
    #memo-insert-dialog .insert-add-column { width:100%; border-style:dashed; margin-top:4px; }
    #memo-insert-dialog button:disabled { opacity:.5; cursor:not-allowed; }
    #memo-insert-dialog [data-insert-error]:empty { display:none; }
    #memo-insert-dialog [data-insert-error]:not(:empty) { margin-top:16px; padding:12px; border:1px solid #d0443855; border-radius:8px; background:#d0443810; color:#b42318; }
    #memo-insert-dialog .insert-dialog-footer { position:sticky; bottom:0; display:flex; justify-content:flex-end; gap:10px; padding:18px 28px; background:var(--insert-bg); border-top:1px solid var(--insert-border); }
    html[data-theme=dark] #memo-insert-dialog { --insert-bg:#19263b; --insert-soft:#142034; --insert-ink:#e0e8f5; --insert-muted:#a3b1c7; --insert-border:#354761; color-scheme:dark; }
    html[data-theme=dark] #memo-insert-dialog [data-insert-error] { color:#ffb4ab; }
    @media(max-width:600px) { #memo-insert-dialog .insert-dialog-header, #memo-insert-dialog .insert-dialog-body, #memo-insert-dialog .insert-dialog-footer { padding:20px; } #memo-insert-dialog .insert-column { grid-template-columns:minmax(0,1fr) 36px; } #memo-insert-dialog .insert-column label:first-child { grid-column:1 / -1; } .memo-insert-bar { position:static; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const attachmentInput = document.getElementById('memo-attachments');
    const attachmentList = document.getElementById('memo-attachment-list');
    document.querySelector('[data-add-attachments]')?.addEventListener('click', () => attachmentInput?.click());
    attachmentInput?.addEventListener('change', () => {
        if (!attachmentList) return;
        attachmentList.replaceChildren();
        [...attachmentInput.files].forEach(file => {
            const item = document.createElement('li');
            item.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            attachmentList.append(item);
        });
        const status = document.getElementById('memo-insert-status');
        if (status) status.textContent = attachmentInput.files.length ? `${attachmentInput.files.length} attachment(s) selected.` : 'No attachments selected.';
    });
    const dialog = document.getElementById('memo-insert-dialog');
    const form = document.getElementById('memo-insert-form');
    const columns = form.querySelector('[data-column-list]');
    const error = form.querySelector('[data-insert-error]');
    const submit = form.querySelector('[type=submit]');
    let kind = 'field';
    let busy = false;
    const element = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };
    const updateColumnCount = () => {
        form.querySelector('[data-column-count]').textContent = `${columns.children.length} / 20`;
        form.querySelector('[data-add-column]').disabled = columns.children.length >= 20;
    };
    const addColumn = () => {
        if (columns.children.length >= 20) return;
        const row = element('div', 'insert-column');
        const label = element('input', 'form-control');
        label.required = true; label.maxLength = 255; label.placeholder = 'Column name'; label.setAttribute('aria-label', 'Column name');
        const type = element('select', 'form-control');
        type.setAttribute('aria-label', 'Column type');
        [['text','Text'],['textarea','Paragraph'],['number','Number'],['decimal','Decimal'],['date','Date']].forEach(([value, title]) => type.add(new Option(title, value)));
        const nameLabel = element('label', '', 'Column name'); nameLabel.append(label);
        const typeLabel = element('label', '', 'Format'); typeLabel.append(type);
        const remove = element('button', 'insert-remove-column', '×');
        remove.type = 'button'; remove.setAttribute('aria-label', 'Remove column'); remove.title = 'Remove column';
        remove.onclick = () => { row.remove(); updateColumnCount(); form.querySelector('[data-add-column]').focus(); };
        row.append(nameLabel, typeLabel, remove); columns.append(row);
        updateColumnCount();
        return label;
    };
    document.querySelectorAll('[data-insert]').forEach(button => button.addEventListener('click', () => {
        kind = button.dataset.insert; form.reset(); columns.replaceChildren(); error.textContent = '';
        document.getElementById('memo-insert-title').textContent = 'Insert ' + kind;
        form.querySelector('[data-name-label]').textContent = kind === 'field' ? 'Field name' : 'Table name';
        form.elements.label.placeholder = kind === 'field' ? 'e.g. Purchase reference' : 'e.g. Cost breakdown';
        submit.textContent = kind === 'field' ? 'Add field' : 'Add table';
        document.getElementById('memo-insert-description').textContent = kind === 'field'
            ? 'Add a detail to your memo without changing the template.'
            : 'Organize memo details into a table. You can add rows after inserting it.';
        form.querySelector('[data-field-type]').hidden = kind !== 'field';
        form.querySelector('[data-table-columns]').hidden = kind !== 'table';
        if (kind === 'table') { addColumn(); addColumn(); }
        dialog.showModal(); form.elements.label.focus();
    }));
    form.querySelector('[data-add-column]').onclick = () => addColumn()?.focus();
    form.querySelectorAll('[data-cancel-insert]').forEach(button => { button.onclick = () => { if (!busy) dialog.close(); }; });
    dialog.addEventListener('cancel', event => { if (busy) event.preventDefault(); });
    let itemIndex = 0;
    const insertItem = payload => {
        const index = itemIndex++;
        const prefix = `inserted_items[${index}]`;
        const hidden = (name, value) => {
            const input = element('input'); input.type = 'hidden'; input.name = `${prefix}[${name}]`; input.value = value;
            document.getElementById('memoForm').append(input);
        };
        hidden('kind', payload.kind); hidden('label', payload.label);
        if (payload.kind === 'field') hidden('type', payload.type);
        const item = {id: 'local_' + index, field_name: 'local_field_' + index, field_label: payload.label, field_type: payload.type, table_label: payload.label,
            columns: (payload.columns || []).map((column, i) => ({...column, column_name: 'column_' + i}))};
        item.columns.forEach((column, i) => Object.entries(column).forEach(([key, value]) => hidden(`columns][${i}][${key}`, value)));
        const result = {kind: payload.kind, item};
        renderItem(result, prefix, payload);
    };
    form.addEventListener('submit', event => {
        event.preventDefault(); if (busy) return;
        const payload = {kind, label:form.elements.label.value};
        if (kind === 'field') payload.type = form.elements.type.value;
        else payload.columns = [...columns.children].map(row => ({column_label:row.querySelector('input').value, column_type:row.querySelector('select').value}));
        busy = true; submit.disabled = true; error.textContent = '';
        try {
            if (kind === 'table' && !payload.columns.length) throw new Error('Add at least one column.');
            insertItem(payload);
            dialog.close();
            document.getElementById('memo-insert-status').textContent = payload.label + ' added to this memo only.';
        } catch (exception) {
            error.textContent = exception.message || 'Unable to insert. Please try again.';
        } finally { busy = false; submit.disabled = false; }
    });
    const renderItem = (result, prefix, payload) => {
            const item = result.item;
            if (result.kind === 'field') {
                const group = element('div', 'form-group');
                const label = element('label', '', item.field_label); label.htmlFor = item.field_name;
                const input = element(item.field_type === 'textarea' ? 'textarea' : 'input', 'form-control');
                input.id = item.field_name; input.name = `${prefix}[value]`; input.value = payload.value || '';
                if (item.field_type !== 'textarea') input.type = item.field_type;
                if (item.field_type === 'number') input.step = 'any';
                group.append(label, input); document.getElementById('inserted-memo-fields').append(group);
                const row = element('div', 'memo-detail-row');
                const value = element('div', 'memo-value', '—'); value.id = 'preview_' + item.field_name;
                row.append(element('div', 'memo-label', item.field_label), value);
                document.querySelector('.additional-preview-fields').append(row);
                input.addEventListener('input', () => updatePreview(input.id));
                updatePreview(input.id);
            } else {
                const group = element('div', 'memo-table-form-section');
                group.append(element('h4', 'memo-table-title', item.table_label));
                const table = element('table', 'memo-entry-table');
                const head = table.createTHead().insertRow();
                item.columns.forEach(column => head.append(element('th', '', column.column_label)));
                table.createTBody().id = 'table-body-' + item.id;
                const wrapper = element('div', 'memo-table-wrapper'); wrapper.append(table); group.append(wrapper);
                const addRow = (values = {}) => {
                    addTableRow(item.id);
                    const row = table.tBodies[0].lastElementChild;
                    row.querySelectorAll('input, textarea').forEach(input => {
                        input.name = input.name.replace(`tables[${item.id}]`, prefix);
                        input.value = values[input.dataset.columnName] || '';
                        input.dispatchEvent(new Event('input', {bubbles: true}));
                    });
                };
                const add = element('button', 'btn btn-secondary', '+ Add row'); add.type = 'button'; add.onclick = () => addRow(); group.append(add);
                document.getElementById('inserted-memo-tables').append(group);
                const preview = element('div', 'preview-table-section'); preview.append(element('h3', 'document-section-title', item.table_label));
                const previewTable = element('table', 'charges-table dynamic-preview-table'); previewTable.id = 'preview-table-' + item.id;
                const previewHead = previewTable.createTHead().insertRow(); item.columns.forEach(column => previewHead.append(element('th', '', column.column_label)));
                previewTable.createTBody(); preview.append(previewTable);
                document.querySelector('[data-text-position="before_recommendation"]').before(preview);
                templateTables.push({id:item.id, columns:item.columns.map(column => ({name:column.column_name, type:column.column_type}))});
                const rows = Object.values(payload.rows || {});
                (rows.length ? rows : [{}]).forEach(addRow);
            }
    };
    Object.values(@json(old('inserted_items', []))).forEach(insertItem);
});
</script>
