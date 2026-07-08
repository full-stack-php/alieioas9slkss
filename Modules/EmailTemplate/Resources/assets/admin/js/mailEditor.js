const typeSelect = document.querySelector('select[name="type"]');
const shortcodesContainer = document.getElementById('email-template-shortcodes');

if (typeSelect && shortcodesContainer) {
    const shortcodesByType = JSON.parse(shortcodesContainer.dataset.shortcodesByType || '{}');
    const emptyText = shortcodesContainer.dataset.emptyText || '';

    const renderShortcodes = () => {
        const selectedType = typeSelect.value;
        const shortcodes = shortcodesByType[selectedType] || [];

        shortcodesContainer.innerHTML = '';

        if (!shortcodes.length) {
            shortcodesContainer.textContent = emptyText;

            return;
        }

        shortcodes.forEach((shortcode) => {
            const code = document.createElement('code');

            code.classList.add('me-2');
            code.textContent = shortcode;

            shortcodesContainer.appendChild(code);
        });
    };

    typeSelect.addEventListener('change', renderShortcodes);

    renderShortcodes();
}

const testEmailInput = document.getElementById('email-template-test-email');
const testButton = document.getElementById('email-template-send-test');
const testMessage = document.getElementById('email-template-test-message');

if (testEmailInput && testButton && testMessage) {
    const form = document.getElementById('email-template-create-form')
        || document.getElementById('email-template-edit-form');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const showTestMessage = (message, type = 'success') => {
        testMessage.className = `mt-2 alert alert-${type}`;
        testMessage.textContent = message;
    };

    testButton.addEventListener('click', async () => {
        if (!form) {
            return;
        }

        if (window.tinymce) {
            window.tinymce.triggerSave();
        }

        if (window.tinyMCE) {
            window.tinyMCE.triggerSave();
        }

        const defaultText = testButton.dataset.defaultText;
        const sendingText = testButton.dataset.sendingText;
        const errorText = testButton.dataset.errorText;

        testButton.disabled = true;
        testButton.textContent = sendingText;
        testMessage.className = 'mt-2';
        testMessage.textContent = '';

        const formData = new FormData(form);

        formData.delete('_method');
        formData.set('test_email', testEmailInput.value);

        try {
            const response = await fetch(testButton.dataset.url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                const validationMessage = data.errors
                    ? Object.values(data.errors).flat()[0]
                    : data.message;

                showTestMessage(validationMessage || errorText, 'danger');

                return;
            }

            showTestMessage(data.message, 'success');
        } catch (error) {
            showTestMessage(errorText, 'danger');
        } finally {
            testButton.disabled = false;
            testButton.textContent = defaultText;
        }
    });
}
