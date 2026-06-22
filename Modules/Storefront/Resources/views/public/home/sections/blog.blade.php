<div class="row">
    <div class="col-sm-12">
        <div class="container-module module-articles latest-atricles">
            <div class="title-module rc-mod"><span> {{ setting("storefront_blogs_section_title") }}</span></div>
            <div class="module-articles__list row-flex">
                @foreach($blog['blogPosts'] as $blogPost)
                    @include('storefront::public.partials.blog_post_card', $blogPost)
                @endforeach
            </div>
        </div>
    </div>
</div>
