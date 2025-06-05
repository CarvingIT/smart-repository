<!DOCTYPE html>
@php
$config = \App\Sysconfig::all();
$settings = array();
foreach($config as $c){
	$settings[$c->param] = $c->value;
}
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Smart Repository - Knowledge management made easy') }}</title>
	@if(!empty($settings['favicon_url']))
    <link rel="icon" type="image/png" href="/storage/{{ $settings['favicon_url']}}">
    <link rel="apple-touch-icon" sizes="76x76" href="/storage/{{ $settings['favicon_url']}}">
	@else
    <link rel="icon" type="image/png" href="{{ asset('material') }}/img/favicon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('material') }}/img/apple-icon.png">
	@endif
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!-- link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" -->
    <!-- link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" -->

    <!-- CSS Files -->
    <link href="{{ asset('material') }}/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
    <link href="/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="{{ asset('material') }}/css/bootstrap-select.min.css" rel="stylesheet" />
    <!--
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    -->
        <!--   Core JS Files   -->
        <script src="/js/jquery-3.5.1.js"></script>
        <!--   Core JS Files   -->
        <script src="{{ asset('material') }}/js/core/popper.min.js"></script>
        <script src="{{ asset('material') }}/js/core/bootstrap-material-design.min.js"></script>
        <script src="{{ asset('material') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
        <!-- Plugin for the momentJs  -->
        <script src="{{ asset('material') }}/js/plugins/moment.min.js"></script>
        <!-- Forms Validations Plugin -->
        <script src="{{ asset('material') }}/js/plugins/jquery.validate.min.js"></script>
        <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
        <!--script src="{{ asset('material') }}/js/plugins/jquery.bootstrap-wizard.js"></script-->
        <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
        <script src="{{ asset('material') }}/js/plugins/bootstrap-selectpicker.js"></script>
        <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
        <script src="{{ asset('material') }}/js/plugins/bootstrap-datetimepicker.min.js"></script>
        <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
        <script src="{{ asset('material') }}/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
        <script src="{{ asset('material') }}/js/settings.js"></script>
	@stack('js')
    <link href="/css/custom.css" rel="stylesheet" />
	<!-- overriding css -->
	<style>
@php
    $conf = \App\Sysconfig::all();
    $settings = array();
    foreach($conf as $c){
        $settings[$c->param] = $c->value;
    }
@endphp
                    @if(!empty($settings['overridingcss']))
                    {!! $settings['overridingcss'] !!}
					@endif
	</style>
    </head>
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
			@include('layouts.page_templates.user-home')
        @endauth
        @guest()
            @include('layouts.page_templates.guest')
        @endguest
    </body>
</html>
