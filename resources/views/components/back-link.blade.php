@props(['fallback'])

<a href="{{ $fallback }}"
   onclick="if (!event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey && window.history.length > 1) { event.preventDefault(); window.history.back(); }">&larr; Back</a>
