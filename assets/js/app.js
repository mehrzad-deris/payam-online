/* Services Swiper */
var ServicesCarousel = new Swiper('.servicesSwiper', {
    slidesPerView: 3,
    spaceBetween: 20,
    loop: true,
    navigation: {
        nextEl: '.servicesSwiper .button-next', prevEl: '.servicesSwiper .button-prev',
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
    },
});

/* Tab Module */
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.tab-btn');
    const tabs = document.querySelectorAll('.tab-content');

    if (!buttons.length || !tabs.length) {
        return;
    }

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.tab;

            buttons.forEach((item) => {
                item.classList.toggle('active', item === button);
            });

            tabs.forEach((tab) => {
                const isActive = tab.id === targetId;

                tab.classList.toggle('active', isActive);
                tab.classList.toggle('visible', isActive);
                tab.classList.toggle('invisible', !isActive);
                tab.classList.toggle('opacity-100', isActive);
                tab.classList.toggle('opacity-0', !isActive);
                tab.classList.toggle('translate-y-0', isActive);
                tab.classList.toggle('translate-y-2', !isActive);
                tab.classList.toggle('pointer-events-none', !isActive);
            });
        });
    });

});
/* Load More Features */
document.addEventListener('DOMContentLoaded', () => {
    const wrappers = document.querySelectorAll('.plans-wrapper');

    wrappers.forEach((wrapper) => {
        const buttons = wrapper.querySelectorAll('.show-more-features');
        const extraContainers = wrapper.querySelectorAll('[data-extra-features]');

        if (!buttons.length || !extraContainers.length) {
            return;
        }

        buttons.forEach((clickedButton) => {
            clickedButton.addEventListener('click', () => {
                const isExpanded = clickedButton.getAttribute('aria-expanded') === 'true';

                const newState = !isExpanded;

                extraContainers.forEach((container) => {
                    container.classList.toggle('grid-rows-[1fr]', newState);

                    container.classList.toggle('grid-rows-[0fr]', !newState);

                    container.classList.toggle('opacity-100', newState);

                    container.classList.toggle('opacity-0', !newState);

                    container.setAttribute('aria-hidden', String(!newState));
                });

                buttons.forEach((button) => {
                    const buttonText = button.querySelector('[data-button-text]');
                    const arrow = button.querySelector('.arrow');

                    button.setAttribute('aria-expanded', String(newState));

                    if (buttonText) {
                        buttonText.textContent = newState ? button.dataset.lessText : button.dataset.moreText;
                    }

                    if (arrow) {
                        arrow.classList.toggle('rotate-180', newState);
                    }
                });
            });
        });
    });
});

/* Video modal */
const modal = document.querySelector('[data-video-modal]');
const video = modal?.querySelector('[data-modal-video]');
const openButtons = document.querySelectorAll('[data-video-modal-open]');
const closeButtons = modal?.querySelectorAll('[data-video-modal-close]');
function loadVideo() {
    if (!video || video.src) {
        return;
    }
    const videoSrc = video.dataset.src;
    if (!videoSrc) {
        return;
    }
    video.src = videoSrc;
    video.load();
}
function openVideoModal() {
    if (!modal || !video) {
        return;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
    loadVideo();
    video.play().catch(() => {
    });
}
function closeVideoModal() {
    if (!modal || !video) {
        return;
    }
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
    video.pause();
    video.currentTime = 0;
}
openButtons.forEach((button) => {
    button.addEventListener('click', openVideoModal);
});
closeButtons?.forEach((button) => {
    button.addEventListener('click', closeVideoModal);
});
document.addEventListener('keydown', (event) => {
    if (
        event.key === 'Escape' &&
        modal &&
        !modal.classList.contains('hidden')
    ) {
        closeVideoModal();
    }
});