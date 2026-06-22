<li class="{{ $item->getItemClass() ? $item->getItemClass() : 'nav-item' }}
    {{ $active ? 'active' : null }}
    {{ $item->hasItems() ? 'treeview' : null }}
">
        @if ($item->hasItems())
            <a class="nav-link menu-arrow" href="#{{ $item->getToggleIcon() }}" data-bs-toggle="collapse" role="button"
               aria-expanded="false" aria-controls="{{ $item->getToggleIcon() }}">
                 <span class="nav-icon">
                      <iconify-icon icon="solar:{{ $item->getIcon() }}"></iconify-icon>
                 </span>
                <span class="nav-text"> {{ $item->getName() }} </span>
            </a>
        @elseif(isset($item->isChild))
            <a class="sub-nav-link" href="{{ $item->getUrl() }}">{{ $item->getName() }}</a>
        @else
            <a href="{{ $item->getUrl() }}" class="{{ count($appends) > 0 ? 'hasAppend' : 'nav-link' }}"
               @if ($item->getNewTab())
                   target="_blank"
                @endif
            >
                <span class="nav-icon">
                  <iconify-icon icon="solar:{{ $item->getIcon() }}"></iconify-icon>
                </span>
                <span class="nav-text">{{ $item->getName() }}</span>
            </a>
        @endif


    @foreach ($appends as $append)
        {!! $append !!}
    @endforeach

    @if (count($items) > 0)
        <div class="collapse" id="{{ $item->getToggleIcon() }}">
            <ul class="nav sub-navbar-nav">
                @foreach ($items as $item)
                    {!! $item !!}
                @endforeach
            </ul>
        </div>
    @endif
</li>
