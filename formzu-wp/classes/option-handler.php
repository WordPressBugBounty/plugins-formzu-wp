<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class FormzuOptionHandler
{
    private function __construct()
    {
    }


    public static function get_option( $name, $default = FALSE )
    {
        $options = get_option('formzu_option_data');

        if ($options === false) {
            return $default;
        }
        if (isset($options[$name])) {
            return $options[$name];
        }
        return $default;
    }


    public static function update_option( $name, $value, $do_push = FALSE )
    {
        $options = get_option('formzu_option_data');

        if ($options === false || ! is_array($options) ) {
            $options = array();
        }
        if ( ! isset($option[$name]) ) {
            if ( $do_push ) {
                $options[$name] = array($value);
            }
            else {
                $options[$name] = $value;
            }
            update_option('formzu_option_data', $options);
            return true;
        }
        if ( is_array($options[$name]) ) {
            if ( $do_push ) {
                $options[$name][] = $value;
            }
            else {
                $options[$name] = $value;
            }
            update_option('formzu_option_data', $options);
            return true;
        }
        if ( $do_push ) {
            return false;
        }
        $options[$name] = $value;
        update_option('formzu_option_data', $options);
        return true;
    }


    public static function has_option( $name, $value )
    {
        $options = get_option('formzu_option_data');

        if ($option === false) {
            return false;
        }
        if ( isset($options[$name]) && ! $options[$name] ) {
            return true;
        }
        return false;
    }


    public static function find_option( $item_key, $search_query, $match_all_query = FALSE )
    {
        $options = get_option('formzu_option_data');

        if ( ! is_array($search_query) ) {
            $search_query = (array) $search_query;
        }

        $items = $options[$item_key];
        $item;

        if ( ! is_array($items) ) {
            return false;
        }

        for ($i = 0, $l = count($items); $i < $l; $i++) {
            foreach ($search_query as $query_key => $query_value) {
                if ( isset($items[$i][$query_key]) && $items[$i][$query_key] == $query_value ) {
                    $item = $items[$i];
                    if ( ! $match_all_query ) {
                        break 2;
                    }
                }
                else {
                    $item = null;
                }
            }
            if ( ! empty($item) && $match_all_query ) {
                break;
            }
        }
        if ( isset($item) ) {
            return array(
                'item'  => $item,
                'index' => $i,
            );
        }
        return false;
    }


    public static function delete_option( $name )
    {
        $options = get_option('formzu_option_data');

        if ($options === false) {
            return false;
        }
        if (isset($options[$name])) {
            unset($options[$name]);
            //$options = array_values($options);
        }
        update_option('formzu_option_data', $options);
    }
}

