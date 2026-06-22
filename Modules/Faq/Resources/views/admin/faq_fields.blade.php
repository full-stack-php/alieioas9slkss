<div id="faq-container" class="row">
    @php
        // Сначала проверяем старые данные из сессии (после ошибки валидации)
        // Если их нет, берем данные из сущности
        $oldFaqs = old('faqs');

        if ($oldFaqs) {
           $existingFaqs = collect($oldFaqs)->map(function($item) {
            return (object) $item;
        });
        } else {
            $existingFaqs = $entity->faqs ?? [];
        }
    @endphp

    @foreach ($existingFaqs as $index => $faq)
        {{-- Если в URL есть categories, меняем сетку динамически и в существующих --}}
        <div class="{{ str_contains(request()->url(), 'categories') ? 'col-md-12' : 'col-md-6' }}">
            @include('faq::admin.partials.faq_item', [
               'faq' => $faq,
               'faq_index' => $index,
               'is_new' => !isset($faq->id), // Элемент новый, если у него нет ID из базы
           ])
        </div>
    @endforeach

        <template id="faq-template">
            <div class="col-md-6 col-lg-6">
                @include('faq::admin.partials.faq_item', [
                    'faq' => null,
                    'faq_index' => '__FAQ_INDEX__',
                    'is_new' => true,
                ])
            </div>
        </template>
</div>


<button type="button" class="btn btn-sm btn-info" id="add-faq-btn">
    {{ trans('faq::faq.add_btn') }}
</button>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('faq-container');
            const template = document.getElementById('faq-template').innerHTML;
            const addButton = document.getElementById('add-faq-btn');
            const currentUrl = window.location.href;
            let containsCategories = currentUrl.includes('categories');

            window.faqIndex = {{ count($existingFaqs) }} + 1;

            addButton.addEventListener('click', () => {
                renderFaqItem(container, template, containsCategories);
            });

            // addButton.addEventListener('click', function() {
            //
            //     let newFaqHtml = template.replace(/__FAQ_INDEX__/g, window.faqIndex);
            //
            //     if(containsCategories){
            //         newFaqHtml = newFaqHtml.replace(/col-md-6 col-lg-6/g, 'col-md-12 col-lg-12');
            //     }
            //
            //     const tempDiv = document.createElement('div');
            //     tempDiv.innerHTML = newFaqHtml.trim();
            //     container.appendChild(tempDiv.firstChild);
            //
            //     const newTabs = container.querySelector(`#faq-item-${window.faqIndex} .nav-link.active`);
            //     if(newTabs) {
            //         newTabs.click();
            //     }
            //
            //     window.faqIndex++;
            // });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-faq-btn') || e.target.closest('.remove-faq-btn')) {
                    e.preventDefault();

                    const faqItem = e.target.closest('.faq-item');
                    if (faqItem) {
                        $(faqItem).parent().remove();
                    }
                }
            });

            function renderFaqItem(container, template, containsCategories) {
                let newFaqHtml = template.replace(/__FAQ_INDEX__/g, window.faqIndex);

                if (containsCategories) {
                    newFaqHtml = newFaqHtml.replace(/col-md-6 col-lg-6/g, 'col-md-12 col-lg-12');
                    newFaqHtml = newFaqHtml.replace(/rows="10"/g, 'rows="3"');
                }

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = newFaqHtml.trim();
                const newNode = tempDiv.firstChild;
                container.appendChild(newNode);

                const newTabs = newNode.querySelector(`.nav-link.active`);
                if (newTabs) {
                    newTabs.click();
                }

                const currentIndex = window.faqIndex;
                window.faqIndex++;

                return newNode; // Возвращаем узел, чтобы сразу наполнить его данными
            }
        });

        window.faqIndex = {{ count($existingFaqs) }};
    </script>
@endpush
