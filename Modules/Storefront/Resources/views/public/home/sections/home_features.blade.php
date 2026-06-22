<div class="row">
    <div class="col-sm-12">
        <div class="container-module">
            <div class="row banner-blocks-container">
                @foreach ($features as $feature)
                    @if($feature->icon->path)
                    <div class="mt-10 mb-10 col-6 col-sm-6 col-md-4 col-lg-2">
                        <div class="banner-item dflex flex-column h-100 align-items-center is_a_link">
                            <div class="banner-image">
                                <img decoding="async" width="40" height="40" loading="lazy" src="{{ $feature->icon->path }}" alt="Бесплатная доставка" class="img-responsive">
                                <span class="bb-circle-color" style="background: #457DE3"></span>
                            </div>
                            <div class="banner-info dflex flex-column h-100 w-100">
                                <div class="banner-title">{{ $feature->title }}</div>
                                <div class="banner-description">{{ $feature->subtitle }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach


            </div>
        </div>
    </div>
</div>
