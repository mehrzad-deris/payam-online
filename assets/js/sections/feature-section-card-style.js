const featureCardMobileQuery = window.matchMedia('(max-width: 767px)');
const featureCardReducedMotion = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
);

document.querySelectorAll('[data-feature-card]').forEach((section) => {
    if (section.dataset.featureCardReady === 'true') {
        return;
    }

    const toggle = section.querySelector('[data-feature-card-toggle]');
    const label = toggle?.querySelector('[data-feature-card-label]');
    const extraItems = Array.from(
        section.querySelectorAll('[data-feature-card-extra]')
    );

    if (!toggle || !label || !extraItems.length) {
        return;
    }

    section.dataset.featureCardReady = 'true';
    let isAnimating = false;

    const setLabel = (isExpanded) => {
        toggle.setAttribute('aria-expanded', String(isExpanded));
        label.textContent = isExpanded
            ? toggle.dataset.expandedLabel
            : toggle.dataset.collapsedLabel;
        section.classList.toggle('feature-card--expanded', isExpanded);
    };

    const setItemsHidden = (isHidden) => {
        extraItems.forEach((item) => {
            item.hidden = isHidden;
        });
    };

    const syncMode = () => {
        isAnimating = false;

        if (featureCardMobileQuery.matches) {
            setLabel(false);
            setItemsHidden(true);
            return;
        }

        setItemsHidden(false);
        setLabel(true);
    };

    toggle.addEventListener('click', async () => {
        if (!featureCardMobileQuery.matches || isAnimating) {
            return;
        }

        const shouldExpand = toggle.getAttribute('aria-expanded') !== 'true';
        const shouldAnimate = !featureCardReducedMotion.matches;

        isAnimating = true;

        if (shouldExpand) {
            setItemsHidden(false);
        }

        setLabel(shouldExpand);

        if (shouldAnimate) {
            const animations = extraItems.map((item, index) =>
                item.animate(
                    shouldExpand
                        ? [
                              { opacity: 0, transform: 'translateY(-10px)' },
                              { opacity: 1, transform: 'translateY(0)' },
                          ]
                        : [
                              { opacity: 1, transform: 'translateY(0)' },
                              { opacity: 0, transform: 'translateY(-10px)' },
                          ],
                    {
                        duration: 240,
                        delay: index * 40,
                        easing: 'ease-out',
                        fill: 'both',
                    }
                ).finished
            );

            await Promise.allSettled(animations);
        }

        if (!shouldExpand) {
            setItemsHidden(true);
        }

        extraItems.forEach((item) =>
            item.getAnimations().forEach((animation) => animation.cancel())
        );
        isAnimating = false;
    });

    syncMode();
    featureCardMobileQuery.addEventListener('change', syncMode);
});
