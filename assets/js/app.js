/* Services Swiper */
var ServicesCarousel = new Swiper('.servicesSwiper', {
    slidesPerView: 3,
    spaceBetween: 20,
    loop: true,
    navigation: {
        nextEl: '.servicesSwiper .button-next', prevEl: '.servicesSwiper .button-prev',
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
    },
});

/* Tab Module */
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.tab-btn');
    const tabs = document.querySelectorAll('.tab-content');

    if (!buttons.length || !tabs.length) {
        return;
    }

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.tab;

            buttons.forEach((item) => {
                item.classList.toggle('active', item === button);
            });

            tabs.forEach((tab) => {
                const isActive = tab.id === targetId;

                tab.classList.toggle('active', isActive);
                tab.classList.toggle('visible', isActive);
                tab.classList.toggle('invisible', !isActive);
                tab.classList.toggle('opacity-100', isActive);
                tab.classList.toggle('opacity-0', !isActive);
                tab.classList.toggle('translate-y-0', isActive);
                tab.classList.toggle('translate-y-2', !isActive);
                tab.classList.toggle('pointer-events-none', !isActive);
            });
        });
    });

});

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