<nav class="menu-big">
    <div class="container">
        <div class="menu-big__top">
            <span></span>
            <button class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                    <defs>
                        <style>
                            .cls-1 {
                                fill: none;
                                stroke: #B1B1B1;
                                stroke-linecap: round;
                                stroke-linejoin: round;
                                stroke-width: 2px;
                            }
                        </style>
                    </defs>
                    <title></title>
                    <g id="cross">
                        <line class="cls-1" x1="7" x2="25" y1="7" y2="25"></line>
                        <line class="cls-1" x1="7" x2="25" y1="25" y2="7"></line>
                    </g>
                </svg>
            </button>
        </div>
        <ul>
            @foreach ($menu->menus() as $menu)
                <li style="background: {{ $loop->first ? 'color: rgb(255, 255, 255);' : 'transparent' }};">

                    <a href="{{ $menu->url() }}" class="menu_link" style="{{ $loop->first ? 'color: rgb(122, 76, 217);' : 'rgb(38, 35, 38)' }}" target="{{ $menu->target() }}">
                        {{ $menu->name() }}
                    </a>

                    @if ($menu->hasSubMenus())
                        @include('storefront::public.layouts.sidebar_menu.dropdown', [
                            'active' => $loop->first,
                            'subMenus' => $menu->subMenus()
                        ])
                    @endif

                </li>
            @endforeach

        </ul>
    </div>
</nav>
