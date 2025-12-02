@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'title' => 'System Information'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">System Information</h4>
                    <p class="card-category">System health and configuration details</p>
                </div>
                <div class="card-body">
                    
                    {{-- Permissions Section --}}
                    <h5 class="mt-4 mb-3"><i class="material-icons">folder</i> Directory Permissions</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Directory</th>
                                    <th>Path</th>
                                    <th>Exists</th>
                                    <th>Readable</th>
                                    <th>Writable</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($systemInfo['permissions'] as $name => $details)
                                <tr>
                                    <td><strong>{{ $name }}</strong></td>
                                    <td>{{ $details['path'] }}</td>
                                    <td>
                                        @if($details['exists'])
                                            <span class="badge badge-success">✓ Yes</span>
                                        @else
                                            <span class="badge badge-danger">✗ No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['readable'])
                                            <span class="badge badge-success">✓ Yes</span>
                                        @else
                                            <span class="badge badge-danger">✗ No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['writable'])
                                            <span class="badge badge-success">✓ Yes</span>
                                        @else
                                            <span class="badge badge-danger">✗ No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['exists'] && $details['readable'] && $details['writable'])
                                            <span class="badge badge-success">OK</span>
                                        @else
                                            <span class="badge badge-danger">ISSUE</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Dependencies Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">build</i> System Dependencies</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Command</th>
                                    <th>Description</th>
                                    <th>Package</th>
                                    <th>Required</th>
                                    <th>Status</th>
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
                                            <span class="badge badge-warning">Required</span>
                                        @else
                                            <span class="badge badge-info">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($details['installed'])
                                            <span class="badge badge-success">✓ Installed</span>
                                        @else
                                            @if($details['required'])
                                                <span class="badge badge-danger">✗ Missing</span>
                                            @else
                                                <span class="badge badge-warning">Not Installed</span>
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
                            <h5 class="mb-3"><i class="material-icons">search</i> Elasticsearch</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th width="150">Configured</th>
                                            <td>
                                                @if($systemInfo['elasticsearch']['configured'])
                                                    <span class="badge badge-success">✓ Yes</span>
                                                @else
                                                    <span class="badge badge-warning">✗ No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($systemInfo['elasticsearch']['configured'])
                                        <tr>
                                            <th>Running</th>
                                            <td>
                                                @if($systemInfo['elasticsearch']['running'])
                                                    <span class="badge badge-success">✓ Yes</span>
                                                @else
                                                    <span class="badge badge-danger">✗ No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($systemInfo['elasticsearch']['running'])
                                        <tr>
                                            <th>Version</th>
                                            <td>{{ $systemInfo['elasticsearch']['version'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cluster Name</th>
                                            <td>{{ $systemInfo['elasticsearch']['cluster_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cluster Health</th>
                                            <td>
                                                @if($systemInfo['elasticsearch']['cluster_health'] === 'green')
                                                    <span class="badge badge-success">Green (Healthy)</span>
                                                @elseif($systemInfo['elasticsearch']['cluster_health'] === 'yellow')
                                                    <span class="badge badge-warning">Yellow (Warning)</span>
                                                @else
                                                    <span class="badge badge-danger">Red (Unhealthy)</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if(!empty($systemInfo['elasticsearch']['indices']))
                                        <tr>
                                            <th>Indices</th>
                                            <td>{{ implode(', ', $systemInfo['elasticsearch']['indices']) }}</td>
                                        </tr>
                                        @endif
                                        @else
                                        <tr>
                                            <th>Error</th>
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
                            <h5 class="mb-3"><i class="material-icons">info</i> Version Information</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="150">PHP Version</th>
                                        <td>{{ $systemInfo['versions']['php']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Laravel Version</th>
                                        <td>{{ $systemInfo['versions']['laravel']['version'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- OCR Libraries Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">text_fields</i> OCR Libraries</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>TESSERACT OCR</h6>
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="150">Installed</th>
                                        <td>
                                            @if($systemInfo['ocr']['tesseract']['installed'])
                                                <span class="badge badge-success">✓ Yes</span>
                                            @else
                                                <span class="badge badge-danger">✗ No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($systemInfo['ocr']['tesseract']['installed'])
                                    <tr>
                                        <th>Version</th>
                                        <td>{{ $systemInfo['ocr']['tesseract']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Path</th>
                                        <td>{{ $systemInfo['ocr']['tesseract']['path'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Languages</th>
                                        <td>
                                            <span class="badge badge-info">{{ $systemInfo['ocr']['tesseract']['language_count'] }} languages</span>
                                            <br>{{ implode(', ', $systemInfo['ocr']['tesseract']['languages']) }}
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>OCRMYPDF</h6>
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="150">Installed</th>
                                        <td>
                                            @if($systemInfo['ocr']['ocrmypdf']['installed'])
                                                <span class="badge badge-success">✓ Yes</span>
                                            @else
                                                <span class="badge badge-warning">✗ No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($systemInfo['ocr']['ocrmypdf']['installed'])
                                    <tr>
                                        <th>Path</th>
                                        <td>{{ $systemInfo['ocr']['ocrmypdf']['path'] }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!empty($systemInfo['versions']['packages']) && !isset($systemInfo['versions']['packages']['error']))
                    <h6 class="mt-4 mb-3"><i class="material-icons">inventory</i> Key Package Versions</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Version</th>
                                    <th>Description</th>
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
                    <h5 class="mt-5 mb-3"><i class="material-icons">storage</i> Database Information</h5>
                    <div class="table-responsive">
                        @if($systemInfo['database']['configured'])
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Connection Type</th>
                                        @if($systemInfo['database']['connection_type'] !== 'sqlite')
                                        <th>Host</th>
                                        <th>Port</th>
                                        <th>Database Name</th>
                                        <th>Username</th>
                                        @else
                                        <th>Database File</th>
                                        @endif
                                        <th>Version</th>
                                        <th>Connection Status</th>
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
                                                <span class="badge badge-success">✓ Connected</span>
                                            @else
                                                <span class="badge badge-danger">✗ Failed to Connect</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-warning">
                                <i class="material-icons">warning</i>
                                No database configured
                            </div>
                        @endif
                    </div>

                    {{-- Disk Space Section --}}
                    <h5 class="mt-5 mb-3"><i class="material-icons">save</i> Disk Space Usage</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Path</th>
                                    <th>Total</th>
                                    <th>Used</th>
                                    <th>Free</th>
                                    <th>Usage</th>
                                    <th>Status</th>
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
                                            <span class="badge badge-danger">⚠ Critical</span>
                                        @elseif($info['status'] === 'warning')
                                            <span class="badge badge-danger">Warning</span>
                                        @elseif($info['status'] === 'caution')
                                            <span class="badge badge-warning">Caution</span>
                                        @else
                                            <span class="badge badge-success">OK</span>
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
