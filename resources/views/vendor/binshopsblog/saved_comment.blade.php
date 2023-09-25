@extends("layouts.app",['title'=>"Saved comment",'class'=>'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'Blog','titlePage'=>'Blog'])
@section("content")
<div class="container">
<div class="container-fluid">

	<div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Blog Comment</h4></div>

                <div class="card-body">

    <div class='text-center'>
        <h3>Thanks! Your comment has been saved!</h3>

        @if(!config("binshopsblog.comments.auto_approve_comments",false) )
            <p>After an admin user approves the comment, it'll appear on the site!</p>
        @endif

        <a href='{{$blog_post->url(app('request')->get('locale'))}}' class='btn btn-primary'>Back to blog post</a>
    </div>

</div>
</div>
</div>
</div>

@endsection
