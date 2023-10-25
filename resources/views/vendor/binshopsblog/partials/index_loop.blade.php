{{--Used on the index page (so shows a small summary--}}
{{--See the guide on binshops.com for how to copy these files to your /resources/views/ directory--}}

<!--div class="row"-->
<div class="col-md-12" style="border-bottom:1px solid #eee; padding-bottom:1%; padding-top:1%;">
            <h3 class=''><i class="fa-solid fa-blog" style="color:#f05a22"></i>
		&nbsp;{{$post->title}}</h3>
		@if(!empty($post->post->author->name))
                <em>{{$post->post->author->name}}</em>
		@endif
		<p>{!! mb_strimwidth($post->post_body_output(), 0, 400, "...") !!}</p>
		<br />
		<span style="float:right;"><strong><a href='{{$post->url($locale, $routeWithoutLocale)}}'>More >></a></strong></span>
</div>
<!--/div-->
