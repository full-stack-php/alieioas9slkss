@php
    use Modules\Sticker\Entities\Sticker;

    $selectedType = old(
        'type',
        $sticker->type ?: Sticker::TYPE_LABEL
    );
@endphp

<div class="row">
    <div class="col-md-12">
        {{ Form::select(
            'type',
            trans('sticker::attributes.type'),
            $errors,
            trans('sticker::stickers.form.sticker_types'),
            $sticker,
            [
                'required' => true,
            ]
        ) }}

        {{ Form::number(
            'sort_order',
            trans('sticker::attributes.sort_order'),
            $errors,
            $sticker,
            [
                'min' => 0,
                'required' => true,
            ]
        ) }}

        {{ Form::checkbox(
            'is_active',
            trans('sticker::attributes.is_active'),
            trans('sticker::stickers.form.enable_the_sticker'),
            $errors,
            $sticker
        ) }}
    </div>
</div>

<hr>

<ul class="nav nav-pills">
    @foreach (supported_locales() as $locale => $language)
        <li class="nav-item">
            <a
                href="#stickerTranslationTab{{ $locale }}"
                data-bs-toggle="tab"
                aria-expanded="true"
                class="nav-link {{ $locale === locale() ? 'active' : '' }}"
            >
                <span class="d-block d-sm-none">
                    {{ strtoupper($locale) }}
                </span>

                <span class="d-none d-sm-block">
                    {{ $language['name'] }}
                </span>
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content pt-2 text-muted">
    @foreach (supported_locales() as $locale => $language)
        <div
            class="tab-pane {{ $locale === locale() ? 'show active' : '' }}"
            id="stickerTranslationTab{{ $locale }}"
        >
            {{ Form::text(
                $locale . '[name]',
                trans('sticker::attributes.name'),
                $errors,
                $sticker,
                [
                    'labelCol' => 2,
                    'required' => true,
                ]
            ) }}

            <div data-sticker-types="image,info">
                {{ Form::text(
                    $locale . '[image_alt]',
                    trans('sticker::attributes.image_alt'),
                    $errors,
                    $sticker,
                    [
                        'labelCol' => 2,
                        'required' => true,
                    ]
                ) }}
            </div>

            <div data-sticker-types="info">
                {{ Form::textarea(
                    $locale . '[description]',
                    trans('sticker::attributes.description'),
                    $errors,
                    $sticker,
                    [
                        'labelCol' => 2,
                        'rows' => 5,
                        'required' => true,
                    ]
                ) }}

                {{ Form::textarea(
                    $locale . '[popup_description]',
                    trans('sticker::attributes.popup_description'),
                    $errors,
                    $sticker,
                    [
                        'labelCol' => 2,
                        'rows' => 5,
                        'required' => true,
                    ]
                ) }}
            </div>
        </div>
    @endforeach
</div>

<hr>

<div class="row">
    <div class="col-md-12">
        <div data-sticker-types="label">
            {{ Form::color(
                'text_color',
                trans('sticker::attributes.text_color'),
                $errors,
                $sticker
            ) }}

            {{ Form::color(
                'background_color',
                trans('sticker::attributes.background_color'),
                $errors,
                $sticker
            ) }}
        </div>

        <div data-sticker-types="info">
            {{ Form::color(
                'image_background_color',
                trans('sticker::attributes.image_background_color'),
                $errors,
                $sticker
            ) }}
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        const stickerTypeInput = document.querySelector(
            '[name="type"]'
        );

        const stickerTypeElements = document.querySelectorAll(
            '[data-sticker-types]'
        );

        function updateStickerFields() {
            if (!stickerTypeInput) {
                return;
            }

            const selectedType = stickerTypeInput.value;

            stickerTypeElements.forEach((element) => {
                const availableTypes = element.dataset.stickerTypes
                    .split(',')
                    .map((type) => type.trim());

                element.classList.toggle(
                    'd-none',
                    !availableTypes.includes(selectedType)
                );
            });
        }

        if (stickerTypeInput) {
            stickerTypeInput.addEventListener(
                'change',
                updateStickerFields
            );

            updateStickerFields();
        }
    </script>
@endpush
