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
