<div class='add_comment_area'>
    <h4>Add a comment</h4>
    <form method='post' action='{{route("binshopsblog.comments.add_new_comment",[app('request')->get('locale'),$post->slug])}}'>
        @csrf


        <div class='container-fluid' style="margin-top:0px;">
            <div class='row'>
            <div class='col'>
        	<div class="form-group ">

            <label id="comment_label" for="comment" style="color:#f05a22;">Your Comment </label>
                    <textarea
                            class="form-control"
                            name='comment'
                            required
                            id="comment"
                            placeholder="Write your comment here"
                            rows="3">{{old("comment")}}</textarea>
        	</div>
	    </div>
	    </div>

            <div class='row'>

                @if(config("binshopsblog.comments.save_user_id_if_logged_in", true) == false || !\Auth::check())

            <div class='row'>
                    <div class='col'>
                        <div class="form-group ">
                            <label id="author_name_label" for="author_name" style="color:#f05a22;">Your Name </label>
                            <input
                                    type='text'
                                    class="form-control"
                                    name='author_name'
                                    id="author_name"
                                    placeholder="Your name"
                                    required
                                    value="{{old("author_name")}}">
                        </div>
                    </div>
                    </div>
<br/>
                    @if(config("binshopsblog.comments.ask_for_author_email"))
            <div class='row'>
                        <div class='col'>
                            <div class="form-group">
                                <label id="author_email_label" for="author_email" style="color:#f05a22;">Your Email
                                    <small>(won't be displayed publicly)</small>
                                </label>
                                <input
                                        type='email'
                                        class="form-control"
                                        name='author_email'
                                        id="author_email"
                                        placeholder="Your Email"
                                        required
                                        value="{{old("author_email")}}">
                            </div>
                        </div>
                        </div>
                    @endif
                @endif

<br/>

                @if(config("binshopsblog.comments.ask_for_author_website"))
		<!--
                    <div class='col'>
                        <div class="form-group">
                            <label id="author_website_label" for="author_website">Your Website
                                <small>(Will be displayed)</small>
                            </label>
                            <input
                                    type='url'
                                    class="form-control"
                                    name='author_website'
                                    id="author_website"
                                    placeholder="Your Website URL"
                                    value="{{old("author_website")}}">
                        </div>
                    </div>
		-->
                @endif
            </div>
        </div>


        @if($captcha)
            {{--Captcha is enabled. Load the type class, and then include the view as defined in the captcha class --}}
            {{-- @include($captcha->view()) --}}
        @endif


        <div class="form-group ">
            <input type='submit' class="input-sm btn btn-success "
                   value='Add Comment'>
        </div>

    </form>
</div>
