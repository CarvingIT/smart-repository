<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Required - {{ config('app.name', 'DAMS') }}</title>
    <link href="{{ asset('material') }}/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        }
        .password-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        .password-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .card-header-password {
            background: linear-gradient(60deg, #ab47bc, #8e24aa);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .lock-icon {
            font-size: 60px;
            margin-bottom: 10px;
        }
        .card-body-password {
            padding: 40px 30px;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #9c27b0;
            box-shadow: 0 0 0 2px rgba(156, 39, 176, 0.1);
        }
        .btn-access {
            background: linear-gradient(60deg, #ab47bc, #8e24aa);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-access:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(156, 39, 176, 0.4);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .input-group {
            position: relative;
            margin-bottom: 10px;
        }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }
        .form-control-with-icon {
            padding-left: 45px;
        }
        .help-text {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="password-container">
        <div class="password-card">
            <div class="card-header-password">
                <i class="material-icons lock-icon">lock</i>
                <h3 style="margin: 0; font-weight: 400;">{{ __('Password Protected') }}</h3>
                <p style="margin: 10px 0 0; opacity: 0.9; font-size: 14px;">{{ __('This document is password protected') }}</p>
            </div>
            <div class="card-body-password">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('shared-links.verify-password', $sharedLink->token) }}">
                    @csrf
                    <div class="input-group">
                        <i class="material-icons input-icon">lock_outline</i>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control form-control-with-icon" 
                            placeholder="{{ __('Enter password') }}"
                            required 
                            autofocus>
                    </div>
                    
                    <button type="submit" class="btn-access">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 5px;">lock_open</i>
                        {{ __('Access Document') }}
                    </button>

                    <p class="help-text">
                        {{ __('Please enter the password provided by the document owner') }}
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('material') }}/js/core/jquery.min.js"></script>
    <script src="{{ asset('material') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('material') }}/js/core/bootstrap-material-design.min.js"></script>
</body>
</html>
