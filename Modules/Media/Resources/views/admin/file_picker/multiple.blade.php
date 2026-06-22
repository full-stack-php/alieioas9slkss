<div class="multiple-files-wrapper">
    <h4>{{ $title }}</h4>

    @isset($description)
        <p class="text-muted mb-3">{{ $description }}</p>
    @endisset

    <button
        type="button"
        class="file-picker btn btn-default"
        data-input-name="{{ $inputName }}"
        data-multiple
    >
        <i class="fa fa-folder-open m-r-5"></i>{{ trans('media::media.browse') }}
    </button>

    <div class="multiple-files">
        <div class="col-md-12">
            <div class="row">
                <div class="file-list image-holder-wrapper clearfix">
                    @if ($files->isEmpty())
                        <div class="image-holder placeholder cursor-auto">
                            <i class="h1 fa fa-file-o"></i>
                        </div>
                    @else
                        @foreach ($files as $file)
                            <div class="image-holder file-holder">
                                @if ($file->isImage())
                                    <img src="{{ $file->path }}" alt="{{ $file->filename }}">
                                @else
                                    <i class="file-icon fa {{ $file->icon() }}"></i>
                                @endif

                                <div class="file-name small text-center mt-1">
                                    {{ $file->filename }}
                                </div>

                                <button type="button" class="btn remove-file">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M6.00098 17.9995L17.9999 6.00053" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17.9999 17.9995L6.00098 6.00055" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>

                                <input type="hidden" name="{{ $inputName }}" value="{{ $file->id }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
