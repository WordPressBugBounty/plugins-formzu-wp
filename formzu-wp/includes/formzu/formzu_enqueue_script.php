<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function formzu_enqueue_script() {
    wp_enqueue_style(
        'formzu_plugin_formzu',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/css/formzu_plugin_formzu.css' ),
        false,
        filemtime( FORMZU_PLUGIN_PATH . '/css/formzu_plugin_formzu.css' ),
        'all'
    );

    wp_enqueue_style(
        'formzu_plugin_admin',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/css/formzu_plugin_admin.css' ),
        false,
        filemtime( FORMZU_PLUGIN_PATH . '/css/formzu_plugin_admin.css' ),
        'all'
    );

    wp_enqueue_script(
        'formzu_button_actions',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/js/formzu_button_actions.js' ),
        array( 'jquery' ),
        filemtime( FORMZU_PLUGIN_PATH . '/js/formzu_button_actions.js' ),
        true
    );

    wp_enqueue_script('common');
    wp_enqueue_script('wp-lists');
    wp_enqueue_script('postbox');

    global $wp_version;

    wp_localize_script(
        'formzu_button_actions',
        'formzu_ajax_obj',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('get_iframe_height'),
            'action'  => 'get_iframe_height',
            'email'   => get_option('admin_email'),
            'version' => $wp_version,
        )
    );

    ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
    <?php

}

