<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function delete_formzu_form_data() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('action', 'delete_nonce', 'id', 'number')) ) {
        return false;
    }

    $action = FormzuParamHelper::get_REQ('action', false);

    if ( 'delete_form' !== $action ) {
        return false;
    }

    // Added on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: manage_options および edit_theme_options
    if ( ! current_user_can('manage_options') || ! current_user_can('edit_theme_options') ) {
        return false;
    }

    // Added on 2026.08.19
    // - Array to string conversionの抑止
    if ( ! is_string($_REQUEST['id']) || ! is_scalar($_REQUEST['number']) ) {
        return false;
    }

    if ( ! FormzuParamHelper::check_referer('delete_form-' . $_REQUEST['id'], 'delete_nonce') ) {
        return false;
    }

    // Fixed on 2026.08.25
    // - form_dataが配列かどうかの検証を追加
    // - array_values()でform_dataのインデックスを連番に直す
    $form_data = FormzuOptionHandler::get_option('form_data', array());
    if ( ! is_array($form_data) ) {
        set_transient('formzu-admin-error', __('保存されているフォームデータが不正です。', 'formzu-admin'), 3);
        return false;
    }
    $form_data = array_values($form_data);

    // Fixed on 2026.08.25
    // - numberが0以上の整数かどうかの検証を追加
    if ( preg_match('/^[0-9]+$/D', strval($_REQUEST['number'])) !== 1 ) {
        return false;
    }

    $delete_num = intval($_REQUEST['number']);

    // Fixed on 2026.08.25
    // - deleted_idの検証に失敗した場合処理を中止
    $deleted_id = FormzuParamHelper::validate_form_id($_REQUEST['id']);
    if ( ! $deleted_id ) {
        return false;
    }

    // Fixed on 2026.08.07
    // - 通知メッセージ内リンクのURLをエスケープ
    $form_url = esc_url('https://ws.formzu.net/fgen/' . rawurlencode($deleted_id));

    // Fixed on 2026.08.07
    // - 通知メッセージ内リンクの出力をエスケープ
    // Fixed on 2026.08.25
    // - デフォルトのフォーム名を追加(「対象フォーム（ID）」)
    // - フォーム名が文字列かどうかの検証を追加
    // - 削除対象のform_data要素が配列であることと、フォームIDが一致することの検証を追加
    // Fixed on 2026.08.27
    // - 削除処理が成功したかどうかを示すフラグを追加

    $form_delete_succeeded = false;

    if (
        isset($form_data[$delete_num])
        && is_array($form_data[$delete_num])
        && isset($form_data[$delete_num]['id'])
        && $form_data[$delete_num]['id'] === $deleted_id
    ) {
        $deleted_form_name = '対象フォーム（' . $deleted_id . '）';
        if ( isset($form_data[$delete_num]['name']) && is_string($form_data[$delete_num]['name']) ) {
            $deleted_form_name = $form_data[$delete_num]['name'];
        }

        $message = '<a href="'
            . $form_url
            . '" target="_blank" rel="noopener noreferrer">'
            . esc_html($deleted_form_name)
            . '</a>'
            . __( ' : 登録を解除しました。', 'formzu-admin');
        set_transient('formzu-admin-updated', $message, 3);

        unset($form_data[$delete_num]);
        $form_data = array_values($form_data);

        $form_delete_succeeded = true;
    }
    else {
        $message = '<a href="'
            . $form_url
            . '" target="_blank" rel="noopener noreferrer">対象フォーム</a>'
            . __('のデータがありませんでした。', 'formzu-admin');
        set_transient('formzu-admin-error', $message, 3);
    }

    // Fixed on 2026.08.27
    // - フォームデータ削除処理が成功した場合のみウィジェットの削除処理を行う
    if ( $form_delete_succeeded ) {
        for ($i = 0, $len = count($form_data); $i < $len; $i++) {
            if ( ! is_array($form_data[$i]) ) {
                continue;
            }
            $form_data[$i]['number'] = $i;
        }
        FormzuOptionHandler::update_option('form_data', $form_data);
        $deleted_keys = delete_from_widget_contents($deleted_id);
        delete_from_sidebars_widgets($deleted_keys);
    }

    wp_safe_redirect( menu_page_url( 'formzu-admin' ) );
    exit;
}


// Fixed on 2026.08.18
// - ウィジェット未作成の場合にoptionがないことでPHP8.5で配列関連のDepricatedが出る不具合を修正
// Fixed on 2026.08.21
// - idが前方一致でなく完全一致した場合のみ削除するように修正
// Fixed on 2026.08.26
// - widget_contentsの各要素の配列型検証を追加
function delete_from_widget_contents($deleted_id) {
    $widget_contents = get_option('widget_formzudefaultwidget', array());
    if ( ! is_array($widget_contents) ) {
        $widget_contents = array();
    }

    $deleted_keys = array();

    foreach ($widget_contents as $key => $value) {
        if (
            ! is_array($value)
            || ! isset($value['form_widget_data'])
            || ! is_string($value['form_widget_data'])
        ) {
            continue;
        }

        $widget_data = explode(' ', $value['form_widget_data'], 3);
        $widget_data_id = $widget_data[0];

        if ( $widget_data_id === $deleted_id ) {
            unset($widget_contents[$key]);
            $deleted_keys[] = $key;
        }
    }
    update_option('widget_formzudefaultwidget', $widget_contents);

    return $deleted_keys;
}


// Fixed on 2026.08.19
// - sidebars_widgetsがない場合のウィジェット削除処理における
//   foreach(false)でのエラーを防止
function delete_from_sidebars_widgets($deleted_keys) {
    $sidebar = get_option('sidebars_widgets', array());
    if ( ! is_array($sidebar) ) {
        $sidebar = array();
    }

    foreach ($deleted_keys as $num) {

        $deleted_widget_id = 'formzudefaultwidget-' . $num;

        foreach ($sidebar as $sidebar_name => $widgets) {
            if ( gettype($widgets) !== 'array' ) {
                continue;
            }
            foreach ($widgets as $num => $widget_id) {
                if ( $widget_id === $deleted_widget_id ) {
                    unset($sidebar[$sidebar_name][$num]);
                }
            }
        }
    }
    update_option('sidebars_widgets', $sidebar);
}
