document.querySelectorAll('[data-about-video-modal]').forEach((modal) => {
    if (modal.dataset.aboutVideoReady === 'true') {
        return;
    }

    const openButton = document.querySelector(
        `[data-about-video-open][aria-controls="${modal.id}"]`
    );
    const closeButton = modal.querySelector('[data-about-video-close]');
    const player = modal.querySelector('[data-about-video-player]');
    const source = player?.querySelector('[data-video-src]');

    if (!openButton || !closeButton || !player || !source) {
        return;
    }

    modal.dataset.aboutVideoReady = 'true';

    const unloadVideo = () => {
        player.pause();
        source.removeAttribute('src');
        player.load();
        document.documentElement.classList.remove('about-modal-open');
    };

    const closeModal = () => {
        if (modal.open) {
            modal.close();
        }
    };

    openButton.addEventListener('click', () => {
        if (!source.hasAttribute('src')) {
            source.src = source.dataset.videoSrc;
            player.load();
        }

        modal.showModal();
        document.documentElement.classList.add('about-modal-open');

        const playRequest = player.play();

        if (playRequest instanceof Promise) {
            playRequest.catch(() => {});
        }
    });

    closeButton.addEventListener('click', closeModal);
    modal.addEventListener('close', unloadVideo);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
});
