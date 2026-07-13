@if($productAvailability['is_preorder'])
    <div class="stock-status up-icon preorder">
        {{ trans('storefront::preorder.status') }}
    </div>
@elseif($productAvailability['is_discontinued'])
    <div class="stock-status up-icon discontinued">
        {{ trans('storefront::preorder.discontinued') }}
    </div>
@elseif($productAvailability['is_purchasable'])
    <div class="stock-status up-icon instock">
        {{ trans('storefront::product.in_stock') }}
    </div>
@else
    <div class="stock-status up-icon outofstock">
        {{ trans('storefront::product.out_of_stock') }}
    </div>
@endif
