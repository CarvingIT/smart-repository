@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Manage Meta Information Fields</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                   <form method="post" action="/collection/{{$collection->id}}/meta">
                    @csrf()
                    <input type="hidden" name="collection_id" value="{{$collection->id}}" />
                   <div class="form-group row">
                   <label for="field_name" class="col-md-4 col-form-label text-md-right">Label</label> 
                    <div class="col-md-6">
                    <input type="text" name="field_name" id="field_name" class="form-control" placeholder="Label of the field you will be creating" value="" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <label for="description" class="col-md-4 col-form-label text-md-right">Placeholder text</label> 
                    <div class="col-md-6">
                    <input type="text" name="description" id="description" class="form-control" placeholder="A short description of what you want to store" value="" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <label for="collection_type" class="col-md-4 col-form-label text-md-right">Data type</label> 
                    <div class="col-md-6">
                        <select class="form-control" name="field_type">
                            <option value="text">Text</option> 
                            <option value="number">Numeric</option> 
                        </select>
                    </div>
                   </div>
                
                   <div class="form-group row mb-0"><div class="col-md-8 offset-md-4"><button type="submit" class="btn btn-primary">
                                    Save
                                </button> 
                     </div></div> 
                   </form> 
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
