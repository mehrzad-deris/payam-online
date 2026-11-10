const statuses = {available: 'موجود است.', unavailable: 'موجود نیست.', error: 'بررسی ناموفق؛ دوباره تلاش کنید.', pending: 'در حال بررسی…'};
const money = value => new Intl.NumberFormat('fa-IR', {maximumFractionDigits: 2}).format(value);
const node = (tag, cls, text) => { const el = document.createElement(tag); el.className = cls; if (text) el.textContent = text; return el; };

document.querySelectorAll('[data-domain-search-config]').forEach(section => {
    let config;
    try { config = JSON.parse(section.dataset.domainSearchConfig); } catch { return; }
    const form = section.querySelector('.domain-whois');
    const results = section.querySelector('[data-domain-results]');
    if (!form || !results || !config.catalog.length) return;
    const input = form.querySelector('input[name="query"]');
    input.inputMode = 'url'; input.autocapitalize = 'none'; input.spellcheck = false;
    const notice = node('p', 'domain-search-notice');
    notice.id = input.id + '-notice'; notice.setAttribute('role', 'alert');
    const noticeWrap = node('div', 'domain-search-notice-wrap');
    const noticeClip = node('div', 'domain-search-notice-clip');
    noticeClip.append(notice); noticeWrap.append(noticeClip);
    noticeWrap.setAttribute('aria-hidden', 'true'); noticeWrap.inert = true;
    input.setAttribute('aria-describedby', [input.getAttribute('aria-describedby'), notice.id].filter(Boolean).join(' '));
    form.after(noticeWrap);
    const notify = text => {
        const visible = Boolean(text);
        noticeWrap.classList.toggle('is-visible', visible);
        noticeWrap.setAttribute('aria-hidden', visible ? 'false' : 'true');
        noticeWrap.inert = !visible;
        if (visible && notice.textContent !== text) notice.textContent = text;
        input.setAttribute('aria-invalid', visible ? 'true' : 'false');
    };
    const englishOnly = /^[a-zA-Z0-9.-]*$/;
    input.addEventListener('beforeinput', event => {
        if (event.data && !englishOnly.test(event.data) && event.cancelable) {
            event.preventDefault(); notify('فقط حروف انگلیسی، اعداد انگلیسی، نقطه و خط تیره مجاز هستند.');
        }
    });
    input.addEventListener('paste', event => {
        const text = event.clipboardData?.getData('text');
        if (text && !englishOnly.test(text)) { event.preventDefault(); notify('نام دامنه را فقط با حروف و اعداد انگلیسی وارد کنید.'); }
    });
    input.addEventListener('input', () => notify(englishOnly.test(input.value) ? '' : 'نام دامنه را فقط با حروف و اعداد انگلیسی وارد کنید.'));
    let controller, generation = 0;
    const hidden = new Map();
    const message = results.querySelector('[data-domain-message]');
    const featured = results.querySelector('[data-domain-featured]');
    const grid = results.querySelector('[data-domain-grid]');
    const gridTitle = results.querySelector('[data-domain-grid-title]');
    const updateGridVisibility = () => {
        const empty = !Array.from(grid.children).some(card => !card.hidden);
        grid.hidden = empty;
        gridTitle.hidden = empty;
    };
    const recommendation = results.querySelector('[data-domain-recommendation]');
    const recommendationRow = results.querySelector('[data-domain-recommendation-row]');
    const clearButton = node('button', 'domain-search-clear');
    clearButton.append(results.querySelector('[data-domain-close-icon]').content.cloneNode(true));
    clearButton.type = 'button'; clearButton.setAttribute('aria-label', 'پاک‌کردن جستجو و بستن نتایج'); clearButton.hidden = true;
    form.append(clearButton);
    const request = async (action, domains, signal) => {
        const response = await fetch(config.endpoint + action, {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({domains}), signal});
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'ارتباط با سرویس بررسی دامنه برقرار نشد.');
        return data;
    };
    const close = () => {
        notify('');
        generation++; controller?.abort(); results.hidden = true;
        section.classList.remove('has-domain-results');
        clearButton.hidden = true;
        hidden.forEach((wasHidden, el) => { el.hidden = wasHidden; }); hidden.clear();
        form.querySelector('button[type="submit"]').disabled = false;
        form.querySelector('input[name="query"]').focus();
    };
    clearButton.addEventListener('click', () => { close(); form.querySelector('input[name="query"]').value = ''; });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!englishOnly.test(input.value)) { notify('نام دامنه را فقط با حروف و اعداد انگلیسی وارد کنید.'); input.focus(); return; }
        const raw = input.value.toLowerCase();
        const known = [...config.catalog].sort((a,b) => b.extension.length - a.extension.length);
        const match = known.find(item => raw.endsWith('.' + item.extension));
        if (!match && raw.includes('.')) { notify('این پسوند در فهرست دامنه‌های قابل ثبت وجود ندارد.'); input.focus(); return; }
        const name = match ? raw.slice(0, -(match.extension.length + 1)) : raw;
        if (!/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/.test(name) || (!match && raw.includes('.'))) {
            notify('یک نام دامنه معتبر وارد کنید.'); input.focus(); return;
        }
        notify('');
        const main = match || known.find(item => item.extension === 'com') || known[0];
        controller?.abort(); controller = new AbortController(); const run = ++generation;
        const signal = controller.signal;
        document.querySelectorAll('[data-hide-on-domain-results]').forEach(el => {
            if (!el.contains(section) && !hidden.has(el)) { hidden.set(el, el.hidden); el.hidden = true; }
        });
        results.hidden = false; featured.replaceChildren(); grid.replaceChildren();
        recommendationRow.replaceChildren(); recommendation.hidden = true;
        section.classList.add('has-domain-results'); clearButton.hidden = false;
        message.textContent = 'در حال بررسی دامنه‌ها…';
        const items = new Map();
        const add = (item, parent) => {
            const domain = name + '.' + item.extension;
            if (items.has(domain)) return items.get(domain);
            const card = node('article', 'domain-result-card is-pending');
            card.setAttribute('aria-busy', 'true');
            const copy = node('p', 'domain-result-copy');
            const title = node('bdi', 'domain-result-name', domain); title.dir = 'ltr';
            const state = node('span', 'domain-result-status', statuses.pending);
            copy.append(node('span', '', 'دامنه'), title, state);
            const pricing = node('div', 'domain-result-pricing');
            const unit = node('small', '', item.suffix);
            if (['IRT', 'TMN'].includes(item.currency) || item.suffix.includes('تومان')) {
                unit.className = 'domain-result-currency'; unit.textContent = '';
                unit.append(results.querySelector('[data-domain-toman-icon]').content.cloneNode(true));
                unit.setAttribute('aria-label', item.suffix || 'تومان'); unit.title = item.suffix || 'تومان';
            }
            pricing.append(node('strong', '', money(item.price)), unit);
            const button = node('button', 'cta-link cta-btn-primary', 'ثبت دامنه'); button.type = 'button'; button.disabled = true;
            card.append(copy, pricing, button); parent.append(card);
            const entry = {item, domain, card, state, button, status: 'pending'}; items.set(domain, entry);
            button.addEventListener('click', () => {
                if (entry.status !== 'available' || !config.orderUrl) return;
                const url = new URL(config.orderUrl);
                url.searchParams.set('query', domain);
                window.location.assign(url.href);
            });
            return entry;
        };
        const primary = add(main, featured);
        primary.card.classList.add('domain-result-primary');
        const seen = new Set([main.extension]);
        const candidates = config.suggestions.filter(item => {
            if (seen.has(item.extension)) return false;
            seen.add(item.extension); return true;
        });
        const confirmedCount = () => Array.from(grid.children).filter(card => !card.hidden && card.dataset.status === 'available').length;
        updateGridVisibility();
        const updateRecommendation = () => {
            if (primary.status !== 'unavailable') { recommendation.hidden = true; return; }
            const alternative = [...items.values()].find(entry => entry !== primary && entry.status === 'available' && !entry.button.disabled);
            if (!alternative) return;
            alternative.card.classList.add('domain-result-recommended');
            recommendationRow.replaceChildren(alternative.card);
            recommendation.hidden = false;
        };
        results.scrollIntoView({behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'start'});
        let cursor = 0;
        let firstBatch = true;
        while (firstBatch || (config.orderUrl && cursor < candidates.length && confirmedCount() < 10)) {
            if (signal.aborted) break;
            const batch = firstBatch ? [primary] : [];
            firstBatch = false;
            const slots = Math.min(4 - batch.length, 10 - confirmedCount());
            for (let i = 0; i < slots && cursor < candidates.length; i++) batch.push(add(candidates[cursor++], grid));
            updateGridVisibility();
            let data;
            let requestFailed = false;
            try { data = await request('check', batch.map(item => item.domain), signal); }
            catch (error) { if (error.name === 'AbortError') break; requestFailed = true; data = {results: {}}; if (run === generation) message.textContent = error.message; }
            if (run !== generation) return;
            batch.forEach(entry => {
                entry.status = data.results[entry.domain] || 'error';
                entry.card.classList.remove('is-pending'); entry.card.dataset.status = entry.status;
                entry.card.setAttribute('aria-busy', 'false');
                entry.card.hidden = entry !== primary && (entry.status !== 'available' || !config.orderUrl);
                entry.state.textContent = statuses[entry.status]; entry.button.disabled = entry.status !== 'available' || !config.orderUrl;
                entry.card.querySelector('.domain-result-pricing').hidden = entry.status === 'unavailable' || entry.status === 'error';
                entry.button.hidden = entry.status === 'unavailable' || entry.status === 'error';
            });
            updateRecommendation();
            updateGridVisibility();
            message.textContent = primary.status === 'error' ? 'بررسی دامنه انجام نشد؛ دوباره تلاش کنید.' : primary.domain + ' — ' + statuses[primary.status];
            if (requestFailed) break;
        }
    });
});
