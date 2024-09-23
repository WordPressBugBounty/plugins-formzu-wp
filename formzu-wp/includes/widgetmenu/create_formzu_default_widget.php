<?php

function create_formzu_default_widget() {
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('id', 'name', 'action')) ) {
        return false;
    }

    if ( $_REQUEST['action'] != 'create_formzu_default_widget' ) {
        return false;
    }

    $form_id     = FormzuParamHelper::validate_form_id($_REQUEST['id']);
    $widget_name = sanitize_text_field(strval($_REQUEST['name']));

    if ( ! $form_id || ! $widget_name ) {
        return false;
    }
    if ( ! FormzuParamHelper::check_referer('create_formzu_default_widget-' . $form_id, 'create_widget_nonce') ) {
        return false;
    }


    $active_widgets = get_option('sidebars_widgets');
    $sidebar_name = null;

    if ( array_key_exists('sidebar-1', $active_widgets) ) {
        $sidebar_name = 'sidebar-1';
    }
    else{
        foreach ($active_widgets as $sidebar_key => $sidebar_value) {
            if ($sidebar_key != 'wp_inactive_widgets' and $sidebar_key != 'array_version') {
                $sidebar_name = $sidebar_key;
                break;
            }
        }
    }


    $widget_contents = get_option('widget_formzudefaultwidget');
    $widget_counter = 2;

    if ( $widget_contents ) {
        $widget_contents_keys = array_keys($widget_contents);
        $widget_keys_max = max($widget_contents_keys);
        if ( is_numeric($widget_keys_max) ) {
            $widget_counter += $widget_keys_max;
        }
    }


    $form_data = FormzuOptionHandler::find_option('form_data', array('id' => $form_id));

    if ( ! $form_data ) {
        return false;
    }
    if ( ! isset($form_data['item']['height']) or ! isset($form_data['item']['mobile_height']) ) {
        return false;
    }


    $height        = $form_data['item']['height'];
    $mobile_height = $form_data['item']['mobile_height'];
    $widget_id     = 'formzudefaultwidget-' . $widget_counter;

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

    set_transient( 'formzu-admin-updated', __( $widget_name . 'のウィジェットを作成しました。', 'formzu-admin' ), 3 );

    $url_atts = 'action=created_formzu_widget&widget_id=' . $widget_id . '&id=' . $form_id;
    $url      = admin_url('widgets.php') . '?' . sanitize_text_field($url_atts);

    wp_safe_redirect($url);
    exit;
}

