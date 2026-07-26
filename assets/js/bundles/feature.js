const featureDesktopQuery = window.matchMedia('(min-width: 1280px)');

const formatFeatureCounter = (value, decimals) =>
    new Intl.NumberFormat('fa-IR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(value);

const hydrateFeatureImages = (section) => {
    section.querySelectorAll('[data-feature-srcset]').forEach((source) => {
        source.srcset = source.dataset.featureSrcset;
        source.removeAttribute('data-feature-srcset');
    });

    section.querySelectorAll('[data-feature-src]').forEach((image) => {
        image.src = image.dataset.featureSrc;
        image.removeAttribute('data-feature-src');
    });

    if (!featureDesktopQuery.matches) {
        return;
    }

    section
        .querySelectorAll('[data-feature-desktop-srcset]')
        .forEach((image) => {
            image.srcset = image.dataset.featureDesktopSrcset;
            image.removeAttribute('data-feature-desktop-srcset');
        });

    section
        .querySelectorAll('[data-feature-desktop-src]')
        .forEach((image) => {
            image.src = image.dataset.featureDesktopSrc;
            image.removeAttribute('data-feature-desktop-src');
        });
};

const animateFeatureCounter = (counter) => {
    const target = Number.parseFloat(counter.dataset.counterTarget || '0');
    const decimals = Number.parseInt(
        counter.dataset.counterDecimals || '0',
        10
    );
    const duration = 1400;
    const startTime = performance.now();

    if (!Number.isFinite(target)) {
        return;
    }

    const update = (time) => {
        const progress = Math.min((time - startTime) / duration, 1);
        const easedProgress = 1 - Math.pow(1 - progress, 3);
        const currentValue = target * easedProgress;

        counter.textContent = formatFeatureCounter(
            progress === 1 ? target : currentValue,
            decimals
        );

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    };

    requestAnimationFrame(update);
};

const initBrands = (section) => {
    const stage = section.querySelector('[data-brand-stage]');
    const track = section.querySelector('[data-brand-track]');
    const firstGroup = track?.querySelector('.brands-group');
    const items = section.querySelectorAll('[data-brand-item]');

    if (!stage || !track || !firstGroup || !items.length) {
        return;
    }

    let frameId = 0;
    let lastTime = 0;
    let offset = -firstGroup.offsetWidth;
    const speed = 52;

    track.style.transform = `translate3d(${offset}px, 0, 0)`;

    const updateMarquee = (time) => {
        const groupWidth = firstGroup.offsetWidth;
        const elapsed = lastTime ? Math.min(time - lastTime, 64) : 0;

        lastTime = time;
        offset += (speed * elapsed) / 1000;

        if (offset >= 0 && groupWidth > 0) {
            offset -= groupWidth;
        }

        track.style.transform = `translate3d(${offset}px, 0, 0)`;

        const center = stage.getBoundingClientRect().left + stage.offsetWidth / 2;

        items.forEach((item) => {
            const bounds = item.getBoundingClientRect();
            const hasPassedCenter = bounds.left + bounds.width / 2 >= center;

            item.classList.toggle('brand-color', hasPassedCenter);
        });

        frameId = requestAnimationFrame(updateMarquee);
    };

    const start = () => {
        if (frameId) {
            return;
        }

        lastTime = 0;
        section.classList.add('brands-running');
        frameId = requestAnimationFrame(updateMarquee);
    };

    const stop = () => {
        section.classList.remove('brands-running');

        if (frameId) {
            cancelAnimationFrame(frameId);
            frameId = 0;
        }

        lastTime = 0;
    };

    const observer = new IntersectionObserver(
        ([entry]) => {
            const shouldRun = entry.isIntersecting && featureDesktopQuery.matches;

            shouldRun ? start() : stop();
        },
        {
            rootMargin: '120px 0px',
            threshold: 0,
        }
    );

    observer.observe(section);

    featureDesktopQuery.addEventListener('change', () => {
        if (!featureDesktopQuery.matches) {
            stop();
            return;
        }

        const bounds = section.getBoundingClientRect();

        if (bounds.bottom >= -120 && bounds.top <= window.innerHeight + 120) {
            start();
        }
    });
};

document.querySelectorAll('[data-brands-section]').forEach(initBrands);

document.querySelectorAll('[data-feature-module]').forEach((section) => {
    let isActivated = false;
    let canHydrateImages = false;
    const pinStage = section.querySelector('.infrastructure-section__map');

    const activate = () => {
        if (isActivated) {
            hydrateFeatureImages(section);
            return;
        }

        isActivated = true;
        hydrateFeatureImages(section);

        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            section
                .querySelectorAll('[data-counter-target]')
                .forEach(animateFeatureCounter);
        } else {
            section.querySelectorAll('[data-counter-target]').forEach(
                (counter) => {
                    const target = Number.parseFloat(
                        counter.dataset.counterTarget || '0'
                    );
                    const decimals = Number.parseInt(
                        counter.dataset.counterDecimals || '0',
                        10
                    );

                    counter.textContent = formatFeatureCounter(
                        target,
                        decimals
                    );
                }
            );
        }
    };

    const imageObserver = new IntersectionObserver(
        ([entry]) => {
            if (!entry.isIntersecting) {
                return;
            }

            canHydrateImages = true;
            hydrateFeatureImages(section);
            imageObserver.disconnect();
        },
        {
            rootMargin: section.matches('[data-brands-section]')
                ? '600px 0px'
                : '200px 0px',
            threshold: 0,
        }
    );

    const counterObserver = new IntersectionObserver(
        ([entry]) => {
            if (!entry.isIntersecting) {
                return;
            }

            activate();
            counterObserver.disconnect();
        },
        {
            rootMargin: '0px',
            threshold: 0.3,
        }
    );

    const pinObserver = pinStage
        ? new IntersectionObserver(
              ([entry]) => {
                  if (!entry.isIntersecting) {
                      return;
                  }

                  section.classList.add('is-pins-visible');
                  pinObserver.disconnect();
              },
              {
                  rootMargin: '0px',
                  threshold: 0.6,
              }
          )
        : null;

    imageObserver.observe(section);
    counterObserver.observe(section);
    pinObserver?.observe(pinStage);

    featureDesktopQuery.addEventListener('change', () => {
        if (canHydrateImages || isActivated) {
            hydrateFeatureImages(section);
        }
    });
});
