<div class="row">
    <div class="col-sm-12">
        <div class="banner-pro">
            <div class="row-flex">
            @for ($i = 1; $i <= 4; $i++)
                @if($threeColumnBanners['banner_' . $i]->image->path)
                <div class="banner-pro__column banner-pro__column--col-3 mb-10 mt-10">
                    <div class="banner-pro__item banner-pro__item--00 dflex flex-column flex-grow-1"
                         style="background-color: #B4CBEC" onclick="{{ $threeColumnBanners['banner_' . $i]->call_to_action_url }}">
                        <div class="banner-pro__content">
                            <div class="banner-pro__title"
                                 style="color: #FFFFFF">
                                {{ $threeColumnBanners['banner_' . $i]->title }}
                            </div>
                            <div class="banner-pro__description"
                                 style="color: #FEFEFE">
                                {{ $threeColumnBanners['banner_' . $i]->subtitle }}
                            </div>
                        </div>
                        <div class="banner-pro__image">
                            <img class="banner-pro__img_cover img-responsive" loading="lazy" width="347" height="545" src="{{ $threeColumnBanners['banner_' . $i]->image->path }}" alt="{{ $threeColumnBanners['banner_' . $i]->title }}"/>
                        </div>
                    </div>
                </div>
                @endif
            @endfor
            </div>
        </div>
    </div>
</div>
