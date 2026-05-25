@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'title' => 'System Information'])

@section('content')
<div class="container">
    <style>
        /* Ensure system-info tables have a clear outline on patterned backgrounds */
        .system-info-table {
            background: #ffffff;
            border-collapse: collapse;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.06);
        }
        .system-info-table th,
        .system-info-table td {
            border: 1px solid #e9ecef !important;
            vertical-align: middle;
        }
        /* Make sure responsive wrapper doesn't clip visible borders */
        .table-responsive { overflow-x: auto; }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ __('System Information') }}</h4>
                    <p class="card-category">{{ __('System health and configuration details') }}</p>
                </div>
                <div class="card-body">
                    
                    {{-- Permissions Section --}}
                    <h5 class="mt-4 mb-3"><i class="material-icons">folder</i> {{ __('Directory Permissions') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Directory') }}</th>
                                    <th>{{ __('Path') }}</th>
                                    <th>{{ __('Exists') }}</th>
                                    <th>{{ __('Readable') }}</th>
                                    <th>{{ __('Writable') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($systemInfo['permissions'] as $name => $details)
                                <tr>
                                    <td><strong>{{ $name }}</strong></td>
                                    <td>{{ $details['path'] }}</td>
                                    <td>
                                        @if($details['exists'])
                                            <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                        @else
                                            <span class="badge badge-danger">✗ {{ __('No') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['readable'])
                                            <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                        @else
                                            <span class="badge badge-danger">✗ {{ __('No') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['writable'])
                                            <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                        @else
                                            <span class="badge badge-danger">✗ {{ __('No') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['exists'] && $details['readable'] && $details['writable'])
                                            <span class="badge badge-success">{{ __('OK') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('ISSUE') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Dependencies Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">build</i> {{ __('System Dependencies') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Command') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Package') }}</th>
                                    <th>{{ __('Required') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($systemInfo['dependencies'] as $cmd => $details)
                                <tr>
                                    <td><code>{{ $cmd }}</code></td>
                                    <td>{{ $details['description'] }}</td>
                                    <td>{{ $details['package'] }}</td>
                                    <td>
                                        @if($details['required'])
                                            <span class="badge badge-warning">{{ __('Required') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('Optional') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['installed'])
                                            <span class="badge badge-success">✓ {{ __('Installed') }}</span>
                                        @else
                                            @if($details['required'])
                                                <span class="badge badge-danger">✗ {{ __('Missing') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('Not Installed') }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Elasticsearch and Version Information Side by Side --}}
                    <div class="row mt-5">
                        <div class="col-md-6">
                            {{-- Elasticsearch Section --}}
                            <h5 class="mb-3"><i class="material-icons">search</i> {{ __('Elasticsearch') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th width="150">{{ __('Configured') }}</th>
                                            <td>
                                                @if($systemInfo['elasticsearch']['configured'])
                                                    <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-warning">✗ {{ __('No') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($systemInfo['elasticsearch']['configured'])
                                        <tr>
                                            <th>{{ __('Running') }}</th>
                                            <td>
                                                @if($systemInfo['elasticsearch']['running'])
                                                    <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-danger">✗ {{ __('No') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($systemInfo['elasticsearch']['running'])
                                        <tr>
                                            <th>{{ __('Version') }}</th>
                                            <td>{{ $systemInfo['elasticsearch']['version'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Cluster Name') }}</th>
                                            <td>{{ $systemInfo['elasticsearch']['cluster_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Cluster Health') }}</th>
                                            <td>
                                                @if($systemInfo['elasticsearch']['cluster_health'] === 'green')
                                                    <span class="badge badge-success">{{ __('Green (Healthy)') }}</span>
                                                @elseif($systemInfo['elasticsearch']['cluster_health'] === 'yellow')
                                                    <span class="badge badge-warning">{{ __('Yellow (Warning)') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('Red (Unhealthy)') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if(!empty($systemInfo['elasticsearch']['indices']))
                                        <tr>
                                            <th>{{ __('Indices') }}</th>
                                            <td>{{ implode(', ', $systemInfo['elasticsearch']['indices']) }}</td>
                                        </tr>
                                        @endif
                                        @else
                                        <tr>
                                            <th>{{ __('Error') }}</th>
                                            <td><span class="text-danger">{{ $systemInfo['elasticsearch']['error'] }}</span></td>
                                        </tr>
                                        @endif
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Version Information Section --}}
                            <h5 class="mb-3"><i class="material-icons">info</i> {{ __('Version Information') }}</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="150">{{ __('PHP Version') }}</th>
                                        <td>{{ $systemInfo['versions']['php']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Laravel Version') }}</th>
                                        <td>{{ $systemInfo['versions']['laravel']['version'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SMTP / Mail Configuration Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">mail</i> {{ __('SMTP Configuration') }}</h5>
                    <div class="table-responsive">
                        @if(!$systemInfo['mail']['configured'])
                            <div class="alert alert-warning">SMTP configuration is pending.</div>
                        @else
                            <table class="table table-bordered system-info-table">
                                <tbody>
                                    <tr>
                                        <th width="180">{{ __('Driver') }}</th>
                                        <td>{{ $systemInfo['mail']['driver'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Host') }}</th>
                                        <td>{{ $systemInfo['mail']['host'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Port') }}</th>
                                        <td>{{ $systemInfo['mail']['port'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Username') }}</th>
                                        <td>{{ $systemInfo['mail']['username'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Encryption') }}</th>
                                        <td>{{ $systemInfo['mail']['encryption'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('From Address') }}</th>
                                        <td>{{ $systemInfo['mail']['from'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Password') }}</th>
                                        <td><em>Not displayed</em></td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>

                    {{-- OCR Libraries Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">text_fields</i> {{ __('OCR Libraries') }}</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('TESSERACT OCR') }}</h6>
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="150">{{ __('Installed') }}</th>
                                        <td>
                                            @if($systemInfo['ocr']['tesseract']['installed'])
                                                <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                            @else
                                                <span class="badge badge-danger">✗ {{ __('No') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($systemInfo['ocr']['tesseract']['installed'])
                                    <tr>
                                        <th>{{ __('Version') }}</th>
                                        <td>{{ $systemInfo['ocr']['tesseract']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Path') }}</th>
                                        <td>{{ $systemInfo['ocr']['tesseract']['path'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Languages') }}</th>
                                        <td>
                                            <span class="badge badge-info">{{ $systemInfo['ocr']['tesseract']['language_count'] }} {{ __('languages') }}</span>
                                            <br>{{ implode(', ', $systemInfo['ocr']['tesseract']['languages']) }}
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('OCRMYPDF') }}</h6>
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="150">{{ __('Installed') }}</th>
                                        <td>
                                            @if($systemInfo['ocr']['ocrmypdf']['installed'])
                                                <span class="badge badge-success">✓ {{ __('Yes') }}</span>
                                            @else
                                                <span class="badge badge-warning">✗ {{ __('No') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($systemInfo['ocr']['ocrmypdf']['installed'])
                                    <tr>
                                        <th>{{ __('Path') }}</th>
                                        <td>{{ $systemInfo['ocr']['ocrmypdf']['path'] }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!empty($systemInfo['versions']['packages']) && !isset($systemInfo['versions']['packages']['error']))
                    <h6 class="mt-4 mb-3"><i class="material-icons">inventory</i> {{ __('Key Package Versions') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Package') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($systemInfo['versions']['packages'] as $packageName => $details)
                                <tr>
                                    <td><code>{{ $packageName }}</code></td>
                                    <td><span class="badge badge-secondary">{{ $details['version'] ?? 'unknown' }}</span></td>
                                    <td>{{ $details['description'] ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- Database Information Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">storage</i> {{ __('Database Information') }}</h5>
                    <div class="table-responsive">
                        @if($systemInfo['database']['configured'])
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Connection Type') }}</th>
                                        @if($systemInfo['database']['connection_type'] !== 'sqlite')
                                        <th>{{ __('Host') }}</th>
                                        <th>{{ __('Port') }}</th>
                                        <th>{{ __('Database Name') }}</th>
                                        <th>{{ __('Username') }}</th>
                                        @else
                                        <th>{{ __('Database File') }}</th>
                                        @endif
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Connection Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $systemInfo['database']['connection_type'] }}</td>
                                        @if($systemInfo['database']['connection_type'] !== 'sqlite')
                                        <td>{{ $systemInfo['database']['host'] }}</td>
                                        <td>{{ $systemInfo['database']['port'] }}</td>
                                        <td>{{ $systemInfo['database']['database'] }}</td>
                                        <td>{{ $systemInfo['database']['username'] }}</td>
                                        @else
                                        <td>{{ $systemInfo['database']['database'] }}</td>
                                        @endif
                                        <td>{{ $systemInfo['database']['version'] }}</td>
                                        <td>
                                            @if($systemInfo['database']['connected'])
                                                <span class="badge badge-success">✓ {{ __('Connected') }}</span>
                                            @else
                                                <span class="badge badge-danger">✗ {{ __('Failed to Connect') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-warning">
                                <i class="material-icons">warning</i>
                                {{ __('No database configured') }}
                            </div>
                        @endif
                    </div>

                    {{-- Disk Space Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">save</i> {{ __('Disk Space Usage') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Location') }}</th>
                                    <th>{{ __('Path') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Used') }}</th>
                                    <th>{{ __('Free') }}</th>
                                    <th>{{ __('Usage') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($systemInfo['disk_space'] as $name => $info)
                                <tr>
                                    <td><strong>{{ ucfirst($name) }}</strong></td>
                                    <td>{{ $info['path'] }}</td>
                                    <td>{{ $info['total_human'] }}</td>
                                    <td>{{ $info['used_human'] }}</td>
                                    <td>{{ $info['free_human'] }}</td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar 
                                                @if($info['status'] === 'critical') bg-danger
                                                @elseif($info['status'] === 'warning') bg-danger
                                                @elseif($info['status'] === 'caution') bg-warning
                                                @else bg-success
                                                @endif" 
                                                role="progressbar" 
                                                style="width: {{ $info['percentage'] }}%"
                                                aria-valuenow="{{ $info['percentage'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ $info['percentage'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($info['status'] === 'critical')
                                            <span class="badge badge-danger">⚠ {{ __('Critical') }}</span>
                                        @elseif($info['status'] === 'warning')
                                            <span class="badge badge-danger">{{ __('Warning') }}</span>
                                        @elseif($info['status'] === 'caution')
                                            <span class="badge badge-warning">{{ __('Caution') }}</span>
                                        @else
                                            <span class="badge badge-success">{{ __('OK') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
