@if ($subMenus->isNotEmpty())
    <div class="menu__body" {{ $active ? 'style="display: none;"' : '' }}>
        <ul>
            @foreach ($subMenus as $subMenu)
                <li>
                    <a href="{{ $subMenu->url() }}" target="{{ $subMenu->target() }}" title="{{ $subMenu->name() }}" class="submenu_link" style="color: rgb(38, 35, 38);">
                        {{ $subMenu->name() }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
