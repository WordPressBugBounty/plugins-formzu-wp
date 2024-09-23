<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function create_formzu_form_page(){
    if ( ! FormzuParamHelper::check_referer('formzu_create_page', 'create_page_nonce')) {
        return false;
    }

    $action = FormzuParamHelper::get_REQ('action', false);

    if (!$action) {
        return false;
    }
    if ('create_formzu_page' != $action) {
        return false;
    }
    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('name', 'id')) ) {
        return false;
    }

    $form_id = FormzuParamHelper::validate_form_id($_REQUEST['id']);

    if ( ! $form_id ) {
        return false;
    }

    $form_name = sanitize_text_field($_REQUEST['name']);
    $post_data = array(
        'post_title'   => $form_name,
        'post_content' => '[formzu form_id="' . $form_id . '" tagname="iframe"]',
        'post_name'    => $form_id,
        'post_type'    => 'page',
    );
    $post_id = wp_insert_post( $post_data, true );

    $url_atts = 'post=' . $post_id . '&action=edit';
    $url      = admin_url('post.php') . '?' . sanitize_text_field($url_atts);

    wp_safe_redirect($url);
    exit;
}

