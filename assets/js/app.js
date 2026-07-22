/* Header Style */
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    const sections = document.querySelectorAll('[data-header-theme]');

    if (!header || !sections.length) {
        return;
    }

    const updateHeaderTheme = () => {
        const headerHeight = header.offsetHeight;
        let activeSection = null;

        sections.forEach((section) => {
            const rect = section.getBoundingClientRect();

            if (
                rect.top <= headerHeight &&
                rect.bottom > headerHeight
            ) {
                activeSection = section;
            }
        });

        if (!activeSection) {
            return;
        }

        const theme = activeSection.dataset.headerTheme;

        header.classList.remove(
            'site-header--light',
            'site-header--dark'
        );

        header.classList.add(`site-header--${theme}`);
    };

    updateHeaderTheme();

    window.addEventListener('scroll', updateHeaderTheme, {
        passive: true
    });

    window.addEventListener('resize', updateHeaderTheme);
});