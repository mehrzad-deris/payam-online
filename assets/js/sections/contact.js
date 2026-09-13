(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    document.querySelectorAll('[data-contact-form]').forEach((form) => {
        if (form.dataset.ready) return;
        form.dataset.ready = 'true';
        const box = form.closest('[data-contact-form-box]');
        const status = box.querySelector('[data-form-status]');
        const submit = form.querySelector('[type="submit"]');
        const refresh = form.querySelector('[data-captcha-refresh]');
        const answer = form.querySelector('[data-captcha-answer]');
        const inputs = [...form.querySelectorAll('[data-form-field]')];
        let challenge = null;
        let busy = false;
        let loading = false;

        const message = (text, success = false) => {
            status.textContent = text;
            status.hidden = false;
            status.classList.toggle('is-success', success);
        };
        const request = async (action, values = {}) => {
            const controller = new AbortController();
            const timer = window.setTimeout(() => controller.abort(), 20000);
            try {
                const response = await fetch(form.dataset.endpoint, {
                    method: 'POST', credentials: 'same-origin', cache: 'no-store',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body: new URLSearchParams({ action, form_id: form.dataset.formId, ...values }),
                    signal: controller.signal,
                });
                const result = await response.json();
                if (!result.success) throw Object.assign(new Error(result.data?.message || 'ارسال انجام نشد. دوباره تلاش کنید.'), { errors: result.data?.errors });
                return result.data;
            } finally { clearTimeout(timer); }
        };
        const loadChallenge = async () => {
            if (loading) return;
            loading = true;
            challenge = null;
            submit.disabled = true;
            refresh.disabled = true;
            answer.value = '';
            try {
                challenge = await request('payam_form_challenge');
                form.querySelector('[data-captcha-question]').textContent = challenge.question;
            } catch (error) {
                message(error.message === 'Failed to fetch' ? 'ارتباط برقرار نشد. روی عبارت جدید کلیک کنید.' : error.message);
            } finally {
                loading = false;
                refresh.disabled = false;
                submit.disabled = !challenge || busy;
            }
        };
        refresh.addEventListener('click', () => { if (!busy) loadChallenge(); });
        form.addEventListener('focusin', () => { if (!challenge && !loading) loadChallenge(); }, { once: true });
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (busy || loading || !form.reportValidity()) return;
            if (!challenge) { await loadChallenge(); return; }
            busy = true;
            submit.disabled = true;
            refresh.disabled = true;
            form.setAttribute('aria-busy', 'true');
            status.hidden = true;
            inputs.forEach((input) => {
                input.removeAttribute('aria-invalid');
                input.closest('.contact-field').querySelector('[data-field-error]').textContent = '';
            });
            try {
                const result = await request('payam_form_submit', {
                    nonce: challenge.nonce,
                    captcha_token: challenge.token,
                    captcha_answer: answer.value,
                    website: form.querySelector('[data-form-trap]').value,
                    values: JSON.stringify(Object.fromEntries(inputs.map((input) => [input.name, input.value]))),
                });
                if (!reducedMotion.matches && form.animate) {
                    await form.animate([{ opacity: 1 }, { opacity: 0, transform: 'translateY(8px)' }], { duration: 220, fill: 'forwards' }).finished;
                }
                form.hidden = true;
                message(result.message, true);
                status.focus({ preventScroll: true });
            } catch (error) {
                message(error.name === 'AbortError' ? 'پاسخ سرور دریافت نشد؛ ممکن است پیام ثبت شده باشد. پیش از ارسال مجدد کمی صبر کنید.' : error.message);
                inputs.forEach((input) => {
                    if (!error.errors?.[input.name]) return;
                    input.setAttribute('aria-invalid', 'true');
                    input.closest('.contact-field').querySelector('[data-field-error]').textContent = error.errors[input.name];
                });
                (inputs.find((input) => input.getAttribute('aria-invalid')) || status).focus({ preventScroll: true });
                await loadChallenge();
            } finally {
                busy = false;
                form.removeAttribute('aria-busy');
                refresh.disabled = false;
                submit.disabled = !challenge;
            }
        });
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(([entry]) => {
                if (!entry.isIntersecting) return;
                loadChallenge();
                observer.disconnect();
            }, { rootMargin: '150px' });
            observer.observe(form);
        } else { loadChallenge(); }
    });
})();
