<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function formzu_howtouse_enqueue_script() {
    wp_enqueue_style(
        'formzu_plugin_howtouse',
        plugins_url( FORMZU_PLUGIN_BASEDIR . '/css/formzu_plugin_howtouse.css' ),
        false,
        filemtime( FORMZU_PLUGIN_PATH . '/css/formzu_plugin_howtouse.css' ),
        'all'
    );
}

