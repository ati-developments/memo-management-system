<script>
(() => {
    const key = 'memoflow-theme';
    const system = window.matchMedia('(prefers-color-scheme: dark)');
    let preference = null;
    try { preference = localStorage.getItem(key); } catch (_) {}
    const apply = theme => {
        document.documentElement.dataset.theme = theme;
        document.querySelectorAll('[data-theme-toggle]').forEach(button => {
            button.setAttribute('aria-pressed', String(theme === 'dark'));
            button.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to night mode');
            button.title = button.getAttribute('aria-label');
        });
    };
    apply(preference === 'dark' || preference === 'light' ? preference : system.matches ? 'dark' : 'light');
    document.addEventListener('DOMContentLoaded', () => apply(document.documentElement.dataset.theme));
    document.addEventListener('click', event => {
        if (!event.target.closest('[data-theme-toggle]')) return;
        preference = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        apply(preference);
        try { localStorage.setItem(key, preference); } catch (_) {}
    });
    system.addEventListener('change', event => { if (preference !== 'dark' && preference !== 'light') apply(event.matches ? 'dark' : 'light'); });
    window.addEventListener('storage', event => {
        if (event.key !== key && event.key !== null) return;
        preference = event.newValue;
        apply(preference === 'dark' || preference === 'light' ? preference : system.matches ? 'dark' : 'light');
    });
})();
</script>