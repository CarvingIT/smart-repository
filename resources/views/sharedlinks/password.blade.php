@extends('layouts.app', ['class' => 'bg-default'])<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@section('content')<head>

<div class="header bg-gradient-primary py-7 py-lg-8">    <meta charset="utf-8">

    <div class="container">    <meta name="viewport" content="width=device-width, initial-scale=1">

        <div class="header-body text-center mb-7">    <title>{{ config('app.name', 'Laravel') }} - Password Required</title>

            <div class="row justify-content-center">    <link rel="stylesheet" type="text/css" href="{{ asset('css/material-dashboard.css') }}">

                <div class="col-lg-5 col-md-6">    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">

                    <h1 class="text-white">Password Protected</h1></head>

                </div><body class="login-page sidebar-collapse">

            </div>    <div class="page-header header-filter" style="background-color: #9c27b0; background-size: cover; background-position: top center;">

        </div>        <div class="container">

    </div>            <div class="row">

    <div class="separator separator-bottom separator-skew zindex-100">                <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">

        <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">                    <div class="card card-login">

            <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>                        <div class="card-header card-header-primary text-center">

        </svg>                            <h4 class="card-title">Password Required</h4>

    </div>                            <div class="social-line">

</div>                                <i class="material-icons">lock</i>

                            </div>

<div class="container mt--8 pb-5">                        </div>

    <div class="row justify-content-center">                        <div class="card-body">

        <div class="col-lg-5 col-md-7">                            <p class="card-description text-center">

            <div class="card bg-secondary shadow border-0">                                This shared document is password protected. Please enter the password to continue.

                <div class="card-body px-lg-5 py-lg-5">                            </p>

                    <div class="text-center text-muted mb-4">                            

                        <small>Enter password to view the document</small>                            @if (session('error'))

                    </div>                                <div class="alert alert-danger text-center">

                    <form role="form" method="POST" action="{{ route('shared-links.public-view', $sharedLink->token) }}">                                    {{ session('error') }}

                        @csrf                                </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">                            @endif

                            <div class="input-group input-group-alternative">

                                <div class="input-group-prepend">                            @if ($errors->any())

                                    <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>                                <div class="alert alert-danger">

                                </div>                                    @foreach ($errors->all() as $error)

                                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" type="password" name="password" required>                                        <p class="mb-0">{{ $error }}</p>

                            </div>                                    @endforeach

                            @if ($errors->has('password'))                                </div>

                                <span class="invalid-feedback" style="display: block;" role="alert">                            @endif

                                    <strong>{{ $errors->first('password') }}</strong>

                                </span>                            <form class="form" method="POST" action="{{ route('shared-links.password.check', $sharedLink->token) }}">

                            @endif                                @csrf

                        </div>                                <div class="card-body">

                        <div class="text-center">                                    <div class="input-group">

                            <button type="submit" class="btn btn-primary my-4">View Document</button>                                        <div class="input-group-prepend">

                        </div>                                            <span class="input-group-text">

                    </form>                                                <i class="material-icons">lock_outline</i>

                </div>                                            </span>

            </div>                                        </div>

        </div>                                        <input type="password" 

    </div>                                               name="password" 

</div>                                               class="form-control" 

@endsection                                               placeholder="Enter password..." 

                                               required 
                                               autofocus>
                                    </div>
                                </div>
                                <div class="footer text-center">
                                    <button type="submit" class="btn btn-primary btn-link btn-wd btn-lg">
                                        Access Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <div class="copyright text-center">
                    &copy; {{ date('Y') }}, made with <i class="material-icons">favorite</i> by
                    <a href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>
                </div>
            </div>
        </footer>
    </div>
</body>

<!--   Core JS Files   -->
<script src="{{ asset('js/core/jquery.min.js') }}"></script>
<script src="{{ asset('js/core/popper.min.js') }}"></script>
<script src="{{ asset('js/core/bootstrap-material-design.min.js') }}"></script>
<script src="{{ asset('js/material-dashboard.js') }}"></script>

</html>
