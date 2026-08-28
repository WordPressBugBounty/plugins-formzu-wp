<?php
// Fixed on 2026.08.17
// - true,falseの表記を小文字に統一

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class FormzuOptionHandler
{
    private function __construct()
    {
    }


    // Fixed on 2026.08.25
    // - formzu_option_dataが配列かどうかの検証を追加
    public static function get_option( $name, $default = false )
    {

        $options = get_option('formzu_option_data', array());

        if ( ! is_array($options) ) {
            return $default;
        }
        if ( isset($options[$name]) ) {
            return $options[$name];
        }

        return $default;
    }


    public static function update_option( $name, $value, $do_push = false )
    {
        $options = get_option('formzu_option_data');

        if ( $options === false || ! is_array($options) ) {
            $options = array();
        }
        if ( ! isset($options[$name]) ) {
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


    // Fixed on 2026.08.20
    // - 使用していなかった引数($value)を削除
    // Fixed on 2026.08.25
    // - formzu_option_dataが配列かどうかの検証を追加
    public static function has_option( $name )
    {
        $options = get_option('formzu_option_data', array());

        if ( ! is_array($options) ) {
            return false;
        }
        return isset($options[$name]);
    }


    // Fixed on 2026.08.14
    // - item_key, optionsのチェックを追加(get_option()がfalseを返す場合への対応)
    public static function find_option( $item_key, $search_query, $match_all_query = false )
    {
        $options = get_option('formzu_option_data');

        if ( ! is_string($item_key) || empty($item_key) ) {
            return false;
        }

        if ( ! is_array($options) || ! isset($options[$item_key]) || ! is_array($options[$item_key]) ) {
            return false;
        }

        if ( ! is_array($search_query) ) {
            $search_query = (array) $search_query;
        }

        // Fixed on 2026.08.25
        // - options[$item_key]の値をarray_values()で取り出し
        //   飛び飛びになっている配列のindexを詰める
        $items = array_values($options[$item_key]);
        $item = null;

        for ($i = 0, $l = count($items); $i < $l; $i++) {
            if ( ! is_array($items[$i]) ) {
                continue;
            }

            foreach ($search_query as $query_key => $query_value) {
                if ( isset($items[$i][$query_key]) && $items[$i][$query_key] === $query_value ) {
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


    // Fixed on 2026.08.25
    // - formzu_option_dataが配列かどうかの検証を追加
    public static function delete_option( $name )
    {
        $options = get_option('formzu_option_data', array());

        if ( ! is_array($options) ) {
            return false;
        }
        if ( isset($options[$name]) ) {
            unset($options[$name]);
        }
        update_option('formzu_option_data', $options);
    }
}
