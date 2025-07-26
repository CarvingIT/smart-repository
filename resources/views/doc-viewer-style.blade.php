@php
    $c = \App\Collection::find($collection_id);
    // show styles depending on the collection-config
    $download_display = 'display: none;';
@endphp
#download{
    {{ $download_display }}
}
