const normalizeDomainText = value => value.normalize('NFKC').toLocaleLowerCase().replace(/[يى]/g, 'ی').replace(/ك/g, 'ک').replace(/[\u200c\u200e\u200f]/g, '').trim();

document.querySelectorAll('[data-domain-products]').forEach(section => {
    if (section.dataset.domainReady) return;
    const search = section.querySelector('[data-domain-search]');
    const category = section.querySelector('[data-domain-category]');
    const more = section.querySelector('[data-domain-more]');
    const label = section.querySelector('[data-domain-more-label]');
    const status = section.querySelector('[data-domain-status]');
    const empty = section.querySelector('[data-domain-empty]');
    const filters = section.querySelector('[data-domain-filters]');
    const searchButton = section.querySelector('[data-domain-search-button]');
    const rows = Array.from(section.querySelectorAll('[data-domain-row]')).map(element => ({
        element,
        extension: normalizeDomainText(element.dataset.extension),
        category: element.dataset.category || '',
    }));
    if (!search || !category || !more || !label || !status || !empty || !filters || !rows.length) return;
    section.dataset.domainReady = 'true';
    const batch = Math.min(100, Math.max(1, Number(section.dataset.batch) || 10));
    const number = new Intl.NumberFormat('fa-IR');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    let visible = batch;
    let expanded = false;
    let timer;
    const render = (animate = true) => {
        const term = normalizeDomainText(search.value).replace(/^\.+/, '');
        const matches = rows.filter(row => (!term || row.extension.includes(term)) && (!category.value || row.category === category.value));
        const shown = new Set(matches.slice(0, visible));
        rows.forEach(row => {
            const show = shown.has(row);
            const wasHidden = row.element.hidden;
            row.element.hidden = !show;
            if (show && wasHidden && animate && !reduced.matches) {
                row.element.getAnimations().forEach(animation => animation.cancel());
                row.element.animate([{ opacity: 0, transform: 'translateY(6px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 180, easing: 'ease-out' });
            }
        });
        const remaining = Math.max(0, matches.length - visible);
        more.hidden = expanded || remaining === 0;
        label.textContent = `مشاهده ${number.format(remaining)} دامنه دیگر`;
        empty.hidden = matches.length > 0;
        status.textContent = `${number.format(shown.size)} دامنه از ${number.format(matches.length)} نتیجه نمایش داده می‌شود.`;
        return matches;
    };
    const filter = () => { clearTimeout(timer); render(); };
    search.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(filter, 120); });
    search.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); filter(); } });
    category.addEventListener('change', filter);
    searchButton?.addEventListener('click', filter);
    more.addEventListener('click', () => {
        clearTimeout(timer);
        const previous = visible;
        expanded = true;
        visible = rows.length;
        const matches = render();
        const firstNew = matches[previous]?.element;
        if (firstNew) { firstNew.tabIndex = -1; firstNew.focus({ preventScroll: true }); }
    });
    // No form submission, history API, URL mutation or remote filtering request.
    filters.hidden = false;
    render(false);
});
