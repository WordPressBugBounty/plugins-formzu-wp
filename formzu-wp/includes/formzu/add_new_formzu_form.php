<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function add_new_formzu_form() {
    // Added on 2026.08.10
    // - 対象リクエストかどうかの確認
    if ( ! isset($_POST['add-new-form']) ) {
        return false;
    }

    // Added on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: manage_options
    if ( ! current_user_can('manage_options') ) {
        return false;
    }

    if ( ! FormzuParamHelper::check_referer('formzu-new-form-save', 'add-new-form') ) {
        return false;
    }

    $form_id = FormzuParamHelper::get_POS('form_id_URL', 'none');

    if ( $form_id === 'none' ) {
        $message = 'ERROR: 10';
        set_transient( 'formzu-admin-errors', $message, 3 );
    }

    $form_id = FormzuParamHelper::get_POS('hidden_id', $form_id);
    $form_id = FormzuParamHelper::validate_form_id($form_id);

    if ( ! $form_id ) {
        return false;
    }

    // Fixed on 2026.08.07
    // - 保存データのサニタイジング
    // - 通知メッセージ内リンクの出力をエスケープ
    $form_name = FormzuParamHelper::get_POS(
        'hidden_title',
        'フォーム（' . $form_id . '）'
    );
    if ( ! is_string($form_name) ) {
        $form_name = 'フォーム（' . $form_id . '）';
    }
    $form_name = sanitize_text_field(wp_unslash($form_name));

    $form_items = FormzuParamHelper::get_POS('hidden_items', 'Noitems');
    if ( ! is_string($form_items) ) {
        $form_items = 'Noitems';
    }

    $allowed_form_items = array(
        'br' => array()
    );
    $form_items = wp_kses(wp_unslash($form_items), $allowed_form_items);

    $form_height = absint(FormzuParamHelper::get_POS('hidden_height', 800));
    if ( ! $form_height ) {
        $form_height = 800;
    }

    $form_mobile_height = absint(FormzuParamHelper::get_POS('hidden_mobile_height', 900));
    if ( ! $form_mobile_height ) {
        $form_mobile_height = 900;
    }

    // Fixed on 2026.08.25
    // - form_dataが配列でない場合のエラーを追加
    $form_data = FormzuOptionHandler::get_option('form_data', array());
    if ( ! is_array($form_data) ) {
        set_transient('formzu-admin-error', __('保存されているフォームデータが不正です。', 'formzu-admin'), 3);
        return false;
    }

    // Fixed on 2026.08.25
    // - array_values()を使用して、添字が連番になるようにする
    $form_data   = array_values($form_data);
    $form_number = count($form_data);

    $same_id_number = null;

    $form_url = esc_url('https://ws.formzu.net/fgen/' . rawurlencode($form_id));
    $form_link = '<a href="' . $form_url . '" target="_blank" rel="noopener noreferrer">' . esc_html($form_name) . '</a>';

    // Fixed on 2026.08.25
    // - form_dataの各要素が配列かどうかの検証を追加
    // - 保存済みのフォーム名の文字列検証とサニタイジングを追加
    for ($i = 0; $i < $form_number; $i++) {
        if ( ! is_array($form_data[$i]) ) {
            continue;
        }

        if ( isset($form_data[$i]['id']) && $form_data[$i]['id'] === $form_id ) {
            $registered_form_name = 'フォーム（' . $form_id . '）';
            if ( isset($form_data[$i]['name']) && is_string($form_data[$i]['name']) ) {
                $registered_form_name = sanitize_text_field($form_data[$i]['name']);
            }

            $message = __('同じIDのフォーム（', 'formzu-admin')
                . $registered_form_name
                . __('）が、新しいフォーム（', 'formzu-admin')
                . $form_link
                . __('）によって上書きされました。', 'formzu-admin');
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

    // Fixed on 2026.08.25
    // - 同一IDの場合、保存済みのnumberではなく、form_dataのインデックスを使用するように変更
    if ( $same_id_number !== null ) {
        $new_data['number'] = $same_id_number;
        array_splice($form_data, $same_id_number, 1, array($new_data));
    }
    else {
        $new_data['number'] = $new_form_number;
        $form_data[] = $new_data;
    }

    // Fixed on 2026.08.25
    // - form_dataのインデックスをnumberに反映させる
    for ($i = 0, $l = count($form_data); $i < $l; $i++) {
        if ( ! is_array($form_data[$i]) ) {
            continue;
        }
        $form_data[$i]['number'] = $i;
    }

    if ( ! isset($message) ) {
        $message = __( 'フォーム : ', 'formzu-admin' )
            . $form_link
            . __('を追加しました。フォーム一覧を確認してください。', 'formzu-admin');
    }

    set_transient( 'formzu-admin-updated', $message, 3 );
    FormzuOptionHandler::update_option( 'form_data', $form_data );
    wp_safe_redirect( menu_page_url( 'formzu-admin' ) . '&action=added&number=' . $new_form_number );
    exit;
}
