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
<dialog id="memo-insert-dialog" aria-labelledby="memo-insert-title">
    <form id="memo-insert-form">
        <h2 id="memo-insert-title">Insert field</h2>
        <p>The new item will be saved with this memo only.</p>
        <label>Name <input class="form-control" name="label" required maxlength="255"></label>
        <label data-field-type>Field type <select class="form-control" name="type">
            <option value="text">Text</option><option value="textarea">Paragraph</option>
            <option value="number">Number</option><option value="date">Date</option>
        </select></label>
        <section data-table-columns hidden>
            <h3>Columns</h3>
            <div data-column-list></div>
            <button type="button" class="btn btn-secondary" data-add-column>+ Column</button>
        </section>
        <p data-insert-error role="alert"></p>
        <div class="memo-insert-actions">
            <button type="button" class="btn btn-secondary" data-cancel-insert>Cancel</button>
            <button type="submit" class="btn btn-primary">Insert</button>
        </div>
    </form>
</dialog>
<style>
    .memo-insert-bar { position:sticky; top:0; z-index:20; background:#fff; border:1px solid #ddd; border-radius:10px; padding:12px 18px; margin-bottom:20px; box-shadow:0 3px 12px #00000008; }
    .memo-insert-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .memo-insert-actions > span { font-size:12px; color:#666; }
    #memo-insert-status:empty { display:none; }
    #memo-insert-status { margin:10px 0 0; font-size:13px; }
    #memo-insert-dialog { width:min(560px, calc(100% - 32px)); max-height:85vh; overflow:auto; border:1px solid #ddd; border-radius:12px; padding:24px; }
    #memo-insert-dialog::backdrop { background:#0006; }
    #memo-insert-dialog label { display:block; margin:14px 0; }
    #memo-insert-dialog p { font-size:13px; line-height:1.5; }
    [data-insert-error] { color:#b42318; }
    .insert-column { display:grid; grid-template-columns:1fr 120px auto; gap:8px; align-items:center; margin:10px 0; }
    @media(max-width:600px) { .insert-column { grid-template-columns:1fr; } .memo-insert-bar { position:static; } }
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
    const addColumn = () => {
        if (columns.children.length >= 20) return;
        const row = element('div', 'insert-column');
        const label = element('input', 'form-control');
        label.required = true; label.maxLength = 255; label.placeholder = 'Column name'; label.setAttribute('aria-label', 'Column name');
        const type = element('select', 'form-control');
        type.setAttribute('aria-label', 'Column type');
        [['text','Text'],['textarea','Paragraph'],['number','Number'],['decimal','Decimal'],['date','Date']].forEach(([value, title]) => type.add(new Option(title, value)));
        const remove = element('button', 'btn btn-secondary', 'Remove');
        remove.type = 'button'; remove.onclick = () => row.remove();
        row.append(label, type, remove); columns.append(row);
    };
    document.querySelectorAll('[data-insert]').forEach(button => button.addEventListener('click', () => {
        kind = button.dataset.insert; form.reset(); columns.replaceChildren(); error.textContent = '';
        document.getElementById('memo-insert-title').textContent = 'Insert ' + kind;
        form.querySelector('[data-field-type]').hidden = kind !== 'field';
        form.querySelector('[data-table-columns]').hidden = kind !== 'table';
        if (kind === 'table') { addColumn(); addColumn(); }
        dialog.showModal(); form.elements.label.focus();
    }));
    form.querySelector('[data-add-column]').onclick = addColumn;
    form.querySelector('[data-cancel-insert]').onclick = () => { if (!busy) dialog.close(); };
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
