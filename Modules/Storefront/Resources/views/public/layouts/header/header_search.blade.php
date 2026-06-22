<div class="box-search flex-grow-sm-1">
    <button aria-label="Search" type="button" class="btn-open-search d-flex align-items-center justify-content-center d-sm-none">
        <svg class="icon icon-22">
            <use xlink:href="#serach"></use>
        </svg>
    </button>
    <div class="search-top d-none d-sm-block">
        <div class="header-search input-group align-items-center livesearch">
            <div class="input-group-btn categories">
                <button aria-label="Search category" type="button" data-bs-toggle="dropdown" data-placement="left" title="{{ trans("storefront::layouts.all_categories") }}" class="btn-search-select dropdown-toggle">
                    <svg class="icon icon-22">
                        <use xlink:href="#settings"></use>
                    </svg>
                </button>

                <ul class="dropdown-menu dropdown-menu-left ch-dropdown">
                    <li class="sel-cat-search"><a href="#" onclick="return false;" data-idsearch="0">{{ trans("storefront::layouts.all_categories") }}</a></li>

                    @foreach($categories as $category)
                        <li>
                            <a href="#" onclick="return false;" data-idsearch="{{ $category['id'] }}">{{ $category['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
                <input type="hidden" name="search_category_id" value="0">
            </div>
            <input type="text" name="search" value="" placeholder="{{ trans('storefront::layouts.search_for_products') }}" class="form-control search-autocomplete">
            <span class="input-group-btn group_voice_search">
                <button type="button" class="btn btn-voice-search" aria-label="Search">
                     <svg class="icon icon-22 up-icon-microphone">
                        <use xlink:href="#speak"></use>
                     </svg>
                </button>
            </span>

            <span class="input-group-btn button_search">
                <button type="button" class="btn btn-search" aria-label="Search">
                     <svg class="icon icon-22">
                        <use xlink:href="#serach"></use>
                    </svg>
                </button>
            </span>
        </div>
    </div>
</div>

