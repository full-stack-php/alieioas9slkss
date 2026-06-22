<ul id="menu-glavnoe" class="menu_main flex-grow-1 justify-content-between align-items-stretch">
    @foreach ($menu->menus() as $menu)
    <li class="menu-item ">
        <a href="{{ $menu->url() }}" target="{{ $menu->target() }}">{{ $menu->name() }}</a>
    </li>
    @endforeach
    @if($repeat_btn)
    <li class="menu-item ">
        <button class="btn btn-primary btn-accent btn-lg w-100" type="button">
            {{ trans('storefront::layouts.repeat_latest_order_btn') }}
        </button>
    </li>
    @endif
</ul>
