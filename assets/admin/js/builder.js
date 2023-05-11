jQuery(document).ready(function() {
    jQuery('form#upload_form').submit( function(e){
        e.preventDefault();
        var original_link = jQuery("input[name='original']").val();
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {original:original_link, action:'shorten_url'},
            crossDomain: true,
            beforeSend: function() {
                jQuery('.reload').css('display','block');

                jQuery('form#upload_form button').text('Обробка...');
            },
            success: function(data) {
                jQuery('.reload').css('display','none');
                jQuery('.error-message').html('');
                jQuery('.short-url').html('');
                jQuery('form#upload_form button').text('Скоротити');

                response = jQuery.parseJSON( data );
                if(response.response && response.response == 'error')
                {
                    jQuery('.error-message').html(response.message);
                } else {

                    jQuery('.short-url').html(response.link_short);

                    if ( response.rows.length )
                        jQuery('#the-list').html( response.rows );

                    if ( response.total_pages_i18n)
                    {
                        jQuery('div.tablenav.top .tablenav-pages span.displaying-num').html( response.total_items_i18n );
                        jQuery('div.tablenav.bottom .tablenav-pages span.displaying-num').html( response.total_items_i18n );
                    }

                    if ( response.total_pages )
                    {
                        jQuery('.tablenav.top .pagination-links .paging-input .total-pages').html( response.total_pages );
                        jQuery('.tablenav.bottom .pagination-links .paging-input .total-pages').html( response.total_pages );
                    }
                }


            }
        })
    })
})