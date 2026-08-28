<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class FormzuActionHooks
{
    private function __construct()
    {
    }


    public static function branch_action_hooks_by_slugname( $slug_list, $load_slug_list )
    {
        foreach ($slug_list as $key => $value) {
            if ( FormzuParamHelper::is_admin_page_of($value) ) {
                call_user_func( array(__CLASS__, 'add_actions_' . $key), $load_slug_list[$key]);
                break;
            }
        }
    }


    public static function register_actions()
    {
        self::add_actions_for_all();
        if ( ! is_admin() ) {
            self::add_actions_for_client();
            return;
        }
        global $pagenow;
        self::add_actions_for_admin();
        if ( ! $pagenow ) {
            return false;
        }
        if ( $pagenow === 'widgets.php' ) {
            self::add_actions_widget_menu();
        }
        elseif ( $pagenow === 'nav-menus.php' ) {
            self::add_actions_nav_menu();
        }
        elseif ( $pagenow === 'plugins.php' ) {
            require_once FORMZU_PLUGIN_PATH . '/includes/introduce-tour.php';
            add_action('admin_enqueue_scripts', array('Formzu_Plugin_Tour', 'get_instance'));
        }
    }


    public static function add_actions_for_all()
    {
        FormzuFileLoader::load_files_for_all();
        add_shortcode( 'formzu',    'add_formzu_shortcode' );

        add_action( 'widgets_init', 'register_formzu_default_widgets' );
    }


    public static function add_actions_for_admin()
    {
        FormzuFileLoader::load_files_for_admin();

        add_action( 'formzu_loaded_menu', array('FormzuActionHooks', 'branch_action_hooks_by_slugname'), 10, 2 );
        add_action( 'admin_menu', 'formzu_admin_menu' );
        add_action( 'admin_enqueue_scripts', 'formzu_admin_enqueue_script' );
        add_action( 'wp_ajax_formzu_get_iframe_height', 'formzu_get_iframe_height' );
        add_action( 'wp_ajax_' . FORMZU_NAVMENU_NONCE, FORMZU_NAVMENU_NONCE );

    }


    public static function add_actions_for_client()
    {
        FormzuFileLoader::load_files_for_client();

        add_action( 'wp_enqueue_scripts', 'formzu_client_enqueue_script' );
    }


    public static function add_actions_formzu( $formzu )
    {
        FormzuFileLoader::load_files_for_formzu();

        add_action( 'load-' . $formzu, 'delete_formzu_form_data' );
        add_action( 'load-' . $formzu, 'create_formzu_form_page' );
        add_action( 'load-' . $formzu, 'add_formzu_context_option_box' );
        add_action( 'load-' . $formzu, array('Formzu_Plugin_Tour', 'get_instance') );

        add_action( 'admin_init', 'add_new_formzu_form' );
        add_action( 'admin_init', 'reload_formzu_form' );
        add_action( 'admin_footer', 'show_added_formzu_form' );
        add_action( 'admin_notices', 'formzu_admin_notices' );
        add_action( 'admin_enqueue_scripts', 'formzu_enqueue_script' );
    }


    public static function add_actions_howtouse( $howtouse )
    {
        FormzuFileLoader::load_files_for_howtouse();

        add_action( 'load-' . $howtouse, array('Formzu_Plugin_Tour', 'get_instance') );
        add_action( 'admin_enqueue_scripts', 'formzu_howtouse_enqueue_script' );
    }


    public static function add_actions_widget_menu()
    {
        FormzuFileLoader::load_files_for_widget_menu();

        add_action( 'admin_footer', 'show_created_formzu_widget' );

        add_action( 'widgets_init', 'create_formzu_default_widget' );

        add_action( 'admin_enqueue_scripts', 'formzu_widgetmenu_enqueue_script' );
        add_action( 'admin_notices', 'formzu_admin_notices' );
    }


    public static function add_actions_nav_menu()
    {
        FormzuFileLoader::load_files_for_nav_menu();

        add_action( 'load-nav-menus.php', 'add_formzu_navmenu_metabox' );
        add_action( 'admin_enqueue_scripts', 'formzu_navmenu_enqueue_script' );

        add_filter( 'wp_setup_nav_menu_item', 'setup_formzu_link_navmenu' );
        add_action( 'init', array('FormzuNavMenuItemFields', 'setup') );
    }


    public static function add_activation_actions()
    {
        add_action('admin_enqueue_scripts', array('Formzu_Plugin_Tour', 'get_instance'));

        require_once FORMZU_PLUGIN_PATH . '/formzu_alert_ie_browser_version.php';
        add_action('admin_enqueue_scripts', 'formzu_alert_ie_browser_version');

        $key   = 'closedpostboxes_toplevel_page_formzu-admin';
        $value = array('formzu-create-box', 'formzu-add-box', 'formzu-list-box');

        add_user_meta(1, $key, $value);
        add_metadata('user', 0, $key, $value);
    }


    public static function add_deactivation_actions()
    {
        // Fixed on 2021.10.13 (Comment modified on 2026.08.14)
        // - プラグイン無効化時にエラーになるためif文を修正(get_class() -> class_exists()に変更)
        if ( ! class_exists('Formzu_Plugin_Tour') ) {
            require_once FORMZU_PLUGIN_PATH . '/includes/introduce-tour.php';
        }
        Formzu_Plugin_Tour::reset_closing_tour();

        // Fixed on 2026.08.19
        // - プラグイン無効化時にforeach(false)でのエラーを防止するため、デフォルト値に空配列を設定
        $active_widgets = get_option('sidebars_widgets', array());
        if ( ! is_array($active_widgets) ) {
            $active_widgets = array();
        }

        // Fixed on 2021.10.13
        // - count関数にint型変数を設定するとValueErrorになる(PHP8以降)ため、キー'array_version'の要素を除外
        // Fixed on 2026.08.26
        // - $active_widgetsのキー($active_key), サイドバー($active_space), ウィジェットID($active_space[$i])の型検証を追加
        // Fixed on 2026.08.27
        // - $active_spaceのインデックスを連番にする処理を追加(Undefined array keyを回避)
        foreach ($active_widgets as $active_key => $active_space) {
            if ( ! is_string($active_key) ) {
                continue;
            }

            if ( $active_key === 'array_version' ) {
                continue;
            }

            if ( ! is_array($active_space) ) {
                continue;
            }

            $active_space = array_values($active_space);

            for ($i = 0, $len = count($active_space); $i < $len; $i++) {
                if ( ! is_string($active_space[$i]) ) {
                    continue;
                }

                // if ( strpos($active_space[$i], 'formzu') !== false ) {
                if ( preg_match('/^formzudefaultwidget-[0-9]+$/D', $active_space[$i]) === 1 ) {
                    // unset($active_widgets[$active_key][$i]);
                    unset($active_space[$i]);
                }
            }

            $active_widgets[$active_key] = array_values($active_space);
        }

        update_option('sidebars_widgets', $active_widgets);

        $page = 'toplevel_page_formzu-admin';

        delete_metadata('user', 0, 'closedpostboxes_' . $page, '', true);
        delete_metadata('user', 0, 'meta-box-order_' . $page, '', true);
    }
}
