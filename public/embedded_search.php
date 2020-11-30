<html>
<head>
<!-- jQuery Library -->
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>
<body>
<script>
$(function () {

$('form').on('submit', function (e) {
e.preventDefault();
$.ajax({
    type: "GET",
    url: "http://smartarchive.local/collection/1/search",
    data: $('form').serialize(), 
    success: function(data){
        //alert("SKK"+data);
	var obj = JSON.parse(data);
	if(data){
                var len = obj.data.length;
                var txt = "";
                if(len > 0){
                    for(var i=0;i<len;i++){
                        if(obj.data[i].title){
                            txt += "<div>"+obj.data[i].title+obj.data[i].type.display+"</div>";
                        }
                    }
                    if(txt != ""){
                        $("#searchResults").append(txt).removeClass("hidden");
                    }
                }
            }
    },
    // Alert status code and error if fail
    error: function (xhr, ajaxOptions, thrownError){
        alert(xhr.status);
        alert(thrownError);
    }
});
});

$('form').on('reset', function () {
    window.location.reload();
});

});
</script>
<style>
.hidden{display:none;}
</style>
<form method="get" action="" >
<input type="hidden" name="collection_id" value="1">
<input type="hidden" name="embedded" value="1">
<input type="hidden" name="length" value="10">
<input type="hidden" name="start" value="0">
<input type="text" name="search[value]">
<input type="submit" value="Submit">
<input type="reset" value="Reset">
</form>
<table id="searchResults" style="width:80%;" class="hidden" >
    <tr style="border:1px solid #ccc;">
        <th style="border:1px solid #ccc;</th>
    </tr>
</table>
</body>
</html>
