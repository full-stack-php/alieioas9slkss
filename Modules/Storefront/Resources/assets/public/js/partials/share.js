// partials/share.js

export class ShareModal {
    constructor() {
        this.modalId = 'modal-share';
    }

    // === 1. Методы модального окна ===
    render() {
        if (!document.getElementById(this.modalId)) {
            const html = `
                <div id="${this.modalId}" class="modal fade">
                  <div class="modal-dialog chm-modal sm-modal-4 modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                          <div class="modal-title">Поделиться</div>
                          <button type="button" class="close-modal" data-dismiss="modal" aria-hidden="true">
                              <svg class="icon-close"><use xlink:href="#cross"></use></svg>
                          </button>
                        </div>
                        <div class="modal-body">
                             <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                                <a class="a2a_button_telegram"></a>
                                <a class="a2a_button_facebook"></a>
                                <a class="a2a_button_twitter"></a>
                                <a class="a2a_button_whatsapp"></a>
                                <a class="a2a_button_facebook_messenger"></a>
                                <a class="a2a_button_viber"></a>
                                <a class="a2a_button_google_gmail"></a>
                             </div>
                        </div>
                    </div>
                  </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
        }
    }

    initAddToAny() {
        if (window.a2a) {
            window.a2a.init_all();
        } else {
            const script = document.createElement('script');
            script.src = "https://static.addtoany.com/menu/page.js";
            script.async = true;
            document.body.appendChild(script);
        }
    }

    open() {
        this.render();
        $(`#${this.modalId}`).modal('show'); // Оставляем вызов Bootstrap модалки
        this.initAddToAny();
    }

    bindEvents(selector) {
        document.addEventListener('click', (event) => {
            const trigger = event.target.closest(selector);

            if (trigger) {
                event.preventDefault();
                this.open();
            }
        });
    }

    // === 2. Методы позиционирования кнопки (Новое) ===
    moveShareButton() {
        // Ищем элементы. Кнопку ищем просто по классу, так как её родитель будет меняться
        const button = document.querySelector('.top-product-button');
        const mobileContainer = document.querySelector('#main_product .right-block-inner');
        const desktopContainer = document.querySelector('.tabs__header .container-tab');

        // Если хотя бы одного элемента нет на странице — прерываем выполнение (защита от ошибок)
        if (!button || !mobileContainer || !desktopContainer) return;

        if (window.innerWidth <= 991) {
            // Для мобильных: убираем классы скрытия и вставляем в начало блока (prepend)
            button.classList.remove('d-none', 'd-md-block');
            mobileContainer.prepend(button);
        } else {
            // Для ПК: возвращаем классы скрытия и вставляем в конец табов (append)
            button.classList.add('d-none', 'd-md-block');
            desktopContainer.append(button);
        }
    }

    initButtonPlacement() {
        // 1. Выполняем перемещение один раз при загрузке страницы
        this.moveShareButton();

        // 2. Вешаем слушатель ресайза с задержкой (аналог вашего sl_delay на 50ms)
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                this.moveShareButton();
            }, 50);
        }, { passive: true });
    }
}
