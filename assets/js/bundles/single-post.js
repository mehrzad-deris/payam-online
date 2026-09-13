const initSingleArticle = () => {
    document.querySelectorAll('[data-single-article]').forEach((article) => {
        if (article.dataset.singleArticleReady === 'true') return;
        article.dataset.singleArticleReady = 'true';

        article.querySelectorAll('[data-article-toc]').forEach((toc) => {
            const button = toc.querySelector('button[aria-controls]');
            const panel = toc.querySelector('.article-toc-panel');
            if (!button || !panel) return;

            button.addEventListener('click', () => {
                const open = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!open));
                panel.hidden = open;
                toc.classList.toggle('is-open', !open);
            });

            panel.addEventListener('click', (event) => {
                const link = event.target.closest('a[href^="#"]');
                if (!link) return;
                const rawId = link.getAttribute('href').slice(1);
                let target = document.getElementById(rawId);
                if (!target) {
                    try { target = document.getElementById(decodeURIComponent(rawId)); } catch { return; }
                }
                if (!target) return;
                event.preventDefault();
                const headerOffset = (document.querySelector('.site-header')?.offsetHeight || 80) + 24;
                window.scrollTo({ top: window.scrollY + target.getBoundingClientRect().top - headerOffset, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
                history.replaceState(null, '', link.hash);
            });
        });

        const toggle = article.querySelector('[data-comment-form-toggle]');
        const invitation = article.querySelector('[data-comment-invitation]');
        const form = article.querySelector('[data-comment-form]');

        if (toggle && invitation && form) {
            toggle.addEventListener('click', () => {
                toggle.setAttribute('aria-expanded', 'true');
                invitation.classList.add('is-leaving');
                window.setTimeout(() => {
                    invitation.hidden = true;
                    form.hidden = false;
                    requestAnimationFrame(() => form.classList.add('is-visible'));
                    form.querySelector('input, textarea')?.focus({ preventScroll: true });
                }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220);
            });
        }
    });
};

document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', initSingleArticle, { once: true })
    : initSingleArticle();
