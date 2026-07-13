@php
    $level = $level ?? 0;
@endphp

@if ($level === 0 && empty($items))
    <div id="mm-mobile" class="mobile-menu-empty">
        {{ trans('storefront::layouts.mobile_menu_empty') }}
    </div>
@else
    <ul
        @if ($level === 0) id="mm-mobile" @endif
    class="m-mm-list"
        data-level="{{ $level }}"
    >
        @foreach ($items as $item)
            @php
                $hasChildren = !empty($item['children']);
                $nextLevel = $level + 1;
            @endphp

            <li class="m-mm-list-item d-flex align-items-center justify-content-between">
                <a
                    class="mm-a d-flex align-items-center"
                    href="{{ $item['url'] }}"
                    target="{{ $item['target'] }}"
                    title="{{ $item['name'] }}"
                >
                    @if (!empty($item['icon']))
                        <span class="menu-item-icon">
                            <i class="{{ $item['icon'] }}"></i>
                        </span>
                    @endif

                    <span>{{ $item['name'] }}</span>
                </a>

                @if ($hasChildren)
                    <button
                        class="show-sc-mobile go-2level"
                        type="button"
                        data-next-level="{{ $nextLevel }}"
                        aria-label="{{ trans('storefront::layouts.open_submenu') }}"
                    >
                        <svg class="icon icon-11">
                            <use xlink:href="#angel-right"></use>
                        </svg>
                    </button>

                    <div
                        class="d-none mob-second-level"
                        data-level="{{ $nextLevel }}"
                    >
                        <div class="mobm-top back-2level" data-back-level="{{ $level }}">
                            <span class="mm-icon-come-back">
                                <svg class="icon icon-22">
                                    <use xlink:href="#arrow-left"></use>
                                </svg>
                            </span>

                            <div class="mobm-title">
                                {{ $item['name'] }}
                            </div>

                            <button
                                type="button"
                                class="mobm-close-menu"
                                aria-label="{{ trans('storefront::layouts.close') }}"
                                data-toggle="close_mob_menu"
                            >
                                <svg class="icon icon-22">
                                    <use xlink:href="#cross"></use>
                                </svg>
                            </button>
                        </div>

                        @include('storefront::public.layouts.mobile_menu.items', [
                            'items' => $item['children'],
                            'level' => $nextLevel,
                        ])
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endif
