<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function reload_formzu_form() {
    if ( ! FormzuParamHelper::check_referer('formzu-reload-form-save', 'reload-form-data') ) {
        return false;
    }

    $form_height        = FormzuParamHelper::get_POS('hidden_height', 600);
    $form_mobile_height = FormzuParamHelper::get_POS('hidden_mobile_height', 700);
    $form_data          = FormzuOptionHandler::get_option('form_data', array());
    $form_id            = FormzuParamHelper::get_POS('hidden_id', 'NoID');
    $old_form_data;

    for ($i = 0, $l = count($form_data); $i < $l; $i++) {
        if (isset($form_data[$i]['id']) && $form_data[$i]['id'] == $form_id) {
            $old_form_data = $form_data[$i];
            break;
        }
    }

    if ( empty($old_form_data) ) {
        set_transient( 'formzu-admin-error', '更新前のデータの取得に失敗しました。', 3 );
        return false;
    }

    $form_name   = FormzuParamHelper::get_POS('hidden_title', $old_form_data['name']);
    $form_items  = FormzuParamHelper::get_POS('hidden_items', $old_form_data['items']);
    $form_number = $old_form_data['number'];
    $reloaded    = array(
        'id'            => $form_id,
        'name'          => $form_name,
        'number'        => $form_number,
        'items'         => $form_items,
        'height'        => $form_height,
        'mobile_height' => $form_mobile_height,
    );

    array_splice($form_data, $form_number, 1, array($reloaded));
    FormzuOptionHandler::update_option( 'form_data', $form_data );

    $url_atts = '&action=reloaded&name=' . $form_name;

    if ( $form_name != $old_form_data['name'] ) {
        $url_atts .= ('&oldname=' . $old_form_data['name']);
    }

    $url = menu_page_url( 'formzu-admin' ) . sanitize_text_field($url_atts);

    wp_safe_redirect($url);
    exit;
}

