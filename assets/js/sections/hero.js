/* Slide domains */
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#domain-prices');

    if (!container) {
        return;
    }

    const items = container.querySelectorAll('.domain-price-item');

    let domainPrices = [];

    try {
        domainPrices = JSON.parse(
            container.dataset.domainPrices || '[]'
        );
    } catch (error) {
        console.error('Domain prices data is not valid JSON.', error);
        return;
    }

    if (!domainPrices.length || !items.length) {
        return;
    }

    const getVisibleItems = () =>
        Array.from(items).filter(
            (item) => window.getComputedStyle(item).display !== 'none'
        );

    let nextIndex = getVisibleItems().length;
    let replacePosition = 0;

    /**
     * Update one domain item.
     *
     * @param {HTMLElement} item
     * @param {{ extension: string, price: string }} data
     */
    function updateItem(item, data) {
        const priceElement = item.querySelector('.domain-price');
        const extensionElement = item.querySelector('.domain-extension');

        if (!priceElement || !extensionElement) {
            return;
        }

        priceElement.textContent = data.price ?? '';
        extensionElement.textContent = data.extension ?? '';
    }

    // نمایش اولیه سه آیتم اول
    items.forEach((item, index) => {
        const domain = domainPrices[index];

        if (domain) {
            updateItem(item, domain);
        }
    });

    // اگر تعداد داده‌ها از آیتم‌های موجود بیشتر نیست، نیازی به تعویض نیست.
    if (domainPrices.length <= getVisibleItems().length) {
        return;
    }

    setInterval(() => {
        const visibleItems = getVisibleItems();

        if (!visibleItems.length) {
            return;
        }

        const item = visibleItems[replacePosition % visibleItems.length];
        const nextDomain = domainPrices[nextIndex];

        if (!item || !nextDomain) {
            return;
        }

        item.classList.add(
            'opacity-0',
            'scale-95',
            'translate-y-2'
        );

        setTimeout(() => {
            updateItem(item, nextDomain);

            nextIndex = (nextIndex + 1) % domainPrices.length;
            replacePosition = (replacePosition + 1) % visibleItems.length;

            item.classList.remove(
                'opacity-0',
                'scale-95',
                'translate-y-2'
            );
        }, 500);
    }, 3000);
});
