<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class FormzuNavMenuItemFields
{
    private function __construct()
    {
    }

    static $options = array();


    static function setup()
    {
        self::$options['fields'] = array(
            'form_id' => 'form_id',
        );

        add_action('save_post', array(__CLASS__, '_save_post'));
    }


    static function get_fields_schema()
    {
        $schema = array();

        foreach (self::$options['fields'] as $name => $field) {
            $schema[] = $field;
        }
        return $schema;
    }


    static function get_menu_item_postmeta_key($name)
    {
        return '_menu_item_' . $name;
    }


    // Fixed on 2026.08.27
    // - 現状フック登録されていないためコメントアウト
    /*
    static function _add_fields($new_fields, $item_output, $item, $depth, $args)
    {
        $schema = self::get_fields_schema();

        foreach ($schema as $field) {

            $field['value'] = get_post_meta($item->ID, self::get_menu_item_postmeta_key($field['name']), true);

            $keys = array();

            foreach (array_keys($field) as $key) {
                $keys[] = '{' . $key . '}';
            }
            $new_fields .= str_replace(
                $keys,
                array_values(array_map('esc_attr', $field)),
                self::$options['item']
            );

        }
        return $new_fields;
    }
    */


    // Fixed on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: edit_theme_options
    // Fixed on 2026.08.21
    // - menu-item-urlおよびmenu-item-objectの検証方法を修正
    // - urlのサニタイジング方法を修正
    static function _save_post($post_id)
    {
        if ( ! current_user_can('edit_theme_options') ) {
            return;
        }

        if ( get_post_type($post_id) !== 'nav_menu_item' ) {
            return;
        }

        if (
            ! isset($_POST['menu-item-url'][$post_id])
            || ! is_string($_POST['menu-item-url'][$post_id])
        ) {
            return;
        }

        if (
            ! isset($_POST['menu-item-object'][$post_id])
            || ! is_string($_POST['menu-item-object'][$post_id])
        ) {
            return;
        }

        $menu_item_object = wp_unslash($_POST['menu-item-object'][$post_id]);
        if ( $menu_item_object !== 'post_type_formzu_link' ) {
            return;
        }

        $menu_item_url = esc_url_raw(
            wp_unslash($_POST['menu-item-url'][$post_id]),
            array('http', 'https')
        );

        if ( empty($menu_item_url) ) {
            return;
        }

        update_post_meta($post_id, '_menu_item_url',    $menu_item_url);
        update_post_meta($post_id, '_menu_item_type',   'formzu_link');
        update_post_meta($post_id, '_menu_item_object', 'post_type_formzu_link');
    }
}
