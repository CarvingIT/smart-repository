{{--Used on the index page (so shows a small summary--}}
{{--See the guide on binshops.com for how to copy these files to your /resources/views/ directory--}}

<!--
<div class="col-md-6">
    <div class="blog-item">

        <div class='text-center blog-image'>
            <?=$post->image_tag("thumbnail", true, ''); ?>
        </div>
        <div class="blog-inner-item">
            <h3 class=''><a href='{{$post->url($locale, $routeWithoutLocale)}}'>{{$post->title}}</a></h3>
            <h5 class=''>{{$post->subtitle}}</h5>

            @if (config('binshopsblog.show_full_text_at_list'))
                <p>{!! $post->post_body_output() !!}</p>
            @else
                <p>{!! mb_strimwidth($post->post_body_output(), 0, 400, "...") !!}</p>
            @endif

            <div class="post-details-bottom">
		@if(!empty($post->post->author->name))
                <span class="light-text">Authored by: </span> {{$post->post->author->name}} <span class="light-text">Posted at: </span> {{date('d M Y ', strtotime($post->post->posted_at))}}
                @endif
            </div>
            <div class='text-center'>
                <a href="{{$post->url($locale, $routeWithoutLocale)}}" class="btn btn-primary">View Post</a>
            </div>
        </div>
    </div>
</div>
-->
<div class="row">
<div class="col-md-12" style="border-bottom:1px solid #eee;">
	<br/>
            <h5 class=''><a href='{{$post->url($locale, $routeWithoutLocale)}}'>{{$post->title}}</a></h5>
		@if(!empty($post->post->author->name))
                <em>{{$post->post->author->name}}</em><br />
		@endif
		<p>{!! mb_strimwidth($post->post_body_output(), 0, 400, "...") !!}</p>
	<br/>
	<br/>
</div>
</div>
