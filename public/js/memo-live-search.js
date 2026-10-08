(() => {
    const pageSelector = '.my-memos-page, .approvals-page, .all-memos-page, #settings-templates';
    const page = document.querySelector(pageSelector);
    if (!page) return;
    const inputSelector = 'input[name="search"], input[name="template_search"]';

    let timer;
    let controller;
    let revision = 0;

    const search = async (form, version) => {
        const input = form.querySelector(inputSelector);
        const url = new URL(form.action, location.href);
        url.search = new URLSearchParams(new FormData(form)).toString();
        url.searchParams.delete('page');
        url.searchParams.delete('templates_page');
        controller = new AbortController();
        page.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url, {
                signal: controller.signal,
                headers: { Accept: 'text/html' },
            });
            if (version !== revision) return;
            if (!response.ok || response.redirected) throw new Error('Search unavailable');
            const documentResult = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (version !== revision) return;
            const result = documentResult.querySelector(pageSelector);
            if (!result) throw new Error('Missing search results');

            const focused = document.activeElement === input;
            const start = input.selectionStart;
            const end = input.selectionEnd;
            page.replaceChildren(...result.childNodes);
            history.replaceState(null, '', url);
            if (focused) {
                const replacement = page.querySelector(inputSelector);
                replacement.focus({ preventScroll: true });
                if (start !== null && end !== null) replacement.setSelectionRange(start, end);
            }
        } catch (error) {
            if (error.name !== 'AbortError' && version === revision) location.assign(url);
        } finally {
            if (version === revision) page.removeAttribute('aria-busy');
        }
    };

    const schedule = (input, immediate = false) => {
        clearTimeout(timer);
        controller?.abort();
        const version = ++revision;
        page.removeAttribute('aria-busy');
        timer = setTimeout(() => search(input.form, version), immediate ? 0 : 350);
    };

    page.addEventListener('input', event => {
        if (!event.target.matches(inputSelector)) return;
        if (event.isComposing) {
            clearTimeout(timer);
            controller?.abort();
            revision++;
            page.removeAttribute('aria-busy');
            return;
        }
        schedule(event.target);
    });
    page.addEventListener('compositionend', event => {
        if (event.target.matches(inputSelector)) schedule(event.target);
    });
    page.addEventListener('change', event => {
        if (event.target.matches('.template-filters select[name="department_id"]')) {
            schedule(event.target, true);
        }
    });
    page.addEventListener('submit', event => {
        const input = event.target.querySelector(inputSelector);
        if (!input) return;
        event.preventDefault();
        schedule(input, true);
    });
})();
