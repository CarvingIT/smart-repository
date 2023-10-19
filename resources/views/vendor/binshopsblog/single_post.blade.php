@extends("layouts.app",['title'=>$post->gen_seo_title(),'class'=>'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'Blog','titlePage'=>'Blog'])

@section('blog-custom-css')
    <link type="text/css" href="{{ asset('binshops-blog.css') }}" rel="stylesheet">
@endsection

@section("content")

<div class="container">
<div class="container-fluid">

    @if(config("binshopsblog.reading_progress_bar"))
        <div id="scrollbar">
            <div id="scrollbar-bg"></div>
        </div>
    @endif

    {{--https://github.com/binshops/laravel-blog--}}
<div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Blog</h4></div>

        <div class="card-body">
    <div class='container'>
    <div class='row'>
        <div class='col-sm-12 col-md-12 col-lg-12'>

            @include("binshopsblog::partials.show_errors")
            @include("binshopsblog::partials.full_post_details")

<hr />
            @if(config("binshopsblog.comments.type_of_comments_to_show","built_in") !== 'disabled')
                <div class="" id='maincommentscontainer'>
                    <h3 id='binshopsblogcomments'>Comments</h3>
                    @include("binshopsblog::partials.show_comments")
                </div>
            @else
                {{--Comments are disabled--}}
            @endif


        </div>
    </div>
    </div>
</div>
</div>
</div>
</div>
</div>

@endsection

@section('blog-custom-js')
    <script src="{{asset('binshops-blog.js')}}"></script>
@endsection
