<fieldset class="memo-format-choice">
    <legend>Memo format</legend>
    <div class="memo-format-options">
        @foreach(['template' => ['Template fields', 'Use the predefined fields and tables.'], 'freeform' => ['Without fields', 'Write a letter-style memo with formatted text.']] as $format => [$title, $description])
            <label title="{{ $description }}">
                <input type="radio" name="creation_type" value="{{ $format }}" @checked(old('creation_type', $memo?->creation_type ?? request('format', 'template')) === $format)>
                <span>{{ $title }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
