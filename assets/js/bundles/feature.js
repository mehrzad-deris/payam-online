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
            rootMargin: '200px 0px',
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
