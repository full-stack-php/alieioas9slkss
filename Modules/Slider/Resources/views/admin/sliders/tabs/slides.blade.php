<div id="slides-wrapper"  class="accordion">
    {{-- Slides will be added here dynamically using JS --}}
</div>

<div class="form-group mt-3">
    <button type="button" class="add-slide btn btn-secondary btn-sm">
        {{ trans('slider::sliders.slide.add_slide') }}
    </button>
</div>

@include('slider::admin.sliders.templates.slide')

@push('globals')
    <script>
        Korf.data['slider.slides'] = {!! old_json('slides', $slider->slides) !!};
    </script>
@endpush
