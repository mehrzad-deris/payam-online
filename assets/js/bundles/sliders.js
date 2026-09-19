const swiperPresets = {
    'os-logos': () => ({
        slidesPerView: 'auto',
        spaceBetween: 16,
        autoplay: false,
        loop: true,
        watchOverflow: true,
        centerInsufficientSlides: true,
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

// Move pagination visually without changing the tallest-slide layout reservation.
const positionTestimonialPagination = (swiper) => {
    const slider = swiper.el;
    if (!slider.querySelector('[data-swiper-pagination]')) return;
    let frame = 0;
    const schedule = () => {
        if (frame) return;
        frame = requestAnimationFrame(() => {
            frame = 0;
            if (swiper.destroyed) return;
            const active = swiper.slides[swiper.activeIndex];
            if (!active) return;
            const offset = Math.min(0, active.offsetHeight - swiper.wrapperEl.offsetHeight);
            slider.style.setProperty('--testimonial-pagination-offset', `${offset}px`);
        });
    };
    const observer = new ResizeObserver(schedule);
    observer.observe(swiper.wrapperEl);
    swiper.slides.forEach(slide => observer.observe(slide));
    swiper.on('slideChange', schedule);
    swiper.on('destroy', () => {
        observer.disconnect();
        cancelAnimationFrame(frame);
        slider.style.removeProperty('--testimonial-pagination-offset');
    });
    schedule();
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
            if (presetName === 'testimonials') positionTestimonialPagination(swiper);

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
