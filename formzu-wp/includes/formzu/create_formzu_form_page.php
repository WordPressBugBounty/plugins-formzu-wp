<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function create_formzu_form_page() {
    if ( ! FormzuParamHelper::check_referer('formzu_create_page', 'create_page_nonce') ) {
        return false;
    }

    $action = FormzuParamHelper::get_REQ('action', false);

    if ( ! $action ) {
        return false;
    }
    if ( 'create_formzu_page' !== $action ) {
        return false;
    }

    // Added on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: manage_options および edit_pages
    if ( ! current_user_can('manage_options') || ! current_user_can('edit_pages') ) {
        return false;
    }

    if ( ! FormzuParamHelper::isset_key($_REQUEST, array('name', 'id')) ) {
        return false;
    }

    $form_id = FormzuParamHelper::validate_form_id($_REQUEST['id']);

    if ( ! $form_id ) {
        return false;
    }

    // Fixed on 2026.08.26
    // - $_REQUEST['name']が文字列でない場合処理を中断
    // - $form_nameが空文字の場合処理を中断
    if ( ! is_string($_REQUEST['name']) ) {
        return false;
    }
    $form_name = sanitize_text_field(wp_unslash($_REQUEST['name']));
    if ( $form_name === '' ) {
        return false;
    }

    $post_data = array(
        'post_title'   => $form_name,
        'post_content' => '[formzu form_id="' . $form_id . '" tagname="iframe"]',
        'post_name'    => $form_id,
        'post_type'    => 'page',
    );
    $post_id = wp_insert_post($post_data, true);

    // Fixed on 2026.08.19
    // - 固定ページの作成に失敗した場合のWP_Error処理を追加
    if ( is_wp_error($post_id) || ! is_int($post_id) || $post_id <= 0 ) {
        set_transient('formzu-admin-error', '固定ページの作成に失敗しました。', 3);
        wp_safe_redirect(menu_page_url('formzu-admin', false));
        exit;
    }

    $url_atts = 'post=' . $post_id . '&action=edit';
    $url      = admin_url('post.php') . '?' . sanitize_text_field($url_atts);

    wp_safe_redirect($url);
    exit;
}
