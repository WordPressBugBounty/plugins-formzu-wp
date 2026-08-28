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

    $id = FORMZU_NAVMENU_SELECT_ID;

    // Fixed on 2026.08.25
    // - form_dataが配列かどうかの検証を追加
    // - form_dataのインデックスを連番にする処理を追加
    $form_data = FormzuOptionHandler::get_option('form_data', array());
    if ( ! is_array($form_data) ) {
        $form_data = array();
    }
    $form_data = array_values($form_data);

    $html = '<p><span>リンクさせるフォーム：</span><br>';
    $html .= sprintf(
        '<select id="%s" class="%s">',
        $id,
        'widefat edit-menu-item-target'
    );

    // Fixed on 2026.08.25
    // - form_dataの各要素が配列かどうかの検証を追加
    // - form_dataの各要素にフォームIDとフォーム名が存在するかどうかの検証を追加
    // - フォームIDの検証を追加
    for ($i = 0, $l = count($form_data); $i < $l; $i++) {
        if (
            ! is_array($form_data[$i])
            || ! isset($form_data[$i]['id'])
            || ! is_string($form_data[$i]['id'])
            || ! isset($form_data[$i]['name'])
            || ! is_string($form_data[$i]['name'])
        ) {
            continue;
        }

        $data  = $form_data[$i];
        $value = FormzuParamHelper::validate_form_id($data['id']);
        if ( ! $value ) {
            continue;
        }

        // Fixed on 2026.08.07
        // - フォームID,フォーム名をエスケープ
        $html .= sprintf('<option value="%s">%s</option>',
            esc_attr($value),
            esc_html($data['name'])
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
