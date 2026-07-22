/* Stars Hero Section */
class StarField {
    constructor(canvas, options = {}) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');

        this.options = {
            minStars: 80,
            maxStars: 100,

            minDuration: 2500,
            maxDuration: 6000,

            minOpacity: 0.08,
            maxOpacity: 1,

            meteorInterval: 5000,
            meteorDuration: 850,

            ...options,
        };

        this.stars = [];
        this.meteor = null;

        this.animationFrame = null;
        this.lastFrameTime = 0;
        this.lastMeteorTime = performance.now();
        this.nextMeteorDelay = this.getNextMeteorDelay();

        this.fpsInterval = 1000 / 30;

        this.reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

        this.resize = this.resize.bind(this);
        this.animate = this.animate.bind(this);

        this.resizeObserver = new ResizeObserver(this.resize);
        this.resizeObserver.observe(this.canvas.parentElement);

        this.resize();
    }

    getNextMeteorDelay() {
        return this.options.meteorInterval * (0.9 + Math.random() * 0.2);
    }

    resize() {
        const parent = this.canvas.parentElement;
        const rect = parent.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio || 1, 2);

        this.width = rect.width;
        this.height = rect.height;

        this.canvas.width = Math.round(this.width * dpr);
        this.canvas.height = Math.round(this.height * dpr);

        this.canvas.style.width = `${this.width}px`;
        this.canvas.style.height = `${this.height}px`;

        this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        this.createStars();
        this.draw(performance.now());

    }

    start() {
        if (
            this.reducedMotion ||
            this.animationFrame ||
            document.hidden
        ) {
            return;
        }

        this.lastFrameTime = performance.now();
        this.animationFrame = requestAnimationFrame(this.animate);
    }

    stop() {
        if (!this.animationFrame) {
            return;
        }

        cancelAnimationFrame(this.animationFrame);
        this.animationFrame = null;
    }

    createStars() {
        const {
            minStars,
            maxStars,
            minDuration,
            maxDuration,
            minOpacity,
            maxOpacity,
        } = this.options;

        const count =
            Math.floor(Math.random() * (maxStars - minStars + 1)) + minStars;

        this.stars = Array.from({ length: count }, () => ({
            x: Math.random() * this.width,
            y: Math.random() * this.height,

            radius: Math.random() > 0.2 ? 1 : 0.5,

            phase: Math.random() * Math.PI * 2,

            duration:
                minDuration +
                Math.random() * (maxDuration - minDuration),

            minOpacity:
                minOpacity + Math.random() * 0.12,

            maxOpacity:
                maxOpacity - Math.random() * 0.35,
        }));
    }

    createMeteor(time) {
        const direction = Math.random() > 0.5 ? 1 : -1;

        const startX =
            direction === 1
                ? this.width * (0.05 + Math.random() * 0.35)
                : this.width * (0.6 + Math.random() * 0.35);

        const startY = this.height * (0.05 + Math.random() * 0.25);

        const distance = Math.max(this.width, this.height) * 0.55;
        const angle = Math.PI / 4 + (Math.random() - 0.5) * 0.15;

        this.meteor = {
            startTime: time,
            duration: this.options.meteorDuration,

            startX,
            startY,

            endX: startX + Math.cos(angle) * distance * direction,
            endY: startY + Math.sin(angle) * distance,

            length: 110 + Math.random() * 70,
            width: 1.2 + Math.random() * 0.8,
        };
    }

    drawStars(time) {
        for (const star of this.stars) {
            const progress = (time % star.duration) / star.duration;

            const wave =
                (Math.sin(progress * Math.PI * 2 + star.phase) + 1) / 2;

            const opacity =
                star.minOpacity +
                wave * (star.maxOpacity - star.minOpacity);

            this.ctx.beginPath();
            this.ctx.arc(
                star.x,
                star.y,
                star.radius,
                0,
                Math.PI * 2
            );

            this.ctx.fillStyle = `rgba(255,255,255,${opacity})`;
            this.ctx.fill();
        }
    }

    drawMeteor(time) {
        if (!this.meteor) {
            return;
        }

        const elapsed = time - this.meteor.startTime;
        const progress = elapsed / this.meteor.duration;

        if (progress >= 1) {
            this.meteor = null;
            return;
        }

        const eased = 1 - Math.pow(1 - progress, 3);

        const x =
            this.meteor.startX +
            (this.meteor.endX - this.meteor.startX) * eased;

        const y =
            this.meteor.startY +
            (this.meteor.endY - this.meteor.startY) * eased;

        const dx = this.meteor.endX - this.meteor.startX;
        const dy = this.meteor.endY - this.meteor.startY;
        const distance = Math.hypot(dx, dy);

        const unitX = dx / distance;
        const unitY = dy / distance;

        const tailX = x - unitX * this.meteor.length;
        const tailY = y - unitY * this.meteor.length;

        const fadeIn = Math.min(progress / 0.12, 1);
        const fadeOut = Math.min((1 - progress) / 0.22, 1);
        const opacity = Math.min(fadeIn, fadeOut);

        const gradient = this.ctx.createLinearGradient(
            x,
            y,
            tailX,
            tailY
        );

        gradient.addColorStop(
            0,
            `rgba(255,255,255,${opacity})`
        );

        gradient.addColorStop(
            0.18,
            `rgba(255,255,255,${opacity * 0.75})`
        );

        gradient.addColorStop(
            1,
            'rgba(255,255,255,0)'
        );

        this.ctx.save();

        this.ctx.beginPath();
        this.ctx.moveTo(x, y);
        this.ctx.lineTo(tailX, tailY);

        this.ctx.strokeStyle = gradient;
        this.ctx.lineWidth = this.meteor.width;
        this.ctx.lineCap = 'round';

        this.ctx.shadowColor = `rgba(255,255,255,${opacity})`;
        this.ctx.shadowBlur = 7;

        this.ctx.stroke();

        this.ctx.beginPath();
        this.ctx.arc(x, y, 1.5, 0, Math.PI * 2);
        this.ctx.fillStyle = `rgba(255,255,255,${opacity})`;
        this.ctx.fill();

        this.ctx.restore();
    }

    draw(time) {
        this.ctx.clearRect(0, 0, this.width, this.height);

        this.drawStars(time);
        this.drawMeteor(time);
    }

    animate(time) {
        if (!this.animationFrame) {
            return;
        }

        this.animationFrame = requestAnimationFrame(this.animate);

        if (
            !this.meteor &&
            time - this.lastMeteorTime >= this.nextMeteorDelay
        ) {
            this.createMeteor(time);

            this.lastMeteorTime = time;
            this.nextMeteorDelay = this.getNextMeteorDelay();
        }

        if (time - this.lastFrameTime < this.fpsInterval) {
            return;
        }

        this.lastFrameTime = time;
        this.draw(time);
    }

    destroy() {
        this.stop();
        this.resizeObserver.disconnect();
    }
}

