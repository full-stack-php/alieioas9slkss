// partials/autocomplete.js

export class AutocompleteSearch {
    constructor(inputElement, options) {
        this.input = inputElement;
        this.options = Object.assign({}, options);
        this.timer = null;
        this.init();
    }

    init() {
        this.input.setAttribute('autocomplete', 'off');

        const nextEl = this.input.nextElementSibling;
        if (!nextEl || !nextEl.classList.contains('search_autocomplete')) {
            const html = `
                <div class="search_autocomplete" style="display: none;">
                    <div class="autocomplete-wrapper">
                        <ul class="list-unstyled autosearch"></ul>
                    </div>
                </div>`;
            this.input.insertAdjacentHTML('afterend', html);
        }

        this.dropdown = this.input.nextElementSibling;
        this.bindEvents();
        this.initMobileScroll();
    }

    bindEvents() {
        this.input.addEventListener('focus', () => this.request());

        this.input.addEventListener('blur', () => {
            setTimeout(() => this.hide(), 200);
        });

        this.input.addEventListener('keyup', (event) => {
            if (event.key === 'Escape' || event.keyCode === 27) {
                this.hide();
            } else {
                this.request();
            }
        });

        const ul = this.dropdown.querySelector('ul.autosearch');
        if (ul) {
            ul.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && typeof this.click === 'function') {
                    this.click(e);
                }
            });
        }
    }

    show() {
        const topContainer = this.input.closest('.search-top');
        let voiceWidth = 0;

        if (topContainer) {
            const voiceSearch = topContainer.querySelector('.group_voice_search');
            if (voiceSearch) voiceWidth = voiceSearch.offsetWidth;

            const posTop = this.input.offsetTop;
            const posLeft = this.input.offsetLeft;

            this.dropdown.style.visibility = 'hidden';
            this.dropdown.style.display = 'block';

            const dropdownWidth = this.dropdown.offsetWidth;
            const left = posLeft + (this.input.offsetWidth / 2) - (dropdownWidth / 2) + (voiceWidth / 2);

            this.dropdown.style.top = `${posTop + this.input.offsetHeight}px`;
            this.dropdown.style.left = `${left}px`;

            this.dropdown.style.visibility = 'visible';
        } else {
            this.dropdown.style.display = 'block';
        }
    }

    hide() {
        if (this.dropdown) this.dropdown.style.display = 'none';
    }

    request() {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            if (typeof this.options.source === 'function') {
                this.options.source(this.input.value, (data) => this.response(data));
            }
        }, 300);
    }

    response(json) {
        let html = '';
        if (json.categories && json.categories.length > 0) {
            html += `
                <li class="search_categories">
                  <div class="search_categories_box">
                     <div class="search_categories_title">Категории</div>
                     <div class="search_category_items">
                        ${json.categories.map(cat => `
                            <a href="${cat.url}"><span>${cat.name}</span></a>
                        `).join('')}
                     </div>
                  </div>
                </li>`;
        }

        if (json.products && json.products.length > 0) {
            html += json.products.map(product => {
                const imageUrl = product.base_image ? product.base_image : '';
                const cleanName = product.name.replace(/<[^>]*>?/gm, '');

                return `
                    <li>
                        <a href="${product.url}" class="autosearch_link">
                            <div class="ajaxadvance">
                                <div class="image">
                                    ${imageUrl ? `<img title="${cleanName}" src="${imageUrl}" />` : ''}
                                </div>
                                <div class="content">
                                    <div class="search__left_block">
                                        <div class="name">${product.name}</div>
                                    </div>
                                    <div class="search__right_block">
                                        <div class="price">
                                            ${product.formatted_price}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>`;
            }).join('');
        }

        const ul = this.dropdown.querySelector('ul.autosearch');
        if (ul) {
            ul.innerHTML = html;
        }

        if (html) {
            this.show();
        } else {
            this.hide();
        }
    }

    initMobileScroll() {
        setTimeout(() => {
            const wrappers = document.querySelectorAll('.mobile-sidebar-search .autocomplete-wrapper');
            const content = document.querySelector('.mobile-sidebar-search__content');

            if (content) {
                wrappers.forEach(wrapper => {
                    wrapper.addEventListener('scroll', () => {
                        if (wrapper.scrollTop > 0) {
                            content.classList.add('active-shadow');
                        } else {
                            content.classList.remove('active-shadow');
                        }
                    });
                });
            }
        }, 200);
    }
}

// Мост совместимости со старым кодом
if (window.jQuery) {
    window.jQuery.fn.autocompleteSerach = function(options) {
        return this.each(function() {
            if (!this._autocompleteInstance) {
                this._autocompleteInstance = new AutocompleteSearch(this, options);
            }
        });
    };
}
