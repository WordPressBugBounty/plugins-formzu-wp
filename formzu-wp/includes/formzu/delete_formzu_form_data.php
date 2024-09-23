<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function delete_formzu_form_data() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('action', 'delete_nonce', 'id', 'number')) ) {
        return false;
    }

    $action = FormzuParamHelper::get_REQ('action', false);

    if ( 'delete_form' != $action ) {
        return false;
    }
    if ( ! FormzuParamHelper::check_referer('delete_form-' . $_REQUEST['id'], 'delete_nonce') ) {
        return false;
    }

    $form_data  = FormzuOptionHandler::get_option('form_data');
    $form_data  = array_values( $form_data );
    $delete_num = intval($_REQUEST['number']);
    $deleted_id = FormzuParamHelper::validate_form_id($_REQUEST['id']);

    if ( isset($form_data[$delete_num]['id']) && $form_data[$delete_num]['id'] == $deleted_id ) {
        set_transient( 'formzu-admin-updated', '<a href="https://ws.formzu.net/fgen/' . $deleted_id . '/" target="_blank">'. $form_data[$delete_num]['name'] . '</a>' . __( ' : 登録を解除しました。'), 3 );

        unset( $form_data[$delete_num] );
        $form_data = array_values( $form_data );

    }
    else {
        set_transient( 'formzu-admin-error', '<a href="https://ws.formzu.net/fgen/' . $deleted_id . '/" target="_blank">対象フォーム</a>' . __( ' のデータがありませんでした。'), 3 );
    }
    for ($i = 0, $len = count($form_data); $i < $len; $i++) {

        $form_data[$i]['number'] = $i;

    }
    FormzuOptionHandler::update_option('form_data', $form_data);


    $deleted_keys = delete_from_widget_contents($deleted_id);

    delete_from_sidebars_widgets($deleted_keys);


    wp_safe_redirect( menu_page_url( 'formzu-admin' ) );
    exit;
}


function delete_from_widget_contents($deleted_id) {
    $widget_contents = get_option('widget_formzudefaultwidget');
    $deleted_keys = array();

    foreach ($widget_contents as $key => $value) {
        if ( ! isset($value['form_widget_data']) ) {
            continue;
        }

        $w_data = $value['form_widget_data'];

        if ( strpos($w_data, $deleted_id) !== false ) {
            unset( $widget_contents[$key] );
            $deleted_keys[] = $key;
        }
    }
    update_option('widget_formzudefaultwidget', $widget_contents);

    return $deleted_keys;
}


function delete_from_sidebars_widgets($deleted_keys) {
    $sidebar = get_option('sidebars_widgets');

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

