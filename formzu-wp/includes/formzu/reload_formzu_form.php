<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function reload_formzu_form() {
    // Added on 2026.08.10
    // - 対象リクエストかどうかの確認
    if ( ! isset($_POST['reload-form-data']) ) {
        return false;
    }

    // Added on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: manage_options
    if ( ! current_user_can('manage_options') ) {
        return false;
    }

    if ( ! FormzuParamHelper::check_referer('formzu-reload-form-save', 'reload-form-data') ) {
        return false;
    }

    // Fixed on 2026.08.25
    // - form_dataが配列でない場合のエラー処理を追加
    // - form_dataのインデックスを連番に直す処理を追加
    $form_data = FormzuOptionHandler::get_option('form_data', array());
    if ( ! is_array($form_data) ) {
        set_transient('formzu-admin-error', __('保存されているフォームデータが不正です。', 'formzu-admin'), 3);
        return false;
    }
    $form_data = array_values($form_data);

    // Fixed on 2026.08.25
    // - フォームIDの検証を追加
    $form_id = FormzuParamHelper::get_POS('hidden_id', 'NoID');
    $form_id = FormzuParamHelper::validate_form_id($form_id);
    if ( ! $form_id ) {
        return false;
    }

    // Fixed on 2026.08.25
    // - 更新対象の要素のインデックスを取得する処理を追加
    $old_form_data = array();
    $old_form_index = null;
    for ($i = 0, $l = count($form_data); $i < $l; $i++) {
        if ( ! is_array($form_data[$i]) ) {
            continue;
        }

        if ( isset($form_data[$i]['id']) && $form_data[$i]['id'] === $form_id ) {
            $old_form_data = $form_data[$i];
            $old_form_index = $i;
            break;
        }
    }

    if ( empty($old_form_data) ) {
        set_transient('formzu-admin-error', '更新前のデータの取得に失敗しました。', 3);
        return false;
    }

    // Fixed on 2026.08.10
    // - 保存データのサニタイジング

    $form_height = absint(FormzuParamHelper::get_POS('hidden_height', 600));
    if ( ! $form_height ) {
        $form_height = 600;
    }

    $form_mobile_height = absint(FormzuParamHelper::get_POS('hidden_mobile_height', 900));
    if ( ! $form_mobile_height ) {
        $form_mobile_height = 900;
    }

    // Fixed on 2026.08.25
    // - 旧フォーム名の検証とサニタイジングを追加
    $old_form_name = 'フォーム（' . $form_id . '）';
    if ( isset($old_form_data['name']) && is_string($old_form_data['name']) ) {
        $old_form_name = sanitize_text_field($old_form_data['name']);
    }

    $form_name = FormzuParamHelper::get_POS('hidden_title', $old_form_name);
    if ( ! is_string($form_name) ) {
        $form_name = $old_form_name;
    }
    $form_name = sanitize_text_field(wp_unslash($form_name));

    // Fixed on 2026.08.25
    // - 旧フォーム項目の検証を追加
    $old_form_items = 'Noitems';
    if ( isset($old_form_data['items']) && is_string($old_form_data['items']) ) {
        $old_form_items = $old_form_data['items'];
    }

    $form_items  = FormzuParamHelper::get_POS('hidden_items', $old_form_items);
    if ( ! is_string($form_items) ) {
        $form_items = $old_form_items;
    }
    $allowed_form_items = array(
        'br' => array()
    );
    $form_items = wp_kses(wp_unslash($form_items), $allowed_form_items);

    // Fixed on 2026.08.25
    // - 保存済みのnumberの代わりに、検索で確定した配列のインデックスを使用
    $form_number = $old_form_index;
    $reloaded    = array(
        'id'            => $form_id,
        'name'          => $form_name,
        'number'        => $form_number,
        'items'         => $form_items,
        'height'        => $form_height,
        'mobile_height' => $form_mobile_height,
    );

    // Fixed on 2026.08.25
    // - form_dataの各要素が配列かどうかの検証を追加
    // - form_dataの各要素のnumberを連番に直す処理を追加
    array_splice($form_data, $form_number, 1, array($reloaded));
    for ($i = 0, $l = count($form_data); $i < $l; $i++) {
        if ( ! is_array($form_data[$i]) ) {
            continue;
        }
        $form_data[$i]['number'] = $i;
    }
    FormzuOptionHandler::update_option( 'form_data', $form_data );

    // Fixed on 2026.08.20
    // - nonceと権限を検証した更新処理内で通知を生成するよう変更(CSRF対策)
    // - 保存済みの旧フォーム名を通知へ使用する前に型検証とサニタイジングを実施
    if ( $form_name !== $old_form_name ) {
        $message = $form_name
            . '（旧:'
            . $old_form_name
            . '） に関するデータ（タイトル、ショートコード、項目）を更新しました。';
    }
    else {
        $message = $form_name . ' に関するデータ（タイトル、ショートコード、項目）を更新しました。';
    }
    set_transient('formzu-admin-updated', $message, 3);

    // Fixed on 2026.08.10
    // - リダイレクトURLの生成・サニタイジング処理改善

    $url = menu_page_url( 'formzu-admin', false );

    wp_safe_redirect($url);
    exit;
}
