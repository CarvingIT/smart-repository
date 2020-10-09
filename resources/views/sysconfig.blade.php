@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'System Config','title'=>'Smart Repository'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">System Configuration</div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <div class="alert alert-<?php echo $msg; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                        </button>
                        <span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>
                        @endif
                    @endforeach

                   <form method="post" action="/admin/sysconfig">
                    @csrf()
                   <div class="form-group row">
                    <div class="col-md-12">
                        <h4>Email configuration</h4>
		            </div>
                   </div>

                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="smtp_server" class="col-md-12 col-form-label text-md-right">SMTP Server</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="smtp_server" id="smtp_server" class="form-control" placeholder="SMTP server" value="" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="smtp_user" class="col-md-12 col-form-label text-md-right">SMTP User</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="smtp_user" id="smtp_user" class="form-control" placeholder="SMTP username" value="" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="smtp_password" class="col-md-12 col-form-label text-md-right">SMTP Password</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="password" name="smtp_password" id="smtp_password" class="form-control" placeholder="SMTP password" value="" />
                    </div>
                   </div>

                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="smtp_port" class="col-md-12 col-form-label text-md-right">SMTP Port</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="smtp_port" id="smtp_port" class="form-control" placeholder="SMTP port" value="" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="smtp_protection" class="col-md-12 col-form-label text-md-right">SMTP Protection</label> 
		           </div>
                    <div class="col-md-9">
                        <select name="smtp_protection" id="smtp_protection" class="form-control selectpicker">
                            <option value="">Select</option>
                            <option value="plain">Plain</option>
                            <option value="ssl">SSL</option>
                            <option value="tls">TLS</option>
                        </select>
                    </div>
                   </div>
                   <div class="form-group row">
                    <div class="col-md-12">
                        <h4>Search Preferences</h4>
		            </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="search_mode" class="col-md-12 col-form-label text-md-right">Mode of search</label> 
		           </div>
                    <div class="col-md-9">
                        <select name="search_mode" id="search_mode" class="form-control selectpicker">
                            <option value="">Select Mode of Search</option>
                            <option value="database">Database</option>
                            <option value="elastic">Elastic search</option>
                        </select>
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                     <label for="elastic_hosts" class="col-md-12 col-form-label text-md-right">Elastic Hosts</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="elastic_hosts" id="elastic_hosts" class="form-control" placeholder="Comma Separated list of hosts" value="" />
                    </div>
                   </div>
                
                   <div class="form-group row">
                    <div class="col-md-12">
                        <h4>Site Configuration</h4>
		            </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="logo_url" class="col-md-12 col-form-label text-md-right">Logo URL</label> 
		           </div>
                    <div class="col-md-9">
                    <input type="text" name="logo_url" id="logo_url" class="form-control" placeholder="http://domain.com/i/logo.png" value="{{$sysconfig['logo_url'] }}" />
                    </div>
                   </div>
                   <div class="form-group row mb-0"><div class="col-md-12 offset-md-4"><button type="submit" class="btn btn-primary">
                                    Save
                                </button> 
                     </div></div> 
                   </form> 
                </div>
            </div>
            
        </div>
    </div>
</div>
</div>
@endsection
