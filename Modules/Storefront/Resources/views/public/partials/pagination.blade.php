@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Кнопка "Назад" --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            @if ($paginator->onFirstPage())
                <span class="page-link"><i class="las la-angle-left"></i></span>
            @else
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    <i class="las la-angle-left"></i>
                </a>
            @endif
        </li>

        {{-- Элементы пагинации --}}
        @foreach ($elements as $element)
            {{-- "Три точки" (...) --}}
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Массив ссылок --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Кнопка "Вперед" --}}
        <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    <i class="las la-angle-right"></i>
                </a>
            @else
                <span class="page-link"><i class="las la-angle-right"></i></span>
            @endif
        </li>
    </ul>
@endif
