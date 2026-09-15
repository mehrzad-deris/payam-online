document.querySelectorAll('[data-tabs]').forEach((tabs) => {
    if (tabs.dataset.tabsReady === 'true') {
        return;
    }

    const tabList = tabs.querySelector('[role="tablist"]');
    const tabButtons = Array.from(
        tabs.querySelectorAll('[role="tab"]')
    );
    const tabPanels = Array.from(
        tabs.querySelectorAll('[role="tabpanel"]')
    );

    if (!tabList || !tabButtons.length || !tabPanels.length) {
        return;
    }

    tabs.dataset.tabsReady = 'true';
    const mobileQuery = window.matchMedia('(max-width: 1279px)');
    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );
    const stackOnMobile = tabs.dataset.tabsMobile !== 'tabs';
    const autoplayDelay = Number.parseInt(
        tabs.dataset.tabsAutoplay || '',
        10
    );
    const hasAutoplay =
        Number.isFinite(autoplayDelay) &&
        autoplayDelay >= 1000 &&
        tabButtons.length > 1;
    let autoplayTimer = null;
    let autoplayStartedAt = 0;
    let autoplayRemaining = autoplayDelay;
    let isInViewport = !hasAutoplay;

    const clearAutoplayTimer = () => {
        window.clearTimeout(autoplayTimer);
        autoplayTimer = null;
    };

    const stopAutoplay = () => {
        clearAutoplayTimer();
        autoplayRemaining = autoplayDelay;
        tabs.classList.remove(
            'is-autoplay-running',
            'is-autoplay-paused'
        );
    };

    const activateTab = (activeButton, moveFocus = false) => {
        tabButtons.forEach((button) => {
            const isActive = button === activeButton;
            const panel = tabs.querySelector(
                `#${CSS.escape(button.getAttribute('aria-controls'))}`
            );

            button.setAttribute('aria-selected', String(isActive));
            button.tabIndex = isActive ? 0 : -1;

            if (panel) {
                panel.hidden = !isActive;

                if (isActive) {
                    window.payamLazyImages?.hydrate(panel);
                }
            }
        });

        if (moveFocus) {
            activeButton.focus();
        }
    };

    const scheduleAutoplay = () => {
        stopAutoplay();

        if (
            !hasAutoplay ||
            reducedMotion.matches ||
            mobileQuery.matches ||
            !isInViewport ||
            document.hidden ||
            tabs.matches(':hover') ||
            tabs.contains(document.activeElement)
        ) {
            return;
        }

        tabs.style.setProperty(
            '--services-tabs-autoplay-duration',
            `${autoplayDelay}ms`
        );
        // Force the active tab's progress animation to restart with the timer.
        void tabs.offsetWidth;
        tabs.classList.add('is-autoplay-running');

        autoplayTimer = window.setTimeout(() => {
            const activeIndex = tabButtons.findIndex(
                (button) => button.getAttribute('aria-selected') === 'true'
            );
            const nextIndex = (Math.max(activeIndex, 0) + 1) % tabButtons.length;

            activateTab(tabButtons[nextIndex]);
            scheduleAutoplay();
        }, autoplayDelay);
        autoplayStartedAt = performance.now();
        autoplayRemaining = autoplayDelay;
    };

    const pauseAutoplay = () => {
        if (!autoplayTimer) {
            return;
        }

        autoplayRemaining = Math.max(
            0,
            autoplayRemaining - (performance.now() - autoplayStartedAt)
        );
        clearAutoplayTimer();
        tabs.classList.add('is-autoplay-paused');
    };

    const resumeAutoplay = () => {
        if (
            !hasAutoplay ||
            !tabs.classList.contains('is-autoplay-running') ||
            reducedMotion.matches ||
            mobileQuery.matches ||
            !isInViewport ||
            document.hidden ||
            tabs.matches(':hover') ||
            tabs.contains(document.activeElement)
        ) {
            return;
        }

        tabs.classList.remove('is-autoplay-paused');
        autoplayStartedAt = performance.now();
        autoplayTimer = window.setTimeout(() => {
            const activeIndex = tabButtons.findIndex(
                (button) => button.getAttribute('aria-selected') === 'true'
            );
            const nextIndex = (Math.max(activeIndex, 0) + 1) % tabButtons.length;

            activateTab(tabButtons[nextIndex]);
            scheduleAutoplay();
        }, autoplayRemaining);
    };

    tabList.addEventListener('click', (event) => {
        const button = event.target.closest('[role="tab"]');

        if (!button || !tabList.contains(button)) {
            return;
        }

        activateTab(button);
        scheduleAutoplay();
    });

    tabList.addEventListener('keydown', (event) => {
        const currentIndex = tabButtons.indexOf(document.activeElement);

        if (currentIndex === -1) {
            return;
        }

        let nextIndex = currentIndex;

        switch (event.key) {
            case 'ArrowDown':
            case 'ArrowLeft':
                nextIndex = (currentIndex + 1) % tabButtons.length;
                break;
            case 'ArrowUp':
            case 'ArrowRight':
                nextIndex =
                    (currentIndex - 1 + tabButtons.length) %
                    tabButtons.length;
                break;
            case 'Home':
                nextIndex = 0;
                break;
            case 'End':
                nextIndex = tabButtons.length - 1;
                break;
            default:
                return;
        }

        event.preventDefault();
        activateTab(tabButtons[nextIndex], true);
        scheduleAutoplay();
    });

    const syncTabsMode = (event) => {
        const isMobile = event.matches && stackOnMobile;

        tabList.hidden = isMobile;

        if (isMobile) {
            stopAutoplay();
            tabPanels.forEach((panel, index) => {
                panel.hidden = false;
                panel.setAttribute('role', 'region');
                panel.setAttribute(
                    'aria-label',
                    tabButtons[index]?.textContent.trim() || ''
                );
                panel.removeAttribute('aria-labelledby');
                panel.removeAttribute('tabindex');
            });

            return;
        }

        tabPanels.forEach((panel, index) => {
            panel.setAttribute('role', 'tabpanel');
            panel.setAttribute('aria-labelledby', tabButtons[index].id);
            panel.removeAttribute('aria-label');
            panel.tabIndex = 0;
        });

        const activeButton =
            tabButtons.find(
                (button) =>
                    button.getAttribute('aria-selected') === 'true'
            ) || tabButtons[0];

        activateTab(activeButton);
        scheduleAutoplay();
    };

    syncTabsMode(mobileQuery);
    mobileQuery.addEventListener('change', syncTabsMode);

    if (hasAutoplay) {
        tabs.addEventListener('mouseenter', pauseAutoplay);
        tabs.addEventListener('mouseleave', resumeAutoplay);
        tabs.addEventListener('focusin', pauseAutoplay);
        tabs.addEventListener('focusout', () => {
            window.setTimeout(resumeAutoplay, 0);
        });
        document.addEventListener('visibilitychange', scheduleAutoplay);
        reducedMotion.addEventListener('change', scheduleAutoplay);

        const visibilityObserver = new IntersectionObserver(
            ([entry]) => {
                isInViewport = entry.isIntersecting;

                if (isInViewport) {
                    scheduleAutoplay();
                } else {
                    stopAutoplay();
                }
            },
            { threshold: 0.25 }
        );

        visibilityObserver.observe(tabs);
    }
});

