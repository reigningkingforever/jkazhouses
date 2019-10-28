(function($) {

    $(document).ready(function($) {

        var button = $('#ajax-test');

        var $ID      = $('.post-list-ajax-search-form').children('form').attr('id'),
            $form    = $('#'+$ID),
            $input   = $form.find('input[type="text"]'),
            $wrapper = $form.siblings('.result-posts-wrapper').hide();

        $input.keypress(function(e){
            if(e.which == 13) {
                return false;
            }else {
                return true;
            }
        });


        $input.on('keyup', function(e) {
            e.preventDefault();

            var $key = $(this).val(),
                $nonce = $(this).siblings('#eael_ajax_post_search_nonce').val();
            

            $.ajax({
                url : localize.ajaxurl,
                type: 'post',
                data: {
                    action  : 'eael_ajax_post_search',
                    _nonce: $nonce,
                    key     : $key
                },
                success: function (r) {

                    if ($key != '') {
                        
                        if ('' != r) {
                            setTimeout(function() {
                                $wrapper.html(r);
                                $wrapper.fadeIn();
                            }, 50);
                        }

                    }else {
                        $wrapper.hide();
                    }
                },
                error: function(r) {
                    console.log('err', r);
                }
            });

        });


    });

})(jQuery);