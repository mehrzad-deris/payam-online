/* Header Style */
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    const sections = Array.from(
        document.querySelectorAll('[data-header-theme]')
    );

    if (!header) {
        return;
    }

    let ticking = false;
    let currentTheme = '';
    const darkThemeOffset = 32;
    const stickyOffset = 60;
    let previousScroll = Math.max(0, window.scrollY);
    let scrollTravel = 0;
    const updateHeaderVisibility = () => {
        const position = Math.max(0, window.scrollY);
        const delta = position - previousScroll;
        previousScroll = position;
        if (position <= stickyOffset ||
            header.classList.contains('has-open-submenu') ||
            document.documentElement.classList.contains('header-drawer-open') ||
            header.contains(document.activeElement)) {
            scrollTravel = 0;
            header.classList.remove('site-header--scroll-hidden');
            return;
        }
        if (delta === 0) return;
        if (Math.sign(delta) !== Math.sign(scrollTravel)) scrollTravel = 0;
        scrollTravel += delta;
        // Hide below the top buffer; filter jitter when revealing.
        if (scrollTravel > 0 || scrollTravel <= -10) {
            header.classList.toggle('site-header--scroll-hidden', scrollTravel > 0);
            scrollTravel = 0;
        }
    };

    const applyHeaderState = (nextTheme, isSticky) => {
        updateHeaderVisibility();
        header.classList.toggle('site-header--sticky', isSticky);

        if (nextTheme && nextTheme !== currentTheme) {
            header.classList.remove(
                'site-header--light',
                'site-header--dark'
            );

            header.classList.add(`site-header--${nextTheme}`);
            currentTheme = nextTheme;
        }
    };

    const getSectionAt = (position) => {
        for (let index = 0; index < sections.length; index += 1) {
            const section = sections[index];
            const rect = section.getBoundingClientRect();

            if (rect.top <= position && rect.bottom > position) {
                return {
                    index,
                    rect,
                    theme: section.dataset.headerTheme || '',
                };
            }
        }

        return null;
    };

    const updateHeaderTheme = () => {
        const isSticky = window.scrollY > stickyOffset;

        if (!sections.length) {
            applyHeaderState('', isSticky);
            ticking = false;
            return;
        }

        // At the top of the page the first section is authoritative. Avoiding
        // geometry reads here prevents a full synchronous layout during boot.
        if (!isSticky) {
            applyHeaderState(sections[0].dataset.headerTheme || '', false);
            ticking = false;
            return;
        }

        const headerHeight = header.offsetHeight;
        const activeSection = getSectionAt(headerHeight);
        let nextTheme = activeSection?.theme || '';

        if (activeSection?.theme === 'dark') {
            const darkEnterLine = Math.max(
                0,
                headerHeight - darkThemeOffset
            );
            const darkExitLine = headerHeight + darkThemeOffset;

            if (activeSection.rect.top > darkEnterLine) {
                nextTheme =
                    getSectionAt(darkEnterLine)?.theme ||
                    sections[activeSection.index - 1]?.dataset.headerTheme ||
                    nextTheme;
            } else if (activeSection.rect.bottom <= darkExitLine) {
                nextTheme =
                    getSectionAt(darkExitLine)?.theme ||
                    sections[activeSection.index + 1]?.dataset.headerTheme ||
                    nextTheme;
            }
        }

        // Geometry reads above are deliberately completed before class writes.
        applyHeaderState(nextTheme, true);

        ticking = false;
    };

    const requestUpdate = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        requestAnimationFrame(updateHeaderTheme);
    };

    const syncRestoredScrollPosition = () => {
        previousScroll = Math.max(0, window.scrollY);
        scrollTravel = 0;
        header.classList.remove('site-header--scroll-hidden');
        // Scroll restoration may happen between load/pageshow and the next paint.
        updateHeaderTheme();
        requestAnimationFrame(() => {
            updateHeaderTheme();
            requestAnimationFrame(updateHeaderTheme);
        });
        window.setTimeout(updateHeaderTheme, 150);
    };

    syncRestoredScrollPosition();
    header.addEventListener('focusin', () => {
        header.classList.remove('site-header--scroll-hidden');
        scrollTravel = 0;
    });

    window.addEventListener('load', syncRestoredScrollPosition, {
        once: true
    });
    window.addEventListener('pageshow', syncRestoredScrollPosition);

    window.addEventListener('scroll', requestUpdate, {
        passive: true
    });

    window.addEventListener('resize', requestUpdate, {
        passive: true
    });
});

import './header-navigation.js';

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

/* Expandable SEO content */
document.querySelectorAll('[data-seo-box]').forEach((box) => {
    if (box.dataset.seoBoxReady === 'true') {
        return;
    }

    const content = box.querySelector('[data-seo-box-content]');
    const toggle = box.querySelector('[data-seo-box-toggle]');
    const label = toggle?.querySelector('[data-seo-box-label]');
    const icon = toggle?.querySelector('[data-seo-box-icon]');

    if (!content || !toggle || !label) {
        return;
    }

    box.dataset.seoBoxReady = 'true';
    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );
    const collapsedLines = Math.max(
        1,
        Number.parseInt(box.dataset.collapsedLines || '6', 10)
    );
    let isExpanded = false;
    let collapsedHeight = 0;
    let resizeFrame = 0;
    let transitionId = 0;

    const getCollapsedHeight = () => {
        const styles = window.getComputedStyle(content);
        const lineHeight = Number.parseFloat(styles.lineHeight);
        const fallbackLineHeight =
            Number.parseFloat(styles.fontSize || '16') * 1.75;

        return Math.ceil(
            (Number.isFinite(lineHeight) ? lineHeight : fallbackLineHeight) *
                collapsedLines
        );
    };

    const syncCollapsedSize = () => {
        collapsedHeight = getCollapsedHeight();

        if (content.scrollHeight <= collapsedHeight + 1) {
            content.style.removeProperty('height');
            content.style.removeProperty('overflow');
            toggle.hidden = true;
            return;
        }

        toggle.hidden = false;

        if (!isExpanded) {
            content.style.height = `${collapsedHeight}px`;
            content.style.overflow = 'hidden';
        }
    };

    const setState = async (shouldExpand) => {
        const currentTransition = ++transitionId;

        content.getAnimations().forEach((animation) => animation.cancel());
        const startHeight = content.getBoundingClientRect().height;

        if (shouldExpand) {
            content.style.height = 'auto';
        }

        const endHeight = shouldExpand
            ? content.scrollHeight
            : collapsedHeight;

        isExpanded = shouldExpand;
        toggle.setAttribute('aria-expanded', String(shouldExpand));
        label.textContent = shouldExpand
            ? toggle.dataset.expandedLabel
            : toggle.dataset.collapsedLabel;
        box.classList.toggle('is-expanded', shouldExpand);
        icon?.classList.toggle('rotate-180', shouldExpand);
        content.style.overflow = 'hidden';

        if (!reducedMotion.matches) {
            const animation = content.animate(
                [
                    { height: `${startHeight}px` },
                    { height: `${endHeight}px` },
                ],
                {
                    duration: 300,
                    easing: 'ease-out',
                }
            );

            await animation.finished.catch(() => {});
        }

        if (currentTransition !== transitionId) {
            return;
        }

        if (shouldExpand) {
            content.style.removeProperty('height');
            content.style.removeProperty('overflow');
        } else {
            content.style.height = `${collapsedHeight}px`;
        }
    };

    toggle.addEventListener('click', () => setState(!isExpanded));
    window.addEventListener(
        'resize',
        () => {
            if (resizeFrame) {
                return;
            }

            resizeFrame = requestAnimationFrame(() => {
                resizeFrame = 0;
                syncCollapsedSize();
            });
        },
        { passive: true }
    );

    syncCollapsedSize();
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
