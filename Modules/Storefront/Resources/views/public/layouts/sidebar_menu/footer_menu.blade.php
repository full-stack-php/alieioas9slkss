<ul class="list-unstyled">
    @foreach ($menu as $menu)
        <li><a target="{{ $menu->target }}" href="{{ $menu->url() }}">{{ $menu->name }}</a></li>
    @endforeach
</ul>
