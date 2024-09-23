<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function setup_formzu_link_navmenu( $item ) {
    if ( isset($item->object) ) {
        if ( $item->object == 'post_type_formzu_link' ) {

            $item->type_label = 'フォームズ フォームリンク';

            if ( ! has_action('admin_footer', 'set_navmenu_hidden_input') ) {
                add_action('admin_footer', 'set_navmenu_hidden_input');
            }
        }
    }
    return $item;
}

