<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function show_created_formzu_widget() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('action', 'id')) ) {
        return false;
    }

    $widget_id = FormzuParamHelper::get_REQ('widget_id', false);

    if ( ! $widget_id ) {
        return false;
    }

?>
<script>
    (function($){
        var widget_id = '<?php echo esc_js($widget_id) ?>';

        console.log(widget_id);
        if (!widget_id) {
            return false;
        }

        var title_elements = $('.widget');
        var $elem;
        console.log(title_elements);

        for (var i = 0, l = title_elements.length; i < l; i++) {
            if (title_elements[i].id.indexOf(widget_id) != -1) {
                $elem = $(title_elements[i]);
            }
        }

        if (!$elem) {
            return false;
        }

        $elem.addClass('my-updated');

        $elem.prepend('<span class="new-item">NEW!</span>');
        $elem.bind('animationend webkitAnimationEnd oAnimationEnd mozAnimationEnd', function(){
            $elem.removeClass('my-updated');
        });
    })(jQuery);
</script>
<?php
}

