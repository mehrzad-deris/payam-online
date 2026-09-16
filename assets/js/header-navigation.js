document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    const drawer = document.querySelector('[data-mobile-menu]');
    const trigger = document.querySelector('[data-mobile-menu-toggle]');
    const backdrop = document.querySelector('[data-header-backdrop]');
    if (!header || !drawer || !trigger || !backdrop) return;
    const desktop = matchMedia('(min-width: 1024px)');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    let closing = false;
    const leaveTimers = new WeakMap();
    const cancelLeave = item => {
        clearTimeout(leaveTimers.get(item));
        leaveTimers.delete(item);
    };
    const syncBackdrop = () => {
        const open = !!header.querySelector('.menu-item.is-submenu-open');
        backdrop.hidden = !open;
        header.classList.toggle('has-open-submenu', open);
    };
    const setExpanded = (button, open) => {
        cancelLeave(button.parentElement);
        button.setAttribute('aria-expanded', String(open));
        button.parentElement.classList.toggle('is-submenu-open', open);
        syncBackdrop();
    };
    const collapseAll = (root) => root.querySelectorAll('[data-submenu-toggle]').forEach(button => setExpanded(button, false));
    const mobileBody = drawer.querySelector('.header-drawer-body');
    const mobileMenu = mobileBody.querySelector('.header-menu-mobile');
    const stepBar = drawer.querySelector('[data-mobile-menu-step]');
    const backButton = drawer.querySelector('[data-mobile-menu-back]');
    const stepTitle = drawer.querySelector('[data-mobile-menu-title]');
    const panels = new Map();
    const steps = [];
    let currentPanel = mobileMenu;
    let stepAnimation;
    if (mobileMenu) {
        mobileMenu.querySelectorAll('.sub-menu').forEach(panel => {
            const owner = panel.parentElement;
            panels.set(owner, panel);
            const link = owner.querySelector(':scope > a');
            link?.setAttribute('aria-controls', panel.id);
            link?.setAttribute('aria-expanded', 'false');
            panel.classList.add('header-menu', 'header-menu-mobile');
            panel.hidden = true;
            // Move, rather than clone, the existing WordPress submenu into a sibling panel.
            mobileMenu.parentElement.append(panel);
        });
    }
    const showPanel = (panel, backwards = false, focusTarget = null) => {
        if (!panel) return;
        stepAnimation?.cancel();
        currentPanel.hidden = true;
        currentPanel = panel;
        panel.hidden = false;
        mobileBody.scrollTop = 0;
        stepBar.hidden = steps.length === 0;
        stepTitle.textContent = steps.at(-1)?.title || '';
        if (!reduced.matches) stepAnimation = panel.animate([
            { opacity: 0, transform: `translateX(${backwards ? '24px' : '-24px'})` },
            { opacity: 1, transform: 'translateX(0)' }
        ], { duration: 200, easing: 'cubic-bezier(.22,1,.36,1)' });
        (focusTarget || (steps.length ? backButton : panel.querySelector('a, button')))?.focus({ preventScroll: true });
    };
    const goBack = () => {
        const step = steps.pop();
        if (!step) return;
        step.button?.setAttribute('aria-expanded', 'false');
        showPanel(step.panel, true, step.trigger);
    };
    backButton.addEventListener('click', goBack);
    mobileBody.addEventListener('click', event => {
        const control = event.target.closest('a');
        const owner = control?.closest('li');
        const panel = panels.get(owner);
        if (!panel) return;
        event.preventDefault();
        const button = control;
        steps.push({ panel: currentPanel, trigger: control, button, title: owner.querySelector('.header-menu-title')?.textContent || '' });
        button?.setAttribute('aria-expanded', 'true');
        showPanel(panel);
    });
    document.querySelectorAll('[data-header-navigation]').forEach(root => {
        if (drawer.contains(root)) return;
        root.addEventListener('click', event => {
            const button = event.target.closest('[data-submenu-toggle]');
            if (!button) return;
            const open = button.getAttribute('aria-expanded') !== 'true';
            const parent = button.parentElement;
            Array.from(parent.parentElement.children).filter(item => item !== parent).forEach(item => collapseAll(item));
            setExpanded(button, open);
        });
        root.addEventListener('keydown', event => {
            if (event.key !== 'Escape') return;
            const item = event.target.closest('.is-submenu-open');
            const button = item?.querySelector(':scope > [data-submenu-toggle]');
            if (button) { event.preventDefault(); event.stopPropagation(); setExpanded(button, false); button.focus(); }
        });
        if (root.closest('.site-header')) {
            root.querySelectorAll('.menu-item-has-children').forEach(item => {
                const button = item.querySelector(':scope > [data-submenu-toggle]');
                item.addEventListener('pointerenter', event => {
                    if (!desktop.matches || event.pointerType === 'touch') return;
                    cancelLeave(item);
                    Array.from(item.parentElement.children).filter(sibling => sibling !== item).forEach(sibling => collapseAll(sibling));
                    setExpanded(button, true);
                });
                item.addEventListener('pointerleave', event => {
                    if (!desktop.matches || event.pointerType === 'touch') return;
                    cancelLeave(item);
                    // Brief exit tolerance protects diagonal travel between floating panels.
                    leaveTimers.set(item, setTimeout(() => {
                        leaveTimers.delete(item);
                        if (!item.matches(':hover') && !item.contains(document.activeElement)) collapseAll(item);
                    }, 120));
                });
            });
            root.addEventListener('focusout', () => setTimeout(() => {
                if (!root.contains(document.activeElement)) collapseAll(root);
            }, 0));
        }
    });
    backdrop.addEventListener('click', () => collapseAll(header));
    document.addEventListener('click', event => {
        if (!header.contains(event.target)) collapseAll(header);
    });
    const resetDrawer = (restoreFocus = true) => {
        drawer.close();
        document.documentElement.classList.remove('header-drawer-open');
        trigger.setAttribute('aria-expanded', 'false');
        collapseAll(drawer);
        mobileBody.querySelectorAll('a[aria-expanded]').forEach(link => link.setAttribute('aria-expanded', 'false'));
        stepAnimation?.cancel();
        if (currentPanel) currentPanel.hidden = true;
        currentPanel = mobileMenu;
        if (mobileMenu) mobileMenu.hidden = false;
        steps.length = 0;
        stepBar.hidden = true;
        closing = false;
        if (restoreFocus && !desktop.matches) trigger.focus();
    };
    const close = async () => {
        if (!drawer.open || closing) return;
        closing = true;
        if (!reduced.matches) await drawer.animate(
            [{ transform: 'translateX(0)' }, { transform: 'translateX(100%)' }],
            { duration: 220, easing: 'ease-in' }
        ).finished.catch(() => {});
        resetDrawer();
    };
    // Remove the top-layer drawer before the browser captures the outgoing page.
    // Navigation remains native: no click interception or artificial delay.
    window.addEventListener('pageswap', () => {
        if (drawer.open) resetDrawer(false);
    });
    window.addEventListener('pageshow', event => {
        if (event.persisted && drawer.open) resetDrawer(false);
    });
    trigger.addEventListener('click', () => {
        if (desktop.matches || drawer.open) return;
        collapseAll(header);
        drawer.showModal();
        document.documentElement.classList.add('header-drawer-open');
        trigger.setAttribute('aria-expanded', 'true');
        if (!reduced.matches) drawer.animate(
            [{ transform: 'translateX(100%)' }, { transform: 'translateX(0)' }],
            { duration: 280, easing: 'ease-out' }
        );
    });
    drawer.querySelector('[data-mobile-menu-close]').addEventListener('click', close);
    drawer.addEventListener('cancel', event => { event.preventDefault(); if (steps.length) goBack(); else close(); });
    drawer.addEventListener('click', event => { if (event.target === drawer) close(); });
    desktop.addEventListener('change', () => { collapseAll(header); if (desktop.matches) close(); });
});
