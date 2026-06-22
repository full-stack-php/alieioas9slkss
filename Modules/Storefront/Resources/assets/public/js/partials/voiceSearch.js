export function initVoiceSearch() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        document.querySelectorAll('.group_voice_search').forEach(el => el.classList.add('d-none'));
        return;
    }

    document.querySelectorAll('.group_voice_search').forEach(el => el.classList.remove('d-none'));

    const htmlLang = document.documentElement.lang;
    let recognition_lang = 'ru-RU';

    if (htmlLang) {
        if (htmlLang.includes('-')) {
            const parts = htmlLang.split('-');
            recognition_lang = `${parts[0].toLowerCase()}-${parts[1].toUpperCase()}`;
        } else {
            const langMap = {
                'ru': 'ru-RU',
                'uk': 'uk-UA',
                'en': 'en-US',
                'pl': 'pl-PL'
            };
            recognition_lang = langMap[htmlLang.toLowerCase()] || htmlLang;
        }
    }

    let recognizing = false;
    let timeout;
    let search_start = false;
    let currentContainer = null;
    let searchInput = null;

    const recognition = new SpeechRecognition();
    recognition.interimResults = true;
    recognition.continuous = true;
    recognition.lang = recognition_lang;

    recognition.onstart = function() {
        recognizing = true;
        startTimeout();
    };

    recognition.onerror = function(event) {
        console.log('Voice Recognition Error:', event.error);
    };

    recognition.onend = function() {
        recognizing = false;
        clearTimeout(timeout);
    };

    recognition.onresult = function(event) {
        search_start = false;
        let interim_transcript = '';
        let final_transcript = '';

        for (let i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
                final_transcript += event.results[i][0].transcript + ' ';
            } else {
                interim_transcript += event.results[i][0].transcript;
            }
        }

        final_transcript = final_transcript.trim();
        interim_transcript = interim_transcript.trim();

        if (searchInput) {
            searchInput.value = final_transcript ? final_transcript : interim_transcript;
        }

        if (event.results[event.resultIndex].isFinal) {
            search_start = true;

            setTimeout(() => {
                if (search_start && searchInput) {
                    if (window.jQuery && $(searchInput).autocompleteSerach) {
                        $(searchInput).autocompleteSerach({ source: window.getAjaxLiveSearch });
                    }
                    searchInput.focus();
                }
            }, 200);
            resetTimeout();
        }
    };

    recognition.onaudiostart = function() {
        document.querySelectorAll('.btn-voice-search').forEach(btn => btn.classList.add('active-speak'));
        addDots();
    };

    recognition.onaudioend = function() {
        document.querySelectorAll('.btn-voice-search').forEach(btn => btn.classList.remove('active-speak'));
        document.querySelectorAll('.search-voice__dots').forEach(dot => dot.remove());
    };

    function addDots() {
        const dotsHTML = '<div class="search-voice__dots"><span class="search-voice__dots-item search-voice__dots-item_color_blue"></span><span class="search-voice__dots-item search-voice__dots-item_color_red"></span><span class="search-voice__dots-item search-voice__dots-item_color_orange"></span><span class="search-voice__dots-item search-voice__dots-item_color_green"></span></div>';

        document.querySelectorAll('.btn-voice-search').forEach(btn => {
            if (btn.offsetWidth > 0 || btn.offsetHeight > 0) {
                btn.insertAdjacentHTML('beforeend', dotsHTML);
            }
        });
    }

    function startTimeout() {
        timeout = setTimeout(() => {
            if (recognizing) recognition.stop();
        }, 30000);
    }

    function resetTimeout() {
        clearTimeout(timeout);
        startTimeout();
    }

    // --- Делегирование событий клика ---
    document.addEventListener('click', (e) => {
        const voiceBtn = e.target.closest('.btn-voice-search');

        if (voiceBtn) {
            currentContainer = voiceBtn.closest('.header-search');
            searchInput = currentContainer ? currentContainer.querySelector("input[name='search']") : null;

            // Если мы УЖЕ знаем, что слушаем — останавливаем
            if (recognizing) {
                recognition.stop();
                return;
            }

            if (searchInput) searchInput.value = '';

            // Защита от "InvalidStateError" при двойном клике
            try {
                recognition.start();
            } catch (err) {
                if (err.name === 'InvalidStateError') {
                    console.warn('Микрофон уже инициализируется, игнорируем двойной клик.');
                } else {
                    console.error('Ошибка Web Speech API:', err);
                }
            }
        }
    });
}
