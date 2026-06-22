@if (count($groups) > 1)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-justified">
                        @foreach ($groups as $group => $options)
                        <li class="nav-item">
                            <a href="#{{ $group }}" data-bs-toggle="tab" aria-expanded="false" class="nav-link {{ ($options['active'] ?? false) ? 'active' : '' }} {{ $tabs->group($group)->hasError() ? 'has-error' : '' }}">

                                <span class="d-block d-sm-none"><i class="bx bx-home"></i></span>
                                <span class="d-inline-block">{{ $options['title'] }}</span>
                                @if ($tabs->group($group)->hasError())
                                    <span class="d-inline-block"><i class="bx bx-error fs-18 align-middle me-1"></i></span>
                                @endif

                            </a>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content pt-2 text-muted">
                        @foreach ($groups as $group => $options)
                        <div class="tab-pane {{ ($options['active'] ?? false) ? 'show active' : '' }} " id="{{ $group }}">
                            <div class="row">
                                <div class="col-sm-3 mb-2 mb-sm-0">
                                    <div class="nav flex-column nav-pills" id="vl-pills-{{ $group }}" role="tablist" aria-orientation="vertical">
                                        {{ $tabs->group($group)->navs() }}
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="tab-content pt-0" id="vl-pills-{{ $group }}Content">
                                        {{ $tabs->renderGroupContent($group) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">

            <div class="tab-content pt-0" id="vl-pills-tabContent">


                <div class="p-3 bg-light mb-3 rounded">
                    <div class="row justify-content-end g-2">
                        @include('admin::form.footer')
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-xl-3 col-lg-4 mb-2 mb-sm-0" id="{{ $name }}">
            @foreach ($groups as $group => $options)
                <div class="card">
                    <div class="card-header">
                        <h4 class="panel-title">
                            <a
                                @if (count($groups) > 1)
                                    class="{{ ($options['active'] ?? false) ? '' : 'collapsed' }} {{ $tabs->group($group)->hasError() ? 'has-error' : '' }}"
                                data-toggle="collapse"
                                data-parent="#{{ $name }}"
                                href="#{{ $group }}"
                                @endif
                            >
                                {{ $options['title'] }}

                                @if ($tabs->group($group)->hasError())
                                    <i class="bx bx-alert-circle fs-18 align-middle me-1"></i>
                                    <i class="bx bx-error fs-18 align-middle me-1"></i>
                                @endif
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="nav flex-column nav-pills" id="vl-pills-tab" role="tablist" aria-orientation="vertical">
                            {{ $tabs->group($group)->navs() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-sm-9">

            <div class="tab-content pt-0" id="vl-pills-tabContent">
                {{ $contents }}

                <div class="p-3 bg-light mb-3 rounded">
                    <div class="row justify-content-end g-2">
                        @include('admin::form.footer')
                    </div>
                </div>
            </div>
        </div>

    </div>
@endif
