jQuery(document).ready(function() {
    jQuery('form#short_form').submit( function(e){
        e.preventDefault();
        var original_link = jQuery("input[name='original']").val();
        jQuery.ajax({
            url: ajax_short,
            type: 'post',
            data: {original:original_link, action:'short_url_user'},
            crossDomain: true,
            beforeSend: function() {
                jQuery('.reload').css('display','block');
                jQuery('form#short_form button').text('Обробка...');
            },
            success: function(data) {
                console.log()
                jQuery('.reload').css('display','none');
                jQuery('.error-message').html('');
                jQuery('.short-url').html('');
                jQuery('form#short_form button').text('Скоротити');

                if(data.response && data.response == 'error')
                {
                    jQuery('.error-message').html(data.message);
                } else {
                    jQuery('.short-url').html(data.link_short);
                }

            }
        })
    })
});