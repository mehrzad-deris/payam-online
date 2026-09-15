(() => {
    const normalizeRoot = (root) => {
        if (root?.jquery) {
            return root[0] || document;
        }

        return root instanceof Element || root instanceof Document
            ? root
            : document;
    };

    const hydrateVisiblePreviews = (root = document) => {
        const scope = normalizeRoot(root);
        const images = scope.matches?.('[data-section-preview-src]')
            ? [scope]
            : scope.querySelectorAll('[data-section-preview-src]');

        images.forEach((image) => {
            const field = image.closest('.acf-field');

            if (!image.dataset.sectionPreviewSrc || field?.offsetParent === null) {
                return;
            }

            image.src = image.dataset.sectionPreviewSrc;
            image.removeAttribute('data-section-preview-src');
        });
    };

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.acf-tab-button')) {
            return;
        }

        requestAnimationFrame(() => hydrateVisiblePreviews(document));
    });

    if (window.acf?.addAction) {
        window.acf.addAction('ready', hydrateVisiblePreviews);
        window.acf.addAction('append', hydrateVisiblePreviews);
        window.acf.addAction('show_field', hydrateVisiblePreviews);
    } else {
        document.addEventListener('DOMContentLoaded', () => hydrateVisiblePreviews());
    }
})();
