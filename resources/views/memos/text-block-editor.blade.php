@php
    $textPositions = \App\Support\MemoTextPositions::forTemplate($template ?? $memo->template);
    $initialTextBlocks = collect(old('text_blocks', $memo->text_blocks ?? []) ?: [])->map(function ($block) {
        if (isset($block['html'])) $block['html'] = \App\Support\MemoTextFormatting::sanitize($block['html']);
        return $block;
    })->values();
@endphp
<section class="memo-text-editor" aria-label="Optional memo text" @if($compact ?? false) data-compact hidden @endif>
    @unless($compact ?? false)
    <h2>Optional memo text</h2>
    <p>Add text just for this memo. Choose where each block appears. To place text between two tables, choose before the second table.</p>
    @endunless
    <input type="hidden" name="text_blocks" value="">
    <div data-text-block-list></div>
    @unless($compact ?? false)
        <button type="button" class="btn btn-secondary" data-add-text>+ Add text</button>
    @endunless
    <template data-text-block-template>
        <div class="memo-text-block">
            <label>Position <select class="form-control" data-block-position>
                @foreach($textPositions as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
            </select></label>
            <div class="memo-text-actions" role="group" aria-label="Text formatting">
                <button type="button" class="btn btn-secondary" data-format="bold" aria-label="Bold" title="Bold (Ctrl+B)"><strong>B</strong></button>
                <button type="button" class="btn btn-secondary" data-format="italic" aria-label="Italic" title="Italic (Ctrl+I)"><em>I</em></button>
                <button type="button" class="btn btn-secondary" data-format="underline" aria-label="Underline" title="Underline (Ctrl+U)"><u>U</u></button>
            </div>
            <div class="form-control memo-rich-text" contenteditable="true" role="textbox" aria-label="Memo text" aria-multiline="true" data-rich-text></div>
            <textarea data-block-text hidden></textarea>
            <input type="hidden" data-block-html>
            <div class="memo-text-actions">
                <button type="button" class="btn btn-secondary" data-text-up>Move up</button>
                <button type="button" class="btn btn-secondary" data-text-down>Move down</button>
                <button type="button" class="btn btn-secondary" data-remove-text>Remove</button>
            </div>
        </div>
    </template>
</section>
<style>
    .memo-text-editor { grid-column:1 / -1; margin:24px 0; padding:18px; border:1px solid var(--ui-line); border-radius:10px; color:var(--ui-ink); }
    .memo-text-editor h2 { font-size:16px; margin:0 0 8px; }
    .memo-text-editor > p { font-size:12px; color:var(--ui-muted); line-height:1.6; }
    .memo-text-block { margin:16px 0; padding:14px; border:1px solid var(--ui-line); border-radius:8px; }
    .memo-text-block label { display:block; margin:8px 0; font-size:13px; }
    .memo-text-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
    .memo-rich-text { min-height:100px; white-space:pre-wrap; margin-top:10px; }
    .memo-rich-text strong, .memo-rich-text b { font-weight:700; }
    .memo-rich-text em, .memo-rich-text i { font-style:italic; }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editor = document.querySelector('.memo-text-editor');
    const list = editor.querySelector('[data-text-block-list]');
    const prototype = editor.querySelector('template');
    const positions = @json(array_keys($textPositions));
    let activeText = null;
    const toolbarButtons = document.querySelectorAll('[data-memo-format]');
    document.addEventListener('focusin', event => {
        if (event.target.closest('[data-memo-format], [data-format]')) return;
        activeText = event.target.closest('[data-rich-text]');
        toolbarButtons.forEach(button => button.disabled = !activeText);
    });
    toolbarButtons.forEach(button => {
        button.addEventListener('mousedown', event => event.preventDefault());
        button.addEventListener('click', () => {
            if (!activeText || !activeText.isConnected) return;
            activeText.focus();
            document.execCommand('styleWithCSS', false, false);
            document.execCommand(button.dataset.memoFormat, false, null);
            sync();
        });
    });
    const sync = () => {
        if (editor.hasAttribute('data-compact')) editor.hidden = list.children.length === 0;
        [...list.children].sort((a, b) => a.querySelector('select').selectedIndex - b.querySelector('select').selectedIndex).forEach((block, index) => {
            if (list.children[index] !== block) list.insertBefore(block, list.children[index]);
        });
        document.querySelectorAll('[data-text-position]').forEach(slot => slot.replaceChildren());
        [...list.children].forEach((block, index) => {
            const select = block.querySelector('select');
            const textarea = block.querySelector('textarea');
            const richText = block.querySelector('[data-rich-text]');
            const html = block.querySelector('[data-block-html]');
            textarea.value = richText.innerText;
            html.value = richText.innerHTML;
            html.name = `text_blocks[${index}][html]`;
            select.name = `text_blocks[${index}][position]`;
            textarea.name = `text_blocks[${index}][text]`;
            block.querySelector('[data-text-up]').disabled = index === 0 && select.selectedIndex === 0;
            block.querySelector('[data-text-down]').disabled = index === list.children.length - 1 && select.selectedIndex === positions.length - 1;
            document.querySelectorAll('[data-text-position]').forEach(slot => {
                if (slot.dataset.textPosition !== select.value || !textarea.value.trim()) return;
                const paragraph = document.createElement('div');
                paragraph.style.cssText = 'white-space:pre-wrap;overflow-wrap:break-word;margin:12px 0';
                paragraph.append(...[...richText.childNodes].map(node => node.cloneNode(true)));
                slot.append(paragraph);
            });
        });
    };
    const add = (value = {}) => {
        const block = prototype.content.firstElementChild.cloneNode(true);
        block.querySelector('select').value = value.position || 'after_subject';
        const richText = block.querySelector('[data-rich-text]');
        if (value.html) richText.innerHTML = value.html;
        else richText.textContent = value.text || '';
        richText.addEventListener('paste', event => {
            event.preventDefault();
            document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
            sync();
        });
        richText.addEventListener('drop', event => event.preventDefault());
        block.querySelectorAll('[data-format]').forEach(button => {
            button.addEventListener('mousedown', event => event.preventDefault());
            button.addEventListener('click', () => {
                richText.focus();
                document.execCommand('styleWithCSS', false, false);
                document.execCommand(button.dataset.format, false, null);
                sync();
            });
        });
        list.append(block);
        sync();
        return block;
    };
    (@json($initialTextBlocks) || []).forEach(add);
    editor.querySelector('[data-add-text]')?.addEventListener('click', () => add().querySelector('[data-rich-text]').focus());
    document.querySelectorAll('[data-add-text-at]').forEach(button => button.addEventListener('click', () => {
        add({position: button.dataset.addTextAt}).querySelector('[data-rich-text]').focus();
    }));
    list.addEventListener('input', sync);
    list.addEventListener('change', sync);
    list.addEventListener('click', event => {
        const block = event.target.closest('.memo-text-block');
        if (!block) return;
        const select = block.querySelector('select');
        if (event.target.matches('[data-remove-text]')) block.remove();
        if (event.target.matches('[data-text-up]')) {
            const previous = block.previousElementSibling;
            if (previous && previous.querySelector('select').value === select.value) list.insertBefore(block, previous);
            else select.selectedIndex = Math.max(0, select.selectedIndex - 1);
        }
        if (event.target.matches('[data-text-down]')) {
            const next = block.nextElementSibling;
            if (next && next.querySelector('select').value === select.value) list.insertBefore(next, block);
            else select.selectedIndex = Math.min(positions.length - 1, select.selectedIndex + 1);
        }
        sync();
    });
    sync();
});
</script>
