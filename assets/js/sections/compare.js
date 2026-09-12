const initCompareSections = () => {
    document.querySelectorAll('[data-compare]').forEach((section) => {
        if (section.dataset.compareReady === 'true') return;
        const modal = section.querySelector('[data-compare-modal]');
        if (!modal || typeof modal.showModal !== 'function') return;
        section.dataset.compareReady = 'true';
        const title = modal.querySelector('[data-compare-modal-title]');
        const features = modal.querySelector('[data-compare-modal-features]');
        const footer = modal.querySelector('[data-compare-modal-footer]');
        const desktop = window.matchMedia('(min-width: 1280px)');
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
        let trigger = null;
        let closeTimer = 0;
        const finishClose = () => {
            window.clearTimeout(closeTimer);
            modal.close();
        };
        const close = () => {
            if (!modal.open || modal.classList.contains('compare-closing')) return;
            if (reduced.matches) {
                finishClose();
                return;
            }
            modal.classList.add('compare-closing');
            closeTimer = window.setTimeout(finishClose, 200);
        };
        section.addEventListener('click', (event) => {
            const button = event.target.closest('[data-compare-open]');
            if (!button || desktop.matches || modal.open) return;
            const plan = button.closest('[data-compare-plan]');
            const template = plan?.querySelector('[data-compare-details]');
            if (!template) return;
            trigger = button;
            title.textContent = 'همه ویژگی‌های ' + plan.querySelector('[data-compare-title]').textContent;
            features.replaceChildren(template.content.cloneNode(true));
            footer.replaceChildren();
            ['[data-compare-price]', '[data-compare-order]'].forEach((selector) => {
                const element = plan.querySelector(selector);
                if (element) footer.append(element.cloneNode(true));
            });
            modal.classList.remove('compare-closing');
            modal.showModal();
            features.scrollTop = 0;
            document.documentElement.classList.add('compare-modal-open');
        });
        modal.querySelector('[data-compare-close]').addEventListener('click', close);
        modal.addEventListener('cancel', (event) => {
            event.preventDefault();
            close();
        });
        modal.addEventListener('click', (event) => {
            if (event.target !== modal) return;
            const rect = modal.getBoundingClientRect();
            if (event.clientX < rect.left || event.clientX > rect.right ||
                event.clientY < rect.top || event.clientY > rect.bottom) close();
        });
        modal.addEventListener('close', () => {
            window.clearTimeout(closeTimer);
            modal.classList.remove('compare-closing');
            document.documentElement.classList.remove('compare-modal-open');
            if (!desktop.matches) trigger?.focus();
        });
        desktop.addEventListener('change', () => {
            if (desktop.matches && modal.open) finishClose();
        });
    });
};

initCompareSections();
