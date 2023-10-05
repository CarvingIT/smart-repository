@if(\Auth::check() && 
	(\Auth::user()->hasRole('Principal') || 
	\Auth::user()->hasRole('Verifier') || 
	\Auth::user()->hasRole('admin')
	)
)
	<!--form method="post" action="/blog_admin/approve-post"-->
	<form method="post" action="/approvals/blog/{{ $post->id }}/save_status">
	@csrf
	<input type="hidden" name="approvable_id" value="{{ @$post->id }}" />
	<input type="hidden" name="slug" value="{{ @$post->slug }}" />
	<input type="hidden" name="approved_by" value="{{ auth()->user()->id }}" />
	<div class="form-group row">
		<div class="col-md-3">
		<label for="approved" class="col-md-12 col-form-label text-md-right">Approval Status</label>
                   </div>
                   <div class="col-md-9">

                   <select id="approval_status" name="approval_status" class="selectpicker" required>
                        <option value="">Select Status</option>
                        <option value="1">Approved</option>
                        <option value="0">Rejected</option>
                   </select>
                </div>
        </div>
	<div class="form-group row">
                <div class="col-md-3">
                   <label for="approved" class="col-md-12 col-form-label text-md-right">Approver Comments</label>
                   </div>
                   <div class="col-md-9">
                   <textarea class="form-control" id="approval_comment" name="comments"></textarea>
                   </div>
                </div>
    <input type="submit" class="btn btn-outline-secondary btn-sm pull-right float-right" value="Approve" />
	</form>
@endif
@if(\Auth::check() && \Auth::user()->canManageBinshopsBlogPosts())
    <a href="{{$post->edit_url()}}" class="btn btn-outline-secondary btn-sm pull-right float-right">Edit 
        Post</a>
@endif

<h3 class='blog_title'>{{$post->title}}</h3>
<h5 class='blog_subtitle'>{{$post->subtitle}}</h5>


<?=$post->image_tag("medium", false, 'd-block mx-auto'); ?>

<p class="blog_body_content">
    {!! $post->post_body_output() !!}

    {{--@if(config("binshopsblog.use_custom_view_files")  && $post->use_view_file)--}}
    {{--                                // use a custom blade file for the output of those blog post--}}
    {{--   @include("binshopsblog::partials.use_view_file")--}}
    {{--@else--}}
    {{--   {!! $post->post_body !!}        // unsafe, echoing the plain html/js--}}
    {{--   {{ $post->post_body }}          // for safe escaping --}}
    {{--@endif--}}
</p>

<hr/>

Posted <strong>{{$post->post->posted_at->diffForHumans()}}</strong>

@includeWhen($post->author,"binshopsblog::partials.author",['post'=>$post])
@includeWhen($categories,"binshopsblog::partials.categories",['categories'=>$categories])
