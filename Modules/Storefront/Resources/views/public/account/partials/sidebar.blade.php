<div id="chm-menu-account">
    <div class="chm-mod-account chm-list-group">
        <a
            href="{{ route('account.dashboard.index') }}"
            class="chm-list-group-item {{ request()->routeIs('account.dashboard.index') ? 'active' : '' }}"
        >
            <svg class="icon-am-person">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-person') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.my_account') }}
        </a>

        <a
            href="{{ route('account.profile.edit') }}"
            class="chm-list-group-item {{ request()->routeIs('account.profile.edit') ? 'active' : '' }}"
        >
            <svg class="icon-am-account-edit">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-account-edit') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.my_profile') }}
        </a>

        <a
            href="{{ route('account.profile.edit') }}#password"
            class="chm-list-group-item"
        >
            <svg class="icon-am-change-password">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-change-password') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.change_password') }}
        </a>

        <a
            href="{{ route('account.addresses.index') }}"
            class="chm-list-group-item {{ request()->routeIs('account.addresses.index') ? 'active' : '' }}"
        >
            <svg class="icon-am-address-book">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-address-book') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.my_addresses') }}
        </a>

        <a
            href="{{ route('account.wishlist.index') }}"
            class="chm-list-group-item {{ request()->routeIs('account.wishlist.index') ? 'active' : '' }}"
        >
            <svg class="icon-am-wishlist">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-wishlist') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.my_wishlist') }}
        </a>

        <a
            href="{{ route('account.orders.index') }}"
            class="chm-list-group-item {{ request()->routeIs('account.orders.index') || request()->routeIs('account.orders.show') ? 'active' : '' }}"
        >
            <svg class="icon-am-order-history">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-order-history') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.my_orders') }}
        </a>

        <a
            href="{{ route('account.reviews.index') }}"
            class="chm-list-group-item {{ request()->routeIs('account.reviews.index') ? 'active' : '' }}"
        >
            <svg class="icon-am-returns">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-returns') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.my_reviews') }}
        </a>

        <a href="{{ route('logout') }}" class="chm-list-group-item">
            <svg class="icon-am-logout">
                <use xlink:href="{{ asset('build/assets/img/sprite_am.svg#icon-am-logout') }}"></use>
            </svg>

            {{ trans('storefront::account.pages.logout') }}
        </a>
    </div>
</div>
