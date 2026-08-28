<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


// Fixed on 2026.08.25
// - actionならびに$widget_idの検証を追加
// Fixed on 2026.08.26
// - widget_idとtitle_elements[i].idの比較を部分一致から完全一致に変更
function show_created_formzu_widget() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('action', 'id')) ) {
        return false;
    }

    if ( ! is_string($_REQUEST['action']) || $_REQUEST['action'] !== 'created_formzu_widget' ) {
        return false;
    }

    $widget_id = FormzuParamHelper::get_REQ('widget_id', false);

    if ( ! $widget_id || ! is_string($widget_id) ) {
        return false;
    }
?>
<script>
    (function($){
        var widget_id = '<?php echo esc_js($widget_id) ?>';

        console.log(widget_id);
        if ( ! widget_id ) {
            return false;
        }

        var title_elements = $('.widget');
        var $elem;
        console.log(title_elements);

        for (var i = 0, l = title_elements.length; i < l; i++) {
            if ( title_elements[i].id === widget_id ) {
                $elem = $(title_elements[i]);
            }
        }

        if ( ! $elem ) {
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
