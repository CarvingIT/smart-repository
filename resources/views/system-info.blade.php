@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'title' => 'System Information'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">System Information</h4>
                    <p class="card-category">System health and configuration details</p>
                </div>
                <div class="card-body">
                    
                    {{-- Permissions Section --}}
                    <h5 class="mt-4 mb-3">📁 Directory Permissions</h5>
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
                                    <td><small>{{ $details['path'] }}</small></td>
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
                    <h5 class="mt-5 mb-3">🔧 System Dependencies</h5>
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
                                    <td><small>{{ $details['package'] }}</small></td>
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

                    {{-- Elasticsearch Section --}}
                    <h5 class="mt-5 mb-3">🔍 Elasticsearch</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="200">Configured</th>
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

                    {{-- OCR Libraries Section --}}
                    <h5 class="mt-5 mb-3">🔤 OCR Libraries</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Tesseract OCR</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
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
                                                <td><small>{{ $systemInfo['ocr']['tesseract']['path'] }}</small></td>
                                            </tr>
                                            <tr>
                                                <th>Languages</th>
                                                <td>
                                                    <span class="badge badge-info">{{ $systemInfo['ocr']['tesseract']['language_count'] }} languages</span>
                                                    <br><small>{{ implode(', ', $systemInfo['ocr']['tesseract']['languages']) }}</small>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>ocrmypdf</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
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
                                                <td><small>{{ $systemInfo['ocr']['ocrmypdf']['path'] }}</small></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Version Information Section --}}
                    <h5 class="mt-5 mb-3">💻 Version Information</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="200">PHP Version</th>
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

                    @if(!empty($systemInfo['versions']['packages']) && !isset($systemInfo['versions']['packages']['error']))
                    <h6 class="mt-4 mb-3">📦 Key Package Versions</h6>
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
                                    <td><small>{{ $details['description'] ?? '' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- Database Information Section --}}
                    <h5 class="mt-5 mb-3">💾 Database Information</h5>
                    <div class="table-responsive">
                        @if($systemInfo['database']['configured'])
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="200">Connection Type</th>
                                        <td>{{ $systemInfo['database']['connection_type'] }}</td>
                                    </tr>
                                    
                                    @if($systemInfo['database']['connection_type'] === 'sqlite')
                                        {{-- SQLite specific --}}
                                        <tr>
                                            <th>Type</th>
                                            <td>{{ $systemInfo['database']['type'] ?? 'File-based database' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Database File</th>
                                            <td><small>{{ $systemInfo['database']['database'] }}</small></td>
                                        </tr>
                                    @else
                                        {{-- MySQL/PostgreSQL/SQL Server --}}
                                        <tr>
                                            <th>Host</th>
                                            <td>{{ $systemInfo['database']['host'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Port</th>
                                            <td>{{ $systemInfo['database']['port'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Database Name</th>
                                            <td>{{ $systemInfo['database']['database'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Username</th>
                                            <td>{{ $systemInfo['database']['username'] }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($systemInfo['database']['connected'])
                                        <tr>
                                            <th>Version</th>
                                            <td>{{ $systemInfo['database']['version'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Connection Status</th>
                                            <td><span class="badge badge-success">✓ Connected</span></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th>Connection Status</th>
                                            <td><span class="badge badge-danger">✗ Failed to Connect</span></td>
                                        </tr>
                                        <tr>
                                            <th>Error</th>
                                            <td><span class="text-danger">{{ $systemInfo['database']['error'] }}</span></td>
                                        </tr>
                                    @endif
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
                    <h5 class="mt-5 mb-3">💽 Disk Space Usage</h5>
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
                                    <td><small>{{ $info['path'] }}</small></td>
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
