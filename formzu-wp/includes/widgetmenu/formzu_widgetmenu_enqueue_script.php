<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function formzu_widgetmenu_enqueue_script() {
    wp_enqueue_style(
        'formzu_plugin_admin',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/css/formzu_plugin_admin.css' ),
        false,
        filemtime( FORMZU_PLUGIN_PATH . '/css/formzu_plugin_admin.css' ),
        'all'
    );
}

