<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Blog Admin - {{ config('app.name', 'Laravel') }}</title>


    <!-- jQuery is only used for hide(), show() and slideDown(). All other features use vanilla JS -->
    <script src="/js/node/jquery.min.js"></script>

    <!-- Font Awesome - served locally via npm (@fortawesome/fontawesome-free) -->
    <link href="/vendor/font-awesome/css/all.min.css" rel="stylesheet">

    <!-- Nunito font - served locally via npm (@fontsource/nunito) -->
    <link rel="stylesheet" href="/vendor/fontsource/nunito/400.css">
    <link rel="stylesheet" href="/vendor/fontsource/nunito/700.css">

    <!-- Styles -->
    {{--    @if(file_exists(public_path("binshopsblog_admin_css.css")))--}}
    <link href="{{ asset('binshopsblog_admin_css.css') }}" rel="stylesheet">
    {{--    @else--}}
    {{--        <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    {{--Edited your css/app.css file? Uncomment these lines to use plain bootstrap:--}}
    {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">--}}
    {{--    @endif--}}


</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand binshops-blog-title" href="{{ route('binshopsblog.admin.index') }}">
                {{ config('app.name', 'Laravel') }} Blog Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->

                    <li class='nav-item px-2'><a class='nav-link' href='{{route("binshopsblog.index" , app('request')->get('locale'))}}' target="_blank">Blog home</a></li>

                    <li class="nav-item ">
                        <a id="" class="nav-link " href="#" role="button"
                           aria-haspopup="true" aria-expanded="false" >
                            Logged in as {{ Auth::user()->name }}
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class='row full-width-div nav-bar-full'>
                <div class='list-group-color'>
                    @include("binshopsblog_admin::layouts.sidebar")
					<!--
                    <div class=' list-group-color text-center mt-5 mb-3 text-muted binshops-version'>
                        <small><a href='https://github.com/binshops/laravel-blog'>Binshops Blog</a></small>

                        <small>
                            Version 9.2.x
                        </small>
                    </div>
					-->
                </div>
                <div class='col-md-9 main-content'>

                    @if (isset($errors) && count($errors))
                        <div class="alert alert-danger">
                            <b>Sorry, but there was an error:</b>
                            <ul class='m-0'>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    {{--REPLACING THIS FILE WITH YOUR OWN LAYOUT FILE? Don't forget to include the following section!--}}

                    @if(\BinshopsBlog\Helpers::has_flashed_message())
                        <div class='alert alert-info'>
                            <h3>{{\BinshopsBlog\Helpers::pull_flashed_message() }}</h3>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
