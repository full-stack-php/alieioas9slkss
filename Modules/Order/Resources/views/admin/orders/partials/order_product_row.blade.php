<tr class="{{ $product->isChild() ? 'table-success' : '' }}">
    <td>
        <div class="d-flex align-items-center gap-2 {{ $product->isChild() ? 'ms-4' : '' }}">
            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                @if($product->product && $product->product->base_image->exists)
                    <img src="{{ $product->product->base_image->path }}"
                         alt="{{ $product->name }}"
                         class="avatar-md object-fit-cover">
                @else
                    <i class="bx bx-image fs-24 text-muted"></i>
                @endif
            </div>

            <div>
                @if($product->isGift())
                    <div class="text-success fw-semibold fs-13">
                        🎁 Подарок
                    </div>
                @endif

                @if ($product->trashed())
                    <span class="text-dark fw-medium fs-15">
                        {{ $product->isChild() ? '↳ ' : '' }}{{ $product->name }}
                    </span>
                @else
                    <a href="{{ route('admin.products.edit', $product->product->id) }}"
                       class="text-dark fw-medium fs-15">
                        {{ $product->isChild() ? '↳ ' : '' }}{{ $product->name }}
                    </a>
                @endif

                @if($product->hasPackaging())
                    <p class="text-muted mb-0 mt-1 fs-13">
                        Упаковка: {{ $product->packaging->name }} ({{ $product->packaging->qty }} шт.)
                    </p>
                @endif

                @if ($product->hasAnyOption())
                    <p class="text-muted mb-0 mt-1 fs-13">
                        @foreach ($product->options as $option)
                            <span>{{ $option->name }}: </span>

                            <span class="text-dark">
                                @if ($option->option->isFieldType())
                                    {{ $option->value }}
                                @else
                                    {{ $option->values->implode('label', ', ') }}
                                @endif
                            </span>

                            @if(!$loop->last)<br>@endif
                        @endforeach
                    </p>
                @endif
            </div>
        </div>
    </td>

    <td>{{ $product->unit_price->format() }}</td>
    <td>{{ $product->qty }}</td>
    <td class="fw-medium text-dark">{{ $product->line_total->format() }}</td>
</tr>