const canvas = document.getElementById('hero-stars');

if (canvas) {
    const starField = new StarField(canvas, {
        minStars: 80,
        maxStars: 100,

        minDuration: 2200,
        maxDuration: 3000,

        meteorInterval: 5000,
        meteorDuration: 850,
    });

    let isCanvasVisible = false;

    const visibilityObserver = new IntersectionObserver(
        ([entry]) => {
            isCanvasVisible = entry.isIntersecting;

            if (isCanvasVisible && !document.hidden) {
                starField.start();
            } else {
                starField.stop();
            }
        },
        {
            threshold: 0.01,
        }
    );

    visibilityObserver.observe(canvas);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden || !isCanvasVisible) {
            starField.stop();
        } else {
            starField.start();
        }
    });
}


/* Scale on scroll */
const hero = document.querySelector('.hero-section');
const visual = document.querySelector('.hero-visual');

if (hero && visual) {
    let ticking = false;

    const updateHeroScale = () => {
        const rect = hero.getBoundingClientRect();
        const progress = Math.min(
            Math.max(-rect.top / rect.height, 0),
            1
        );

        const scale = 1 + progress * 0.9;

        visual.style.transform = `scale(${scale})`;

        ticking = false;
    };

    const onScroll = () => {
        if (ticking) return;

        ticking = true;
        requestAnimationFrame(updateHeroScale);
    };

    updateHeroScale();

    window.addEventListener('scroll', onScroll, {
        passive: true,
    });
}


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

    const visibleItemsCount = items.length;

    let nextIndex = visibleItemsCount;
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

    // اگر سه آیتم یا کمتر وجود داشت، نیازی به تعویض نیست.
    if (domainPrices.length <= visibleItemsCount) {
        return;
    }

    setInterval(() => {
        const item = items[replacePosition];
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
            replacePosition = (replacePosition + 1) % visibleItemsCount;

            item.classList.remove(
                'opacity-0',
                'scale-95',
                'translate-y-2'
            );
        }, 500);
    }, 3000);
});
