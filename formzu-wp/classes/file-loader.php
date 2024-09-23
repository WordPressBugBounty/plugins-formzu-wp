<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class FormzuFileLoader
{
    private function __construct()
    {
    }


    public static function load_files_for_all()
    {
        require_once FORMZU_PLUGIN_PATH . '/classes/option-handler.php';
        require_once FORMZU_PLUGIN_PATH . '/classes/param-helper.php';
        require_once FORMZU_PLUGIN_PATH . '/add_formzu_shortcode.php';
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

        require_once FORMZU_PLUGIN_PATH . '/includes/formzu-default-widget.php';
    }


    private static function _load_directory($directory_name)
    {
        if ( ! isset($directory_name) ) {
            return false;
        }
        foreach (glob(FORMZU_PLUGIN_PATH . '/includes/' . $directory_name . '/*.php') as $filename) {
            require_once $filename;
        }
        return true;
    }


    public static function load_files_for_formzu()
    {
        self::_load_directory('formzu');
    }


    public static function load_files_for_howtouse()
    {
        self::_load_directory('howtouse');
    }


    public static function load_files_for_widget_menu()
    {
        self::_load_directory('widgetmenu');
    }


    public static function load_files_for_nav_menu()
    {
        self::_load_directory('navmenu');
    }


    public static function load_files_for_admin()
    {
        self::_load_directory('admin');
    }


    public static function load_files_for_client()
    {
        self::_load_directory('client');
    }

}

