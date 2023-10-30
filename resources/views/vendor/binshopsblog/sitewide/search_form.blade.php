<div style='max-width:90%;margin:50px auto;' class='search-form-outer'>
    <form method='get' action='{{route("binshopsblog.search", app('request')->get('locale'))}}' class='text-center'>
        <input type='text' name='s' placeholder='Search blogs ...' class='search-field' value='{{\Request::get("s")}}' style="width:90%;padding-left:5px;">
        <input type='submit' value='Search' class='btn btn-primary search' style="height:35px; padding:0 20px; margin-bottom:6px; font-size:0.8em;">
    </form>
</div>
<style>
	::placeholder{
		font-size:1em !important;
	}
</style>
