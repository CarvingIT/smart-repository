@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Set Meta Filters</h4></div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>
                <div class="card-body">

<form name="metasearch_form" action="/collection/{{ $collection->id }}/metafilters" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
    @php
        $meta_filters = Session::get('meta_filters');
        $meta_params = array();
        if(!empty($meta_filters[$collection->id])){
        foreach($meta_filters[$collection->id] as $mf){
            $meta_params['meta_field_'.$mf['field_id']] = $mf['value'];
            $meta_params['operator_'.$mf['field_id']] = $mf['operator'];
        }
        }
    @endphp
    @foreach($collection->meta_fields as $f)
    <div class="form-group row">
	<div class="col-md-3 text-right">
    <label for="meta_field_{{$f->id}}" class="col-md-12 col-form-label">{{$f->label}}</label>
	</div>
        <div class="col-md-5">
        <select class="selectpicker col-md-12" name="operator_{{$f->id}}">
            @if($f->type == 'Text')
            <option value="=" @if(@$meta_params['operator_'.$f->id] == "=") selected @endif>matches</option>
            <option value="contains" @if(@$meta_params['operator_'.$f->id] == "contains") selected @endif>contains</option>
            @elseif($f->type == 'Select')
            <option value="=" @if(@$meta_params['operator_'.$f->id] == "=") selected @endif>matches</option>
            @elseif($f->type == 'Numeric')
            <option value=">=" @if(@$meta_params['operator_'.$f->id] == ">=") selected @endif>greater than or equal to</option>
            <option value="=" @if(@$meta_params['operator_'.$f->id] == "=") selected @endif>matches</option>
            <option value="<=" @if(@$meta_params['operator_'.$f->id] == "<=") selected @endif>less than or equal to</option>
            @elseif($f->type == 'Date')
            <option value=">=" @if(@$meta_params['operator_'.$f->id] == ">=") selected @endif>on or later than</option>
            <option value="=" @if(@$meta_params['operator_'.$f->id] == "=") selected @endif>matches</option>
            <option value="<=" @if(@$meta_params['operator_'.$f->id] == "<=") selected @endif>on or before</option>
            @endif
        </select>
        </div>
        <div class="col-md-4">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ @$meta_params['meta_field_'.$f->id] }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ @$meta_params['meta_field_'.$f->id] }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Date')
        <input class="form-control" id="meta_field_{{$f->id}}" type="date" name="meta_field_{{$f->id}}" value="{{ @$meta_params['meta_field_'.$f->id] }}" placeholder="{{ $f->placeholder }}" />
        @else
        <select class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}">
            @php
                $options = explode(",", $f->options);
            @endphp
            <option value="">Any</option>
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                @endphp
            <option value="{{$o}}" @if(@$meta_params['meta_field_'.$f->id] == $o) selected @endif>{{$o}}</option>
            @endforeach
        </select>
        @endif
        </div>
    </div>
    @endforeach
<div class="form-group row mb-0">
    <div class="col-md-5 offset-md-4">
        <button type="submit" class="btn btn-primary">Set Filters</button>
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
