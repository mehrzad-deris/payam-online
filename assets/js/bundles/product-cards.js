const initServerProductFilters = () => {
    document.querySelectorAll('[data-server-product-browser]').forEach((browser) => {
        if (browser.dataset.serverFiltersReady === 'true') return;

        const form = browser.querySelector('[data-server-filters]');
        const selects = Array.from(
            form?.querySelectorAll('[data-server-filter-select]') || []
        );

        if (!form || !selects.length) return;

        browser.dataset.serverFiltersReady = 'true';
        const cards = Array.from(
            browser.querySelectorAll('[data-server-filter-values]')
        );

        const getCardValues = (card) => {
            try {
                const values = JSON.parse(card.dataset.serverFilterValues || '{}');
                return values && typeof values === 'object' ? values : {};
            } catch {
                return {};
            }
        };

        const cardValues = new Map(
            cards.map((card) => [card, getCardValues(card)])
        );

        const syncControlIcon = (select) => {
            const control = select.closest('[data-server-filter-control]');
            const image = control?.querySelector('[data-server-filter-icon]');
            const iconSrc = select.selectedOptions[0]?.dataset.iconSrc || '';

            if (!control || !image) return;

            control.classList.toggle('has-icon', Boolean(iconSrc));
            image.hidden = !iconSrc;

            if (iconSrc) {
                if (image.src !== iconSrc) image.src = iconSrc;
            } else {
                image.removeAttribute('src');
            }
        };

        const applyFilters = () => {
            const activeFilters = selects
                .filter((select) => select.value)
                .map((select) => [select.name, select.value]);

            cards.forEach((card) => {
                const values = cardValues.get(card);
                card.hidden = !activeFilters.every(([key, value]) =>
                    Array.isArray(values[key]) && values[key].includes(value)
                );
            });

            browser.querySelectorAll('.server-tab-panel').forEach((panel) => {
                const panelCards = Array.from(
                    panel.querySelectorAll('[data-server-filter-values]')
                );
                const emptyMessage = panel.querySelector('[data-server-filter-empty]');

                if (emptyMessage) {
                    emptyMessage.hidden = panelCards.some((card) => !card.hidden);
                }
            });
        };

        form.addEventListener('submit', (event) => event.preventDefault());
        form.addEventListener('change', (event) => {
            const select = event.target.closest('[data-server-filter-select]');

            if (!select || !form.contains(select)) return;

            syncControlIcon(select);
            applyFilters();
        });

        selects.forEach(syncControlIcon);
        applyFilters();
    });
};

initServerProductFilters();

