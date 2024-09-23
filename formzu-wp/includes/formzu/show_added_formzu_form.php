<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function show_added_formzu_form() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('action', 'number')) ) {
        return false;
    }
    if ($_REQUEST['action'] != 'added') {
        return false;
    }
    if ( ! ctype_digit($_REQUEST['number']) ) {
        return false;
    }

?>
<script>
    (function($){
        //新しく追加されたフォームの場所をアニメーションで示す
        $(document).ready(function(){
            var $listbox  = $('#formzu-list-box');
            var is_closed = $listbox.hasClass('closed');

            if (is_closed) {
                $listbox.removeClass('closed');
            }

            var new_item = $('.new-item')[0];

            if (new_item) {

                var $new_item = $(new_item);
                var table_row = $new_item.parent().parent();
                
                if (!table_row) {
                    return;
                }

                var $table_row = $(table_row);

                $table_row.css({'opacity': '0', 'height': '0%'});
                $('html,body').animate({scrollTop: $new_item.offset().top}, {
                    'duration': 'slow',
                    'callback': $table_row.delay(500).fadeTo('slow', 1)
                });

            }
        });

    })(jQuery);
</script>
<?php
}

