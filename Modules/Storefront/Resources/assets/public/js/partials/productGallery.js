export function initProductGallery() {
    const additionalSlides = document.querySelectorAll('.image-additional .swiper-slide');
    const numAdditionalImages = additionalSlides.length;

    if (numAdditionalImages <= 5) {
        document.querySelectorAll('.image-additional .prev-image, .image-additional .next-image')
            .forEach(btn => btn.style.display = 'none');
    }

    const loopSetting = false;

    const galleryThumbsOpts = {
        observer: true,
        observeParents: true,
        speed: 400,
        slidesPerView: 5,
        slideToClickedSlide: true,
        direction: 'horizontal',
        loop: loopSetting,
        breakpoints: {
            768: {
                direction: 'vertical',
            },
        },
    };

    const thumbsContainer = document.querySelector('.image-additional .swiper');
    if (!thumbsContainer) return;

    const galleryThumbs = new Swiper(thumbsContainer, galleryThumbsOpts);

    const mob_nx = document.querySelector('.next-image-mobile');
    const mob_pr = document.querySelector('.prev-image-mobile');

    const galleryMainOpts = {
        observer: true,
        observeParents: true,
        spaceBetween: 0,
        speed: 400,
        slidesPerView: 1,
        slideToClickedSlide: true,
        nested: false,
        loop: false,
        pagination: {
            el: '.thumbnails .swiper-pagination',
            type: 'bullets',
            enabled: true,
        },
        navigation: {
            nextEl: mob_nx,
            prevEl: mob_pr,
        },
        breakpoints: {
            768: {
                spaceBetween: 10,
                pagination: {
                    enabled: false,
                }
            },
        },
        thumbs: {
            swiper: galleryThumbs
        },
    };

    const mainContainer = document.querySelector('#image-box');
    if (!mainContainer) return;

    const galleryMain = new Swiper(mainContainer, galleryMainOpts);
    const generalImageBlock = document.querySelector('.image-block .general-image');

    function stopInactiveVideos() {
        const activeSlide = galleryMain.slides[galleryMain.activeIndex];

        galleryMain.slides.forEach(slide => {
            if (slide === activeSlide) {
                return;
            }

            const videoContainer = slide.querySelector('.video-link .video-container');

            if (videoContainer && videoContainer.innerHTML.trim() !== '') {
                videoContainer.innerHTML = '';
            }

            const videoTag = slide.querySelector('video');

            if (videoTag) {
                videoTag.pause();
                videoTag.currentTime = 0;
            }
        });
    }

    function renderVideoInSlide(slide) {
        const videoLinkElement = slide?.querySelector('.video-link');

        if (!videoLinkElement) {
            return;
        }

        const videoFormat = videoLinkElement.dataset.videoFormat;
        const videoId = videoLinkElement.dataset.videoId;
        const videoLink = videoLinkElement.dataset.videoLink;
        const videoWrapper = videoLinkElement.querySelector('.video-container');

        if (!videoFormat || !videoWrapper || videoWrapper.innerHTML.trim() !== '') {
            return;
        }

        let videoEmbed = '';

        if (videoFormat === 'youtube' && videoId) {
            videoEmbed = `
                <iframe
                    width="100%"
                    height="100%"
                    src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&playsinline=1"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            `;
        }

        if (videoFormat === 'local' && videoLink) {
            videoEmbed = `
                <video width="100%" height="100%" controls autoplay playsinline>
                    <source src="${videoLink}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            `;
        }

        if (videoEmbed) {
            videoWrapper.innerHTML = videoEmbed;
        }
    }

    function updateStickerVisibility() {
        const currentSlide = galleryMain.slides[galleryMain.activeIndex];
        const videoLinkElement = currentSlide?.querySelector('.video-link');

        if (!generalImageBlock) {
            return;
        }

        if (videoLinkElement && videoLinkElement.dataset.videoFormat) {
            generalImageBlock.classList.add('hide-sticker');
        } else {
            generalImageBlock.classList.remove('hide-sticker');
        }
    }

    document.querySelectorAll('.image-additional .next-image, .general-image .next-image-mobile').forEach(btn => {
        btn.addEventListener('click', () => galleryMain.slideNext());
    });

    document.querySelectorAll('.image-additional .prev-image, .general-image .prev-image-mobile').forEach(btn => {
        btn.addEventListener('click', () => galleryMain.slidePrev());
    });

    document.querySelectorAll('.image-additional .swiper-slide').forEach(slide => {
        slide.addEventListener('click', (event) => {
            const thumbnail = event.target.closest('[data-num]');

            if (!thumbnail) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const index = parseInt(thumbnail.dataset.num, 10);

            if (Number.isNaN(index)) {
                return;
            }

            galleryMain.slideTo(index);
        });
    });

    galleryMain.on('slideChange', () => {
        stopInactiveVideos();

        const currentSlide = galleryMain.slides[galleryMain.activeIndex];

        renderVideoInSlide(currentSlide);
        updateStickerVisibility();
    });

    galleryMain.on('slideChangeTransitionStart', () => {
        updateStickerVisibility();
    });

    galleryMain.on('slideChangeTransitionEnd', () => {
        updateStickerVisibility();
    });

    /**
     * Если первый активный слайд сразу видео.
     */
    renderVideoInSlide(galleryMain.slides[galleryMain.activeIndex]);
    updateStickerVisibility();

    document.addEventListener('click', (e) => {
        const mainImageClick = e.target.closest('.thumbnails #image-box .slider-main-img');

        if (!mainImageClick) {
            return;
        }

        e.preventDefault();

        const linkElements = document.querySelectorAll('.thumbnails .general-image .slider-main-img .item span');

        const items = Array.from(linkElements).map(link => {
            const img = link.querySelector('img');
            const isVideo = link.dataset.type === 'video';
            const videoFormat = link.dataset.videoFormat;
            const videoId = link.dataset.videoId;
            const videoLink = link.dataset.videoLink;

            if (isVideo) {
                if (videoFormat === 'youtube' && videoId) {
                    return {
                        src: `https://www.youtube.com/embed/${videoId}?autoplay=1&enablejsapi=1`,
                        type: 'iframe',
                        opts: {
                            caption: link.getAttribute('title') || '',
                            thumb: link.dataset.thumb || ''
                        }
                    };
                }

                if (videoFormat === 'local' && videoLink) {
                    return {
                        src: videoLink,
                        type: 'video',
                        opts: {
                            caption: link.getAttribute('title') || '',
                            thumb: link.dataset.thumb || ''
                        }
                    };
                }
            }

            return {
                src: link.dataset.src || link.getAttribute('href'),
                opts: {
                    caption: link.getAttribute('title') || '',
                    type: 'image',
                    thumb: img ? img.src : ''
                }
            };
        });

        const startIndex = galleryMain.realIndex;

        if (window.jQuery && window.jQuery.fancybox) {
            window.jQuery.fancybox.open(items, {
                loop: true,
                hideScrollbar: false,
                idleTime: false,
                buttons: ['zoom', 'slideShow', 'fullScreen', 'thumbs', 'close'],
                helpers: { media: {} },

                beforeShow: function(instance, current) {
                    if (current.type === 'iframe' || current.type === 'video') {
                        current.thumb = '';
                    }
                },

                afterShow: function(instance, current) {
                    if (current.type === 'video') {
                        current.$content.find('video').attr('playsinline', '');
                    }
                }
            }, startIndex);
        } else {
            console.warn('Fancybox не загружен.');
        }
    });
}
