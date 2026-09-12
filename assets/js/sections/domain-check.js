const initDomainRotator = (container) => {
    if (container.dataset.domainRotatorReady === 'true') {
        return;
    }

    const items = Array.from(container.querySelectorAll('.domain-price-item'));
    let domainPrices = [];

    try {
        domainPrices = JSON.parse(container.dataset.domainPrices || '[]');
    } catch (error) {
        console.error('Domain prices data is not valid JSON.', error);
        return;
    }

    if (!domainPrices.length || !items.length) {
        return;
    }

    container.dataset.domainRotatorReady = 'true';

    const getVisibleItems = () =>
        items.filter(
            (item) => window.getComputedStyle(item).display !== 'none'
        );

    const updateItem = (item, data) => {
        const priceElement = item.querySelector('.domain-price');
        const extensionElement = item.querySelector('.domain-extension');

        if (!priceElement || !extensionElement) {
            return;
        }

        priceElement.textContent = data.price ?? '';
        extensionElement.textContent = data.extension ?? '';
    };

    items.forEach((item, index) => {
        if (domainPrices[index]) {
            updateItem(item, domainPrices[index]);
        }
    });

    let nextIndex = getVisibleItems().length;
    let replacePosition = 0;
    let intervalId = 0;

    const stopRotation = () => {
        if (!intervalId) {
            return;
        }

        window.clearInterval(intervalId);
        intervalId = 0;
    };

    const rotate = () => {
        const visibleItems = getVisibleItems();

        if (!visibleItems.length || domainPrices.length <= visibleItems.length) {
            return;
        }

        const item = visibleItems[replacePosition % visibleItems.length];
        const nextDomain = domainPrices[nextIndex % domainPrices.length];

        if (!item || !nextDomain) {
            return;
        }

        item.classList.add('opacity-0', 'scale-95', 'translate-y-2');

        window.setTimeout(() => {
            updateItem(item, nextDomain);
            nextIndex = (nextIndex + 1) % domainPrices.length;
            replacePosition = (replacePosition + 1) % visibleItems.length;
            item.classList.remove('opacity-0', 'scale-95', 'translate-y-2');
        }, 500);
    };

    const startRotation = () => {
        if (
            intervalId ||
            document.hidden ||
            domainPrices.length <= getVisibleItems().length
        ) {
            return;
        }

        intervalId = window.setInterval(rotate, 3000);
    };

    const visibilityObserver = new IntersectionObserver(
        ([entry]) => {
            entry.isIntersecting ? startRotation() : stopRotation();
        },
        { threshold: 0.1 }
    );

    document.addEventListener('visibilitychange', () => {
        document.hidden ? stopRotation() : startRotation();
    });

    visibilityObserver.observe(container);
};

const initDomainCheckSections = () => {
    document
        .querySelectorAll('[data-domain-prices]')
        .forEach(initDomainRotator);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDomainCheckSections, {
        once: true,
    });
} else {
    initDomainCheckSections();
}
