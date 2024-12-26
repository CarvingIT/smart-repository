@extends('layouts.app',['title'=>'Feedback'])
@section('content')
@push('js')
	<style>
	.ff{
		margin-bottom:20px;
	}
	.BDC_CaptchaIconsDiv, .BDC_CaptchaImageDiv{
		display:inline-block;
	}
	.BDC_SoundIcon{
		display:none;
	}
	.alert-danger, .required{
		color:red;
	}

	form input, textarea{
		width:100%;
		
	}
	select{
		width: 100%;
		background-color: white;
		color: #f05a22 !important;
		font-size:14px !important;
	}
	label{
		color:#f05a22;
		font-size:15px !important;
	}
	</style>
@endpush
    <div class="container">
        <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h6 class="card-title">{{ __('FEEDBACK') }}</h6>
			</div>
			<div class="card-body">
			<div class="beautify">
				<br>
<!-- Content Box -->
                @if ($errors->any())
                <h4>Errors</h4>
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
				@endif
<br>
				<form method="post" action="/feedback">
					@csrf
					<div class="ff">
						<label>Name <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" value="{{ old('name') }}" />
					</div>
					<div class="ff">
						<label>Occupation <span class="required">*</span></label>
						<select name="Occupation" class="form-control"  id="Occupation">
							<option value="select"><span>Select Here <i class="bi bi-chevron-down dropdown-indicator"></i> </span></option>
							<option value="student">Student</option>
							<option value="freelancer">Freelancer</option>
							<option value="government">Government Officer</option>
							<option value="professionals">Professionals</option>
							<option value="entrepreneur">Entrepreneur</option>
						</select>
					</div>
					<div class="ff">
						<label>Email <span class="required">*</span></label>
						<input type="text" class="form-control"  name="email" value="{{ old('email') }}" />
					</div>
					<div class="ff">
						<label>Subject <span class="required">*</span></label>
						<input type="text" class="form-control"  name="subject" value="{{ old('subject') }}" />
					</div>
					<div class="ff">
						<label>Message <span class="required">*</span></label>
						<textarea name="message" class="form-control" >{{ old('message') }}</textarea>
					</div>
					<div class="ff">
					@php
						use Gregwar\Captcha\CaptchaBuilder;
						$builder = new CaptchaBuilder;
						$builder->build();
						session(['captcha_code' => $builder->getPhrase()]);
						//echo $builder->getPhrase();
					@endphp
					<img src="@php echo $builder->inline(); @endphp" />
                <input type="text" style="width:200px;" class="form-control" name="CaptchaCode" id="CaptchaCode" placeholder="Enter the code">
					</div>
					<div class="ff">
						<button type="submit" value="Submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
              </div>
			</div>
			</div>
			<!-- Content Box Ends--> 
            
          </div>
        </div>
    </div>
  @endsection
