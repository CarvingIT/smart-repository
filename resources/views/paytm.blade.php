@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'Payment via PayTM'])
@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Payment via PayTM</h4></div>

                <div class="card-body">
            	<form action="{{ route('make.payment') }}" method="POST" enctype="multipart/form-data">
                {!! csrf_field() !!}

                @if($message = Session::get('message'))
                        <p>{!! $message !!}</p>
                    <?php Session::forget('success'); ?>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <strong>Name</strong>
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>
                    <div class="col-md-12">
                        <strong>Mobile No</strong>
                        <input type="text" name="mobile" class="form-control" maxlength="10" placeholder="Mobile No." required>
                    </div>
                    <div class="col-md-12">
                        <strong>Email</strong>
                        <input type="email" class="form-control" placeholder="Email" name="email" required>
                    </div>
                    <div class="col-md-12" >
                        <br/>
                        <div class="btn btn-info">
                            Term Fee : 1500 Rs/-
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br/>
                        <button type="submit" class="btn btn-success">Paytm</button>
                    </div>
                </div>
	            </form>
	
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
