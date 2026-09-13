const initBlogArchive = () => {
    document.querySelectorAll('[data-blog-archive]').forEach((archive) => {
        if (archive.dataset.blogArchiveReady === 'true') return;

        const grid = archive.querySelector('[data-blog-grid]');
        const button = archive.querySelector('[data-blog-load-more]');
        const config = window.payamBlogArchive;

        if (!grid || !button || !config?.ajaxUrl || !config?.nonce) return;

        archive.dataset.blogArchiveReady = 'true';

        const createSkeleton = () => {
            const skeleton = document.createElement('article');
            skeleton.className = 'blog-card-skeleton';
            skeleton.setAttribute('aria-hidden', 'true');
            skeleton.innerHTML = '<span class="skeleton-image"></span><span class="skeleton-title"></span><span class="skeleton-meta"></span>';
            return skeleton;
        };

        button.addEventListener('click', async () => {
            if (button.disabled) return;

            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            const skeletons = Array.from({ length: 6 }, createSkeleton);
            grid.append(...skeletons);

            const body = new FormData();
            body.append('action', 'payam_load_blog_posts');
            body.append('nonce', config.nonce);
            body.append('offset', archive.dataset.offset || '0');
            body.append('categoryId', archive.dataset.categoryId || '0');

            try {
                const response = await fetch(config.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
                const result = await response.json();

                if (!response.ok || !result.success) throw new Error('Request failed');

                const fragment = document.createRange().createContextualFragment(result.data.html || '');
                skeletons.forEach((item) => item.remove());
                grid.append(fragment);
                archive.dataset.offset = String(result.data.nextOffset);

                if (!result.data.hasMore) {
                    button.closest('.blog-load-more-wrap')?.remove();
                    return;
                }

                const label = button.querySelector('[data-blog-load-more-label]');
                if (label) label.textContent = `مشاهده ${Number(result.data.remaining).toLocaleString('fa-IR')} آموزش دیگر`;
            } catch (error) {
                skeletons.forEach((item) => item.remove());
                button.disabled = false;
                button.removeAttribute('aria-busy');
                return;
            }

            button.disabled = false;
            button.removeAttribute('aria-busy');
        });
    });
};

document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', initBlogArchive, { once: true })
    : initBlogArchive();
