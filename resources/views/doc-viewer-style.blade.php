@php
    $c = \App\Collection::find($collection_id);
    $settings = json_decode($c->column_config);

    // show styles depending on the collection-config
    //print_r($settings);
    if(auth()->user() && auth()->user()->hasPermission($collection_id,'MAINTAINER')){
        $download_display = 'display:block;';
    }
    else{
        if(empty($settings->document_viewer_download))
        $download_display = 'display: none;';
    }
@endphp
#download{
    {{ @$download_display }}
}
