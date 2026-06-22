<div class="tab-pane" id="tab-question-answer">
    <div id="question-answer">
        <h2 class="ch-h2">{{ trans('storefront::product.qa.heading_qa') }}&nbsp;{{ $product->h1_name?? $product->name }}</h2>
        <button type="button" class="chm-btn chm-btn-black chm-px-lg chm-lg chm-lg-rounded xs-w-100 sm-w-auto" data-bs-toggle="modal" data-bs-target="#ch-modal-question-answer">{{ trans('storefront::product.qa.ask_btn') }}</button>

        <div class="box-question-answer">
            @forelse($product->questionsanswers()->where('is_approved', 1)->paginate(5) as $qa)
                <div class="qa-item">
                    <div class="info-client d-flex align-items-center">
                        <div class="client-name">
                                {{ $qa->asker_name }}
                        </div>
                        <div class="client-date-added">{{ $qa->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="question-answer">
                        <div class="question-client">
                            {{ $qa->question }}
                        </div>
                        @if($qa->answer)
                            <div class="answer-admin mt-3 p-3" style="background-color: var(--color-gray-light-1); border-radius: 6px; border-left: 3px solid var( --color-primary-light-2, #007bff);">
                                <div class="font-weight-bold mb-1" style="font-size: 0.9em; opacity: 0.8;">
                                    {{ trans('storefront::product.qa.admin_reply') ?? 'Ответ магазина:' }}
                                </div>
                                <div class="answer-text">
                                    {{ $qa->answer }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-left mt-20">{{ trans('storefront::product.qa.be_the_first_one_to_ask') }}</div>
            @endforelse
            @if($product->questionsanswers()->where('is_approved', 1)->count() > 5)
                <div class="pagination-wrapper mt-4 text-center">
                    {{ $product->questionsanswers()->where('is_approved', 1)->paginate(5)->links('storefront::public.partials.pagination') }}
                </div>
            @endif
        </div>

        <div class="modal fade" id="ch-modal-question-answer" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog chm-modal sm-modal-4 modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <div class="modal-title">{{ trans('storefront::product.qa.ask_question') }}</div>
                        <button type="button" class="close-modal" data-bs-dismiss="modal">
                            <svg class="icon icon-11">
                                <use xlink:href="#cross"></use>
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="form-question-answer" action="{{ route('products.questions.store', $product->id) }}" method="POST">
                            @csrf

                            <div class="form-group required_field @error('asker_name') has-error @enderror">
                                <input id="contact-name" class="form-control contact-name" type="text"
                                       placeholder="{{ trans('storefront::product.qa.name') }}"
                                       value="{{ old('asker_name', auth()->check() ? auth()->user()->getFullNameAttribute() : '') }}"
                                       name="asker_name" {{ auth()->check() ? 'readonly' : '' }}>
                                @error('asker_name')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group required_field @error('asker_phone') has-error @enderror">
                                <input id="contact-telephone" class="form-control contact-telephone" type="text"
                                       placeholder="+38(___) ___-__-__"
                                       value="{{ old('asker_phone', auth()->check() ? auth()->user()->phone ?? '' : '') }}"
                                       name="asker_phone">
                                @error('asker_phone')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group required_field @error('question') has-error @enderror">
                        <textarea name="question" rows="5"
                                  placeholder="{{ trans('storefront::product.qa.your_question') }}"
                                  id="input-comment-field" class="form-control">{{ old('question') }}</textarea>
                                @error('question')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group help-block">
                                {!! trans('storefront::product.qa.note') !!}
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" form="form-question-answer" class="chm-btn chm-btn-primary chm-px-lg chm-lg-rounded w-100" id="button-question-answer" data-loading-text="Загрузка...">
                            {{ trans('storefront::product.qa.send') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
