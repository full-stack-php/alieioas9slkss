@foreach($products as $product)
    <div class="product-layout product-grid {{ $class }} px-2">
        @include('storefront::public.products.index.product_card')
    </div>
@endforeach
