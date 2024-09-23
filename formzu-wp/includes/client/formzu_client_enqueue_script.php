<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function formzu_client_enqueue_script() {
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');

    wp_enqueue_style(
        'formzu_plugin_client',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/css/formzu_plugin_client.css' ),
        false,
        filemtime( FORMZU_PLUGIN_PATH . '/css/formzu_plugin_client.css' ),
        'all'
    );

    wp_enqueue_script(
        'trim_formzu_fixed_widget_layer',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/js/trim_formzu_fixed_widget_layer.js' ),
        array( 'jquery' ),
        filemtime( FORMZU_PLUGIN_PATH . '/js/trim_formzu_fixed_widget_layer.js' ),
        true
    );

    wp_enqueue_script(
        'formzu_resize_thickbox',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/js/formzu_resize_thickbox.js' ),
        array( 'jquery' ),
        filemtime( FORMZU_PLUGIN_PATH . '/js/formzu_resize_thickbox.js' ),
        true
    );
}

