@php
    $templateWorkflow = $approvalWorkflow ? [
        'approval_type' => $approvalWorkflow->approval_type,
        'approval_rule' => $approvalWorkflow->approval_rule,
        'minimum_approvals' => $approvalWorkflow->minimum_approvals,
        'steps' => $approvalWorkflow->steps->map(fn ($step) => ['approver_id' => $step->approver_id])->values()->all(),
    ] : ['approval_type' => 'sequential', 'approval_rule' => 'all', 'minimum_approvals' => null, 'steps' => []];
    $memoWorkflow = old('workflow_config', $memo->workflow_config ?? $templateWorkflow);
@endphp
<section class="memo-workflow" aria-labelledby="memo-workflow-title">
    <h3 id="memo-workflow-title">Approval Workflow</h3>
    <p>Starts with the template workflow. Changes here apply to this memo only.</p>
    <label>Approval order
        <select class="form-control" name="workflow_config[approval_type]" data-workflow-type>
            <option value="sequential">Sequential — in the order below</option>
            <option value="open">Open — in any order</option>
        </select>
    </label>
    <label>Approvals needed
        <select class="form-control" name="workflow_config[approval_rule]" data-workflow-rule>
            <option value="all">All approvers</option><option value="any">Any one approver</option><option value="minimum">Minimum number</option>
        </select>
    </label>
    <label data-workflow-minimum>Minimum approvals
        <input class="form-control" type="number" min="1" name="workflow_config[minimum_approvals]">
    </label>
    <p>You remain the prepared-by signer. Add the other approvers in order below.</p>
    <div data-workflow-steps></div>
    <div class="workflow-actions">
        <button type="button" class="btn btn-secondary" data-workflow-add>+ Add approver</button>
        <button type="button" class="btn btn-secondary" data-workflow-reset>Use template workflow</button>
    </div>
</section>
<style>
    .memo-workflow { margin:24px 0; padding:18px; border:1px solid var(--ui-line, #d0d5dd); border-radius:10px; }
    .memo-workflow h3 { margin:0 0 10px; }
    .memo-workflow p { font-size:12px; line-height:1.5; }
    .memo-workflow label { display:block; margin:12px 0; }
    .memo-workflow .form-control { width:100%; box-sizing:border-box; padding:10px; }
    .workflow-step { display:flex; align-items:center; gap:6px; margin:10px 0; }
    .workflow-step select { min-width:0; flex:1; }
    .workflow-step button { background:transparent; color:inherit; border:1px solid #98a2b3; border-radius:5px; padding:6px; cursor:pointer; }
    .workflow-actions { display:flex; flex-wrap:wrap; gap:8px; }
    .memo-workflow [hidden] { display:none; }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editor = document.querySelector('.memo-workflow');
    const list = editor.querySelector('[data-workflow-steps]');
    const users = @json($workflowUsers);
    const defaults = @json($templateWorkflow);
    const type = editor.querySelector('[data-workflow-type]');
    const rule = editor.querySelector('[data-workflow-rule]');
    const minimum = editor.querySelector('[data-workflow-minimum]');
    const sync = () => {
        minimum.hidden = rule.value !== 'minimum';
        minimum.querySelector('input').required = rule.value === 'minimum';
        minimum.querySelector('input').disabled = rule.value !== 'minimum';
        minimum.querySelector('input').max = list.children.length;
        const preview = document.querySelector('.document-preview .signature-section');
        preview?.querySelectorAll('[data-workflow-signature]').forEach(node => node.remove());
        const count = list.children.length;
        const labels = count === 2 ? ['Checked by', 'Confirmed by'] : count === 4 ? ['Recommended by', 'Approved by', 'Approved by', 'Approved by'] : [];
        [...list.children].forEach((row, i) => {
            const select = row.querySelector('select');
            select.name = `workflow_config[steps][${i}][approver_id]`;
            select.setAttribute('aria-label', `Approver ${i + 1}`);
            row.querySelector('[data-up]').disabled = i === 0;
            row.querySelector('[data-down]').disabled = i === count - 1;
            if (!preview) return;
            const user = users.find(user => String(user.id) === select.value);
            const block = document.createElement('div'); block.className = 'signature-block'; block.dataset.workflowSignature = '';
            [['signature-title', labels[i] || 'Approved by'], ['signature-space', ''], ['signature-name', user?.name || 'Approver'], ['signature-designation', user?.designation || '']].forEach(([className, text]) => {
                const node = document.createElement('div'); node.className = className; node.textContent = text; block.append(node);
            });
            preview.append(block);
        });
    };
    const add = (id = '') => {
        if (list.children.length >= 50) return;
        const row = document.createElement('div'); row.className = 'workflow-step';
        const select = document.createElement('select'); select.className = 'form-control'; select.required = true;
        select.add(new Option('Select approver', ''));
        users.forEach(user => select.add(new Option(user.name + (user.designation ? ' — ' + user.designation : ''), user.id)));
        select.value = String(id); select.addEventListener('change', sync); row.append(select);
        [['up','↑','Move approver up'], ['down','↓','Move approver down'], ['remove','×','Remove approver']].forEach(([action, text, label]) => {
            const button = document.createElement('button'); button.type = 'button'; button.textContent = text; button.setAttribute('aria-label', label); button.dataset[action] = '';
            button.addEventListener('click', () => {
                if (action === 'up' && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
                if (action === 'down' && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
                if (action === 'remove') row.remove();
                sync();
            }); row.append(button);
        });
        list.append(row); sync();
    };
    const load = config => {
        type.value = config.approval_type; rule.value = config.approval_rule;
        minimum.querySelector('input').value = config.minimum_approvals || '';
        list.replaceChildren(); Object.values(config.steps || []).forEach(step => add(step.approver_id)); sync();
    };
    rule.addEventListener('change', sync);
    editor.querySelector('[data-workflow-add]').addEventListener('click', () => add());
    editor.querySelector('[data-workflow-reset]').addEventListener('click', () => load(defaults));
    load(@json($memoWorkflow));
});
</script>
