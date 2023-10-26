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
	</style>
@endpush
<section id="main">

    <div class="container">
        <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h6 class="card-title">{{ __('Feedback') }}</h6>
			</div>
			<div class="card-body">
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

				<form method="post" action="/feedback">
					@csrf
					<div class="ff">
						<label>Name <span class="required">*</span></label>
						<input type="text" name="name" value="{{ old('name') }}" />
					</div>
					<div class="ff">
						<label>Address <span class="required">*</span></label>
						<textarea name="address">{{ old('address') }}</textarea>
					</div>
					<div class="ff">
						<label>Email <span class="required">*</span></label>
						<input type="text" name="email" value="{{ old('email') }}" />
					</div>
					<div class="ff">
						<label>Subject <span class="required">*</span></label>
						<input type="text" name="subject" value="{{ old('subject') }}" />
					</div>
					<div class="ff">
						<label>Message <span class="required">*</span></label>
						<textarea name="message">{{ old('message') }}</textarea>
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
						<input type="submit" value="Submit" style="width:20%;"/>
					</div>
				</form>
              </div>
			</div>
			<!-- Content Box Ends--> 
            
          </div>
        </div>
    </div>
  </section> 


  @endsection
