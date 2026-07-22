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

    const mobileQuery = window.matchMedia('(max-width: 767px)');

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
