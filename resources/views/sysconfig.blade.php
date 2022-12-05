@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'System Config','title'=>'Smart Repository'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">System Configuration</div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <div class="alert alert-<?php echo $msg; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                        </button>
                        <span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>
                        @endif
                    @endforeach

                   <form method="post" action="/admin/sysconfig" enctype="multipart/form-data">
                    @csrf()
                   <div class="form-group row">
                    <div class="col-md-12">
                        <h4>Site Configuration</h4>
		            </div>
                   </div>
		  
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="logo_url" class="col-md-12 col-form-label text-md-right">Logo URL</label> 
		           </div>
                    <div class="col-md-9">
                    <!--input type="text" name="logo_url" id="logo_url" class="form-control" placeholder="http://domain.com/i/logo.png" value="@if(!empty($sysconfig['logo_url'])) {{$sysconfig['logo_url'] }} @endif" /-->
                   <label for='filesize'><font color="red">File size must be less than {{ $size_limit }}B.</font></label>
                   <input id="logo_url" type="file" class="form-control-file" name="logo_url">
                    </div>
                   </div>

                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="favicon_url" class="col-md-12 col-form-label text-md-right">Favicon URL</label> 
		           </div>
                    <div class="col-md-9">
                    <!--input type="text" name="favicon_url" id="favicon_url" class="form-control" placeholder="http://domain.com/i/logo.png" value="@if(!empty($sysconfig['favicon_url'])) {{$sysconfig['favicon_url'] }} @endif" /-->
                   <label for='filesize'><font color="red">File size must be less than 50KB.</font></label>
                   <input id="favicon_url" type="file" class="form-control-file" name="favicon_url">
                    </div>
                   </div>

		   <div class="form-group row">
                    <div class="col-md-3">
                   <label for="driver" class="col-md-12 col-form-label text-md-right">Media Disk Storage</label>
                    </div>
                    <div class="col-md-9">
					<select name="media_storage_drive" class="selectpicker">
							<option value="">Select</option>
                       		@foreach($storage_disks as $disk => $type)
                       			@if(env('ENABLE_LOCAL_STORAGE') != 1 && $disk == 'local')
                             		@continue
                           		@endif
                           		<option value="{{ $disk }}" @if(@$sysconfig['media_storage_drive'] == $disk) selected @endif>{{ $disk }}</option>
                       		@endforeach
               		</select>
                    </div>
                   </div>


                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="home_page" class="col-md-12 col-form-label text-md-right">Home page info</label> 
		           </div>
                    <div class="col-md-9">
                    <textarea name="home_page" id="home_page" class="page_content">@if(!empty($sysconfig['home_page'])) {{$sysconfig['home_page'] }} @endif</textarea>
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="contact_page" class="col-md-12 col-form-label text-md-right">Contact info</label> 
		           </div>
                    <div class="col-md-9">
                    <textarea name="contact_page" id="logo_url" class="page_content">@if(!empty($sysconfig['contact_page'])) {{$sysconfig['contact_page'] }} @endif</textarea>
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="overridingcss" class="col-md-12 col-form-label text-md-right">Overriding CSS</label> 
		           </div>
                    <div class="col-md-9">
                    <textarea name="overridingcss" id="overridingcss">@if(!empty($sysconfig['overridingcss'])) {{$sysconfig['overridingcss'] }} @endif</textarea>
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="bg_image" class="col-md-12 col-form-label text-md-right">Background Image</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="bg_image" id="bg_image" class="form-control" placeholder="http://domain.com/i/logo.png" value="@if(!empty($sysconfig['bg_image'])) {{$sysconfig['bg_image'] }} @endif" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="banner_image_1" class="col-md-12 col-form-label text-md-right">Banner Image 1</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="banner_image_1" id="banner_image_1" class="form-control" placeholder="http://domain.com/i/logo.png" value="@if(!empty($sysconfig['banner_image_1'])) {{$sysconfig['banner_image_1'] }} @endif" />
                    </div>
                   </div>
                   <div class="form-group row mb-0"><div class="col-md-12 offset-md-4"><button type="submit" class="btn btn-primary">
                                    Save
                                </button> 
                     </div></div> 
                   </form> 
                </div>
            </div>
            
        </div>
    </div>
</div>
</div>

                        <script src="/js/tinymce/tinymce.min.js"></script>
                        <script>
                            tinymce.init({
                                selector: '.page_content',
                                plugins: [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                                    "insertdatetime media table nonbreaking save contextmenu directionality paste"
                                ],
                                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                                relative_urls: true,
                                remove_script_host: false,
                                convert_urls: true,
                                force_br_newlines: true,
                                force_p_newlines: false,
                forced_root_block: '', // Needed for 3.x
				/*
                  file_picker_callback (callback, value, meta) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
        let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight

        tinymce.activeEditor.windowManager.openUrl({
          url : '/file-manager/tinymce5',
          title : 'Laravel File manager',
          width : x * 0.8,
          height : y * 0.8,
          onMessage: (api, message) => {
            callback(message.content, { text: message.text })
          }
        })
      }, */
                            });</script>

@endsection
