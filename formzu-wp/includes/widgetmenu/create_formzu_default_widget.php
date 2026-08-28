<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function create_formzu_default_widget() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('id', 'name', 'action')) ) {
        return false;
    }

    if ( $_REQUEST['action'] !== 'create_formzu_default_widget' ) {
        return false;
    }

    // Added on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: manage_options および edit_theme_options
    if ( ! current_user_can('manage_options') || ! current_user_can('edit_theme_options') ) {
        return false;
    }

    $form_id = FormzuParamHelper::validate_form_id($_REQUEST['id']);

    // Added on 2026.08.19
    // - Array to string conversionの抑止
    if ( ! is_string($_REQUEST['name']) ) {
        return false;
    }
    $widget_name = sanitize_text_field(strval($_REQUEST['name']));

    if ( ! $form_id || ! $widget_name ) {
        return false;
    }
    if ( ! FormzuParamHelper::check_referer('create_formzu_default_widget-' . $form_id, 'create_widget_nonce') ) {
        return false;
    }


    // Fixed on 2026.08.19
    // - サイドバーがない場合は非アクティブなウィジェット(wp_inactive_widgets)へ追加
    // - sidebars_widgetsの型を検証
    // Fixed on 2026.08.26
    // - サイドバーの各要素のキーが文字列かつ空文字でないことを検証
    $active_widgets = get_option('sidebars_widgets', array());
    if ( ! is_array($active_widgets) ) {
        $active_widgets = array();
    }

    $sidebar_name = 'wp_inactive_widgets';

    if ( isset($active_widgets['sidebar-1']) && is_array($active_widgets['sidebar-1']) ) {
        $sidebar_name = 'sidebar-1';
    }
    else {
        $sidebar_keys = array_keys($active_widgets);

        for ($i = 0, $l = count($sidebar_keys); $i < $l; $i++) {
            $sidebar_key = $sidebar_keys[$i];

            if ( ! is_string($sidebar_key) || empty($sidebar_key) ) {
                continue;
            }

            if ( $sidebar_key === 'wp_inactive_widgets' || $sidebar_key === 'array_version' ) {
                continue;
            }
            if ( ! is_array($active_widgets[$sidebar_key]) ) {
                continue;
            }

            $sidebar_name = $sidebar_key;
            break;
        }
    }

    if ( ! isset($active_widgets[$sidebar_name]) || ! is_array($active_widgets[$sidebar_name]) ) {
        $active_widgets[$sidebar_name] = array();
    }

    // Fixed on 2026.08.18
    // - ウィジェット作成時に既存ウィジェットを上書きする可能性のある不具合を修正
    // - ウィジェット未登録の場合にoptionがないことでPHP8.5で配列関連のDeprecatedが出る不具合を修正
    // Fixed on 2026.08.27
    // - $widget_counterが異常な値の場合に処理を中止するように修正
    $widget_contents = get_option('widget_formzudefaultwidget', array());
    if ( ! is_array($widget_contents) ) {
        $widget_contents = array();
    }

    $widget_counter = 2;

    $widget_keys = array_keys($widget_contents);
    for ($i = 0, $l = count($widget_keys); $i < $l; $i++) {
        $key = $widget_keys[$i];

        $numeric_key = 0;
        if ( is_int($key) ) {
            $numeric_key = $key;
        }
        elseif ( is_string($key) && ctype_digit($key) ) {
            $numeric_key = intval($key);
        }
        else {
            continue;
        }

        if ( $numeric_key >= $widget_counter ) {
            $widget_counter = $numeric_key + 1;
        }

        if ( $widget_counter >= PHP_INT_MAX ) {
            break;
        }
    }

    if ( $widget_counter >= PHP_INT_MAX ) {
        return false;
    }

    // Fixed on 2026.08.25
    // - 保存済みの高さについて正の整数かどうかの検証を追加
    // - 取得できない、もしくは不正な値の場合は処理を中止
    $height = get_height_from_formzu_option('height', $form_id);
    $mobile_height = get_height_from_formzu_option('mobile_height', $form_id);
    if ( $height === false || $mobile_height === false ) {
        return false;
    }

    $widget_id = 'formzudefaultwidget-' . $widget_counter;

    $active_widgets[$sidebar_name][]  = $widget_id;
    $widget_contents[$widget_counter] = array(
        'form_widget_data' => sprintf('%1$s %2$s %3$s', $form_id, $height, $mobile_height),
        'title'            => '',
        'form_text'        => $widget_name,
        'form_position'    => 'normal',
        'form_plan'        => 'modal_window',
    );

    update_option('widget_formzudefaultwidget', $widget_contents);
    update_option('sidebars_widgets',           $active_widgets);

    // Fixed on 2026.08.07
    // - ウィジェット名をエスケープ
    set_transient('formzu-admin-updated', __(esc_html($widget_name) . 'のウィジェットを作成しました。', 'formzu-admin'), 3);

    $url_atts = 'action=created_formzu_widget&widget_id=' . $widget_id . '&id=' . $form_id;
    $url = admin_url('widgets.php') . '?' . sanitize_text_field($url_atts);

    wp_safe_redirect($url);
    exit;
}
