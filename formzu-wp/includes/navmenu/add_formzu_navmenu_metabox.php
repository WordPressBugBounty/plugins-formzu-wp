<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function add_formzu_navmenu_metabox() {
    add_meta_box(
        FORMZU_NAVMENU_METABOX_ID,
        'フォームズ フォームリンク',
        'add_metabox_callback',
        'nav-menus',
        'side',
        'low'
    );
}


function add_metabox_callback() {
    global $nav_menu_selected_id;

    $id        = FORMZU_NAVMENU_SELECT_ID;
    $form_data = FormzuOptionHandler::get_option('form_data', array());
    $html      = '<p><span>リンクさせるフォーム：</span><br>';

    $html .= sprintf(
        '<select id="%s" class="%s">',
        $id,
        'widefat edit-menu-item-target'
    );
    for ($i = 0, $l = count($form_data); $i < $l; $i++) {

        $data  = $form_data[$i];
        $value = $data['id'];

        $html .= sprintf(
            '<option value="%s">%s</option>',
            $value,
            $data['name']
        );
    }
    $html .= '</select></p>';
    $html .= '<p class="button-controls wp-clearfix"><span class="add-to-menu">';
    $html .= '<input type="submit"' . disabled($nav_menu_selected_id, 0, false);
    $html .= ' id="' . FORMZU_NAVMENU_SUBMIT_ID . '"';
    $html .= ' class="button-secondary submit-add-to-menu right" value="メニューに追加" name="add-post-type-formzu-nav" />';
    $html .= '<span class="spinner"></span>';
    $html .= '</span></p>';

    print $html;
}

