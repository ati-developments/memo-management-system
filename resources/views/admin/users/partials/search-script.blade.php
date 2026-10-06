<script>
(() => {
    const input = document.getElementById('registered-users-search');
    const results = document.getElementById('registered-users-results');
    const status = document.getElementById('users-search-status');
    let timer;
    let controller;
    let version = 0;

    async function search(url, requestVersion) {
        controller = new AbortController();
        status.textContent = 'Searching…';
        results.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, {
                signal: controller.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok || response.redirected) throw new Error('Search failed');
            const html = await response.text();
            if (requestVersion !== version) return;
            results.innerHTML = html;
            status.textContent = '';
        } catch (error) {
            if (requestVersion === version && error.name !== 'AbortError') {
                status.textContent = 'Search could not load. Press Enter to retry.';
            }
        } finally {
            if (requestVersion === version) results.removeAttribute('aria-busy');
        }
    }

    function schedule(delay = 350, pageUrl = null) {
        clearTimeout(timer);
        controller?.abort();
        const requestVersion = ++version;
        const url = new URL(pageUrl || input.form.action, window.location.origin);
        url.searchParams.set('search', input.value);
        timer = setTimeout(() => search(url, requestVersion), delay);
    }
    input.addEventListener('input', () => schedule());
    input.form.addEventListener('submit', (event) => {
        event.preventDefault();
        schedule(0);
    });
    document.getElementById('clear-users-search').addEventListener('click', (event) => {
        event.preventDefault();
        input.value = '';
        input.focus();
        schedule(0);
    });
    results.addEventListener('click', (event) => {
        const link = event.target.closest('.registered-users-pagination a');
        if (!link) return;
        event.preventDefault();
        schedule(0, link.href);
    });
})();
</script>
