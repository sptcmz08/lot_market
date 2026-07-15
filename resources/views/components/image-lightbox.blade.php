@once
    <style>
        .image-lightbox-trigger {
            border: 0;
            padding: 0;
            background: transparent;
            cursor: zoom-in;
            display: inline-block;
            font: inherit;
        }

        .image-lightbox-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(20, 20, 28, 0.78);
            backdrop-filter: blur(6px);
        }

        .image-lightbox-backdrop.is-open {
            display: flex;
        }

        .image-lightbox-panel {
            position: relative;
            width: min(100%, 980px);
            max-height: 92vh;
            border-radius: 18px;
            background: #11131a;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        .image-lightbox-close {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 50%;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.58);
            cursor: pointer;
            font-size: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .image-lightbox-img {
            display: block;
            width: 100%;
            max-height: 86vh;
            object-fit: contain;
            background: #11131a;
        }

        .image-lightbox-caption {
            padding: 10px 16px 14px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            background: #11131a;
        }

        @media (max-width: 640px) {
            .image-lightbox-backdrop {
                padding: 12px;
            }

            .image-lightbox-panel {
                border-radius: 14px;
            }

            .image-lightbox-close {
                top: 8px;
                right: 8px;
                width: 38px;
                height: 38px;
            }
        }
    </style>

    <div class="image-lightbox-backdrop" id="image-lightbox" aria-hidden="true">
        <div class="image-lightbox-panel" role="dialog" aria-modal="true" aria-label="ดูรูปภาพ">
            <button type="button" class="image-lightbox-close" aria-label="ปิดรูปภาพ">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img class="image-lightbox-img" id="image-lightbox-img" src="" alt="">
            <div class="image-lightbox-caption" id="image-lightbox-caption"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lightbox = document.getElementById('image-lightbox');
            const img = document.getElementById('image-lightbox-img');
            const caption = document.getElementById('image-lightbox-caption');
            const closeBtn = lightbox ? lightbox.querySelector('.image-lightbox-close') : null;

            if (!lightbox || !img || !caption || !closeBtn) {
                return;
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                img.src = '';
                img.alt = '';
                caption.textContent = '';
                document.body.style.overflow = '';
            }

            document.addEventListener('click', function (event) {
                const trigger = event.target.closest('[data-lightbox-src]');

                if (!trigger) {
                    return;
                }

                event.preventDefault();
                img.src = trigger.dataset.lightboxSrc;
                img.alt = trigger.dataset.lightboxAlt || 'รูปภาพ';
                caption.textContent = trigger.dataset.lightboxAlt || '';
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            });

            closeBtn.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', function (event) {
                if (event.target === lightbox) {
                    closeLightbox();
                }
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
                    closeLightbox();
                }
            });
        });
    </script>
@endonce
