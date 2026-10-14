<fieldset id="freeform-body-fields" hidden disabled style="border:0; padding:0; margin:18px 0; min-width:0;">
    <label id="freeform-body-label" for="freeform-source">Memo body</label>
    <div class="freeform-format-tools" role="toolbar" aria-label="Memo body formatting">
        @foreach(['bold' => 'Bold', 'italic' => 'Italic', 'underline' => 'Underline', 'insertUnorderedList' => 'Bullet list', 'insertOrderedList' => 'Numbered list'] as $command => $label)
            <button type="button" data-freeform-command="{{ $command }}">{{ $label }}</button>
        @endforeach
        <button type="button" data-freeform-command="formatBlock" data-value="h3">Heading</button>
        <button type="button" data-freeform-command="formatBlock" data-value="p">Paragraph</button>
        <label class="freeform-font-size-label" for="freeform-font-size">Size</label>
        <select id="freeform-font-size" aria-label="Memo body font size">
            <option value="12px">12 px</option><option value="14px">14 px</option><option value="16px" selected>16 px</option><option value="18px">18 px</option><option value="24px">24 px</option>
        </select>
    </div>
    <div class="freeform-table-tools" role="group" aria-label="Insert a table">
        <label for="body-table-rows">Rows <input id="body-table-rows" type="number" min="1" max="20" value="3"></label>
        <label for="body-table-columns">Columns <input id="body-table-columns" type="number" min="1" max="8" value="2"></label>
        <button type="button" id="body-insert-table">Insert table</button>
        <span id="body-table-status" role="status"></span>
    </div>
    <textarea id="freeform-source" name="content" hidden>{{ old('content', $memo?->content) }}</textarea>
    <div class="freeform-editor-wrap">
        <div class="freeform-selected-table-tools" role="toolbar" aria-label="Selected table tools" hidden>
            <button type="button" data-body-table-action="row" aria-label="Add row" title="Add row"><svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2 3h12v7H2zM2 6h12M6 3v7M10 3v7M8 11v4m-2-2h4"/></svg></button>
            <button type="button" data-body-table-action="column" aria-label="Add column" title="Add column"><svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2 2h8v12H2zM5 2v12M8 2v12M11 8h4m-2-2v4"/></svg></button>
            <button type="button" data-body-table-action="remove" aria-label="Remove table" title="Remove table"><svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2 3h12v10H2zM2 6h12M6 3v3M10 3v3M5 9l4 3m0-3-4 3"/></svg></button>
        </div>
        <div id="freeform-rich-editor" contenteditable="true" role="textbox" aria-labelledby="freeform-body-label" aria-multiline="true">{!! \App\Support\MemoTextFormatting::sanitize(old('content', $memo?->content) ?: '<p></p>') !!}</div>
    </div>
</fieldset>
