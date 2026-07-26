document.querySelectorAll('[data-tabs]').forEach((tabs) => {
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

    const mobileQuery = window.matchMedia('(max-width: 1279px)');

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
            }
        });

        if (moveFocus) {
            activeButton.focus();
        }
    };

    tabList.addEventListener('click', (event) => {
        const button = event.target.closest('[role="tab"]');

        if (!button || !tabList.contains(button)) {
            return;
        }

        activateTab(button);
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
    });

    const syncTabsMode = (event) => {
        const isMobile = event.matches;

        tabList.hidden = isMobile;

        if (isMobile) {
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
    };

    syncTabsMode(mobileQuery);
    mobileQuery.addEventListener('change', syncTabsMode);
});

const hydrateTestimonialSlide = (slide) => {
    slide?.querySelectorAll('[data-testimonial-srcset]').forEach((source) => {
        source.srcset = source.dataset.testimonialSrcset;
        source.removeAttribute('data-testimonial-srcset');
    });

    slide?.querySelectorAll('[data-testimonial-src]').forEach((image) => {
        image.src = image.dataset.testimonialSrc;
        image.removeAttribute('data-testimonial-src');
    });
};

const hydrateTestimonialNeighbors = (swiper) => {
    const slides = swiper.slides;

    [-1, 0, 1].forEach((distance) => {
        const index = (swiper.activeIndex + distance + slides.length) % slides.length;
        hydrateTestimonialSlide(slides[index]);
    });
};

const initTestimonialSliders = () => {
    if (typeof window.Swiper !== 'function') {
        return false;
    }

    document.querySelectorAll('[data-testimonials-slider]').forEach((slider) => {
    if (slider.dataset.testimonialsReady === 'true') {
        return;
    }

    slider.dataset.testimonialsReady = 'true';
    const slides = slider.querySelectorAll('.swiper-slide');

    if (!slides.length) {
        hydrateTestimonialSlide(slides[0]);
        return;
    }

    let swiper = null;

    const initSlider = () => {
        if (swiper) {
            return;
        }

        hydrateTestimonialSlide(slides[0]);
        hydrateTestimonialSlide(slides[1]);

        swiper = new window.Swiper(slider, {
            slidesPerView: 1,
            speed: 650,
            loop: slides.length > 1,
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            pagination: {
                el: slider.querySelector('.testimonials-pagination'),
                clickable: true,
            },
            autoplay:
                slides.length > 1
                    ? {
                          delay: 5500,
                          disableOnInteraction: false,
                          pauseOnMouseEnter: true,
                      }
                    : false,
            on: {
                init() {
                    hydrateTestimonialNeighbors(this);
                },
                slideChangeTransitionStart() {
                    hydrateTestimonialNeighbors(this);
                },
            },
        });

        const visibilityObserver = new IntersectionObserver(
            ([entry]) => {
                if (!swiper?.autoplay) {
                    return;
                }

                entry.isIntersecting
                    ? swiper.autoplay.start()
                    : swiper.autoplay.stop();
            },
            { threshold: 0.15 }
        );

        visibilityObserver.observe(slider);
    };

    const initObserver = new IntersectionObserver(
        ([entry]) => {
            if (!entry.isIntersecting) {
                return;
            }

            initSlider();
            initObserver.disconnect();
        },
        {
            rootMargin: '300px 0px',
            threshold: 0,
        }
    );

    initObserver.observe(slider);
    });

    return true;
};

if (!initTestimonialSliders()) {
    document.addEventListener('DOMContentLoaded', initTestimonialSliders, {
        once: true,
    });
    window.addEventListener('load', initTestimonialSliders, { once: true });
}
