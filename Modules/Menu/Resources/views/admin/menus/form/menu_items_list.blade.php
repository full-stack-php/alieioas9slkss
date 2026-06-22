<ol class="dd-list">
    @foreach ($menuItems as $menuItem)
        <li class="dd-item" data-id="{{ $menuItem->id }}">
            @if (!$menuItem->is_root)
                <div class="menu-item-actions btn-group" role="group">
                    <a href="{{ route('admin.menus.items.edit', [$menu->id, $menuItem->id]) }}" class="btn edit-menu-item ">
                        <iconify-icon icon="solar:settings-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    </a>

                    <button type="button" class="btn delete-menu-item" data-action="{{ route('admin.menus.items.destroy', [$menu->id, $menuItem->id]) }}">
                        <iconify-icon icon="solar:trash-bin-2-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    </button>
                </div>
            @endif

            <div class="{{ $menuItem->is_root ? 'dd-handle-root' : 'dd-handle' }} text-primary mb-2">{{ $menuItem->name }}</div>

            @if (count($menuItem->items) !== 0)
                @include('menu::admin.menus.form.menu_items_list', ['menuItems' => $menuItem->items])
            @endif
        </li>
    @endforeach
</ol>
