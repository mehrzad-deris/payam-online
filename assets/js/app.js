/* Header Style */
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    const sections = document.querySelectorAll('[data-header-theme]');

    if (!header || !sections.length) {
        return;
    }

    let ticking = false;
    let currentTheme = '';

    const updateHeaderTheme = () => {
        const headerHeight = header.offsetHeight;
        let nextTheme = '';

        for (const section of sections) {
            const rect = section.getBoundingClientRect();

            if (
                rect.top <= headerHeight &&
                rect.bottom > headerHeight
            ) {
                nextTheme = section.dataset.headerTheme || '';
                break;
            }
        }

        if (nextTheme && nextTheme !== currentTheme) {
            header.classList.remove(
                'site-header--light',
                'site-header--dark'
            );

            header.classList.add(`site-header--${nextTheme}`);
            currentTheme = nextTheme;
        }

        ticking = false;
    };

    const requestUpdate = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        requestAnimationFrame(updateHeaderTheme);
    };

    requestUpdate();

    window.addEventListener('scroll', requestUpdate, {
        passive: true
    });

    window.addEventListener('resize', requestUpdate, {
        passive: true
    });
});

/* Shared deferred image hydration */
const lazyDesktopQuery = window.matchMedia('(min-width: 1280px)');

const hydrateLazyImages = (root = document) => {
    root.querySelectorAll('[data-lazy-srcset]').forEach((source) => {
        source.srcset = source.dataset.lazySrcset;
        source.removeAttribute('data-lazy-srcset');
    });

    root.querySelectorAll('[data-lazy-src]').forEach((image) => {
        image.src = image.dataset.lazySrc;
        image.removeAttribute('data-lazy-src');
    });

    if (!lazyDesktopQuery.matches) {
        return;
    }

    root.querySelectorAll('[data-lazy-desktop-srcset]').forEach((source) => {
        source.srcset = source.dataset.lazyDesktopSrcset;
        source.removeAttribute('data-lazy-desktop-srcset');
    });

    root.querySelectorAll('[data-lazy-desktop-src]').forEach((image) => {
        image.src = image.dataset.lazyDesktopSrc;
        image.removeAttribute('data-lazy-desktop-src');
    });
};

window.payamLazyImages = {
    hydrate: hydrateLazyImages,
};

const lazyRootObserver = new IntersectionObserver(
    (entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            hydrateLazyImages(entry.target);
            observer.unobserve(entry.target);
        });
    },
    {
        rootMargin: '300px 0px',
        threshold: 0,
    }
);

document.querySelectorAll('[data-lazy-root]').forEach((root) => {
    lazyRootObserver.observe(root);
});

/* Mobile footer accordions */
document.querySelectorAll('[data-footer-accordion]').forEach((accordion) => {
    const desktopQuery = window.matchMedia('(min-width: 1280px)');
    const items = Array.from(accordion.querySelectorAll('[data-footer-item]'));

    const setItemState = (item, isOpen) => {
        const button = item.querySelector('[data-footer-toggle]');
        const panel = item.querySelector('[data-footer-panel]');
        const arrow = item.querySelector('[data-footer-arrow]');

        if (!button || !panel) {
            return;
        }

        button.setAttribute('aria-expanded', String(isOpen));
        panel.hidden = !isOpen;
        arrow?.classList.toggle('rotate-180', isOpen);
    };

    const syncFooterMode = () => {
        items.forEach((item) => setItemState(item, desktopQuery.matches));
    };

    accordion.addEventListener('click', (event) => {
        if (desktopQuery.matches) {
            return;
        }

        const button = event.target.closest('[data-footer-toggle]');

        if (!button || !accordion.contains(button)) {
            return;
        }

        const activeItem = button.closest('[data-footer-item]');
        const shouldOpen = button.getAttribute('aria-expanded') !== 'true';

        items.forEach((item) => {
            setItemState(item, item === activeItem && shouldOpen);
        });
    });

    syncFooterMode();
    desktopQuery.addEventListener('change', syncFooterMode);
});
