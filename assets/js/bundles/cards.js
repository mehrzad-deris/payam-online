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
    const stackOnMobile = tabs.dataset.tabsMobile !== 'tabs';

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
        const isMobile = event.matches && stackOnMobile;

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

const swiperPresets = {
    'os-logos': () => ({
        slidesPerView: 'auto',
        spaceBetween: 16,
        autoplay: false,
        loop: true,
        watchOverflow: true,
        centerInsufficientSlides: true,
        centeredSlides: true,
        breakpoints: {
            768: {
                slidesPerView: 'auto',
                spaceBetween: 20,
            },
            1280: {
                slidesPerView: 'auto',
                spaceBetween: 24,
            },
        },
    }),
    blog: ({ slideCount }) => ({
        slidesPerView: 1,
        spaceBetween: 21,
        autoplay: false,
        breakpoints: {
            768: {
                slidesPerView: 2,
                spaceBetween: 21,
            },
            1280: {
                slidesPerView: 3,
                spaceBetween: 21,
            },
        },
    }),
    testimonials: ({ slideCount }) => ({
        slidesPerView: 1,
        speed: 650,
        loop: slideCount > 1,
        effect: 'fade',
        fadeEffect: {
            crossFade: true,
        },
        autoplay:
            slideCount > 1
                ? {
                      delay: 5500,
                      disableOnInteraction: false,
                      pauseOnMouseEnter: true,
                  }
                : false,
    }),
};

const parseSwiperOptions = (slider) => {
    if (!slider.dataset.swiperOptions) {
        return {};
    }

    try {
        return JSON.parse(slider.dataset.swiperOptions);
    } catch (error) {
        console.warn('Invalid Swiper options:', slider, error);
        return {};
    }
};

const hydrateSwiperSlide = (slide) => {
    if (slide) {
        window.payamLazyImages?.hydrate(slide);
    }
};

const getVisibleSlideCount = (swiper) => {
    if (swiper.params.slidesPerView === 'auto') {
        return Math.max(1, swiper.slidesPerViewDynamic());
    }

    return Math.max(1, Math.ceil(Number(swiper.params.slidesPerView) || 1));
};

const hydrateSwiperNeighbors = (swiper) => {
    const { slides } = swiper;

    if (!slides.length) {
        return;
    }

    const visibleCount = getVisibleSlideCount(swiper);

    for (let distance = -1; distance <= visibleCount; distance += 1) {
        const index =
            (swiper.activeIndex + distance + slides.length) % slides.length;
        hydrateSwiperSlide(slides[index]);
    }
};

const resolveSwiperControls = (slider, options) => {
    const resolved = { ...options };

    if (slider.querySelector('[data-swiper-pagination]')) {
        resolved.pagination = {
            clickable: true,
            ...(options.pagination || {}),
            el: slider.querySelector('[data-swiper-pagination]'),
        };
    }

    const nextEl = slider.querySelector('[data-swiper-next]');
    const prevEl = slider.querySelector('[data-swiper-prev]');

    if (nextEl || prevEl) {
        resolved.navigation = {
            ...(options.navigation || {}),
            nextEl,
            prevEl,
        };
    }

    return resolved;
};

const initSwipers = () => {
    if (typeof window.Swiper !== 'function') {
        return false;
    }

    document.querySelectorAll('[data-swiper]').forEach((slider) => {
        if (slider.dataset.swiperReady === 'true') {
            return;
        }

        const slides = slider.querySelectorAll('.swiper-wrapper > .swiper-slide');

        if (!slides.length) {
            return;
        }

        slider.dataset.swiperReady = 'true';
        let swiper = null;

        const initSlider = () => {
            if (swiper) {
                return;
            }

            const presetName = slider.dataset.swiper;
            const preset = swiperPresets[presetName]?.({
                slider,
                slideCount: slides.length,
            }) || {};
            const customOptions = parseSwiperOptions(slider);
            const userEvents = {
                ...(preset.on || {}),
                ...(customOptions.on || {}),
            };
            const options = resolveSwiperControls(slider, {
                ...preset,
                ...customOptions,
                loop:
                    slides.length > 1 &&
                    (customOptions.loop ?? preset.loop ?? false),
                autoplay:
                    slides.length > 1
                        ? (customOptions.autoplay ?? preset.autoplay ?? false)
                        : false,
                on: {
                    ...userEvents,
                    init() {
                        hydrateSwiperNeighbors(this);
                        userEvents.init?.call(this);
                    },
                    slideChangeTransitionStart() {
                        hydrateSwiperNeighbors(this);
                        userEvents.slideChangeTransitionStart?.call(this);
                    },
                },
            });

            hydrateSwiperSlide(slides[0]);
            swiper = new window.Swiper(slider, options);

            if (options.autoplay && swiper.autoplay) {
                const visibilityObserver = new IntersectionObserver(
                    ([entry]) => {
                        entry.isIntersecting
                            ? swiper.autoplay.start()
                            : swiper.autoplay.stop();
                    },
                    { threshold: 0.15 }
                );

                visibilityObserver.observe(slider);
            }
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

if (!initSwipers()) {
    document.addEventListener('DOMContentLoaded', initSwipers, {
        once: true,
    });
    window.addEventListener('load', initSwipers, { once: true });
}
