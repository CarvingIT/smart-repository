// Favorites Toggle Handler
$(document).on('click', '.js-fav-toggle-ui', function(e) {
    e.preventDefault();
    
    var $button = $(this);
    var documentId = $button.data('doc-id');
    var $icon = $button.find('.fav-icon');
    var isCurrentlyFavorited = $button.attr('aria-pressed') === 'true';
    
    // Disable button during request
    $button.prop('disabled', true);
    
    $.ajax({
        url: '/favorites/toggle/' + documentId,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                var nowFavorited = response.favorited;
                
                if (nowFavorited) {
                    $icon.text('favorite');
                    $button.attr('aria-pressed', 'true');
                    $button.attr('title', 'Remove from favourites');
                } else {
                    $icon.text('favorite_border');
                    $button.attr('aria-pressed', 'false');
                    $button.attr('title', 'Add to favourites');
                }
            }
        },
        error: function(xhr) {
            console.error('Favorite toggle error:', xhr);
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
        },
        complete: function() {
            $button.prop('disabled', false);
        }
    });
});

// Notification helper function
function showNotification(type, message) {
    if (typeof $.notify !== 'undefined') {
        $.notify({
            icon: "notifications",
            message: message
        }, {
            type: type,
            timer: 3000,
            placement: {
                from: 'top',
                align: 'right'
            }
        });
    } else {
        alert(message);
    }
}