document.querySelectorAll('[data-faq]').forEach((faq) => {
    if (faq.dataset.faqReady === 'true') return;

    const itemsRoot = faq.querySelector('[data-faq-items]');
    if (!itemsRoot) return;

    faq.dataset.faqReady = 'true';
    const endpoint = faq.dataset.faqEndpoint || '';
    const perPage = Number.parseInt(faq.dataset.faqPerPage || '8', 10);
    const loadMore = faq.querySelector('[data-faq-load-more]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let activeCategory = '0';
    let requestController = null;

    const parts = (item) => ({
        button: item.querySelector('[data-faq-toggle]'),
        panel: item.querySelector('[data-faq-panel]'),
        symbol: item.querySelector('[data-faq-symbol]'),
    });

    const setSymbol = (symbol, open) => {
        const svg = symbol?.querySelector('svg');
        const use = svg?.querySelector('use');
        if (!svg || !use) return;
        use.setAttribute('href', `${(use.getAttribute('href') || '').replace(/#.*$/, '')}#${open ? 'minus' : 'plus'}`);
        svg.classList.toggle('fill-white', open);
        svg.classList.toggle('stroke-blue-primary', !open);
    };

    const setState = async (item, open, instant = false) => {
        const { button, panel, symbol } = parts(item);
        if (!button || !panel) return;

        panel.getAnimations().forEach((animation) => animation.cancel());
        button.setAttribute('aria-expanded', String(open));
        item.classList.toggle('is-open', open);
        setSymbol(symbol, open);

        if (instant || reducedMotion.matches || !panel.animate) {
            panel.hidden = !open;
            return;
        }

        if (open) panel.hidden = false;
        const animation = panel.animate(
            [
                { height: `${open ? 0 : panel.scrollHeight}px`, opacity: open ? 0 : 1 },
                { height: `${open ? panel.scrollHeight : 0}px`, opacity: open ? 1 : 0 },
            ],
            { duration: 280, easing: 'ease-out' }
        );
        await animation.finished.catch(() => {});
        if (!open) panel.hidden = true;
    };

    const animateItems = async (callback) => {
        itemsRoot.getAnimations().forEach((animation) => animation.cancel());
        if (!reducedMotion.matches && itemsRoot.animate) {
            await itemsRoot.animate(
                [{ opacity: 1 }, { opacity: 0, transform: 'translateY(6px)' }],
                { duration: 140, easing: 'ease-out' }
            ).finished.catch(() => {});
        }
        callback();
        if (!reducedMotion.matches && itemsRoot.animate) {
            itemsRoot.animate(
                [{ opacity: 0, transform: 'translateY(6px)' }, { opacity: 1, transform: 'translateY(0)' }],
                { duration: 220, easing: 'ease-out' }
            );
        }
    };

    const loadPage = async (page, append = false) => {
        if (!endpoint) return;
        requestController?.abort();
        requestController = new AbortController();
        faq.classList.add('is-loading');
        loadMore?.setAttribute('disabled', '');

        const url = new URL(endpoint);
        url.searchParams.set('per_page', String(perPage));
        url.searchParams.set('page', String(page));
        if (activeCategory !== '0') url.searchParams.set('category', activeCategory);

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                signal: requestController.signal,
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) throw new Error(`FAQ request failed: ${response.status}`);
            const data = await response.json();

            await animateItems(() => {
                if (append) itemsRoot.insertAdjacentHTML('beforeend', data.html || '');
                else itemsRoot.innerHTML = data.html || '';
            });

            if (loadMore) {
                loadMore.hidden = !data.hasMore;
                loadMore.dataset.nextPage = String(data.nextPage || page + 1);
            }
        } catch (error) {
            if (error.name !== 'AbortError') faq.classList.add('has-load-error');
        } finally {
            faq.classList.remove('is-loading');
            loadMore?.removeAttribute('disabled');
        }
    };

    faq.addEventListener('click', (event) => {
        const category = event.target.closest('[data-faq-category]');
        if (category && faq.contains(category)) {
            const value = category.dataset.faqCategory === 'all' ? '0' : category.dataset.faqCategory;
            if (value === activeCategory || !endpoint) return;
            activeCategory = value;
            faq.querySelectorAll('[data-faq-category]').forEach((control) => {
                control.setAttribute('aria-pressed', String(control === category));
            });
            loadPage(1);
            return;
        }

        const more = event.target.closest('[data-faq-load-more]');
        if (more && faq.contains(more)) {
            loadPage(Number.parseInt(more.dataset.nextPage || '2', 10), true);
            return;
        }

        const button = event.target.closest('[data-faq-toggle]');
        if (!button || !itemsRoot.contains(button)) return;

        const active = button.closest('[data-faq-item]');
        const shouldOpen = button.getAttribute('aria-expanded') !== 'true';
        itemsRoot.querySelectorAll('[data-faq-item]').forEach((item) => {
            const isOpen = parts(item).button?.getAttribute('aria-expanded') === 'true';
            if (item === active || isOpen) setState(item, item === active && shouldOpen);
        });
    });
});

