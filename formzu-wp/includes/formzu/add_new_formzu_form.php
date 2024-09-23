<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function add_new_formzu_form() {
    if ( ! FormzuParamHelper::check_referer('formzu-new-form-save', 'add-new-form') ) {
        return false;
    }

    $form_id = FormzuParamHelper::get_POS('form_id_URL', 'none');

    if ($form_id == 'none') {
        $message = 'ERROR: 10';
        set_transient( 'formzu-admin-errors', $message, 3 );
    }

    $form_id = FormzuParamHelper::get_POS('hidden_id', $form_id);
    $form_id = FormzuParamHelper::validate_form_id($form_id);

    if ( ! $form_id ) {
        return false;
    }

    $form_height        = FormzuParamHelper::get_POS('hidden_height', 800);
    $form_mobile_height = FormzuParamHelper::get_POS('hidden_mobile_height', 900);
    $form_data          = FormzuOptionHandler::get_option('form_data', array());
    $form_items         = FormzuParamHelper::get_POS('hidden_items', 'Noitems');
    $form_number        = count($form_data);
    $form_name          = FormzuParamHelper::get_POS('hidden_title', 'フォーム（' . $form_id . '）');

    $same_id_number = null;

    for ($i = 0; $i < $form_number; $i++) {
        if ( isset($form_data[$i]['id']) && $form_data[$i]['id'] == $form_id ) {

            $message = __('同じIDのフォーム（', 'formzu-admin') . $form_data[$i]['name'] . __('）が、新しいフォーム（', 'formzu-admin') . '<a href="https://ws.formzu.net/fgen/' . $form_id . '" target="_blank">' . $form_name . '</a>' . __('）によって上書きされました。', 'formzu-admin');
            $same_id_number = $i;

            break;
        }
    }

    $new_form_number = count($form_data);
    $new_data        = array(
        'id'            => $form_id,
        'name'          => $form_name,
        'items'         => $form_items,
        'height'        => $form_height,
        'mobile_height' => $form_mobile_height,
    );

    if ( $same_id_number !== null ) {
        $new_data['number'] = $form_data[$same_id_number]['number'];
        array_splice($form_data, $same_id_number, 1, array($new_data));
    }
    else {
        $new_data['number'] = $new_form_number;
        $form_data[] = $new_data;
    }

    if ( ! isset($message) ) {
        $message = __( 'フォーム : ', 'formzu-admin' ) . '<a href="https://ws.formzu.net/fgen/' . $form_id . '" target="_blank">' . $form_name . '</a>' . __('を追加しました。フォーム一覧を確認してください。', 'formzu-admin');
    }

    set_transient( 'formzu-admin-updated', $message, 3 );
    FormzuOptionHandler::update_option( 'form_data', $form_data );
    wp_safe_redirect( menu_page_url( 'formzu-admin' ) . '&action=added&number=' . $new_form_number );
    exit;
}

