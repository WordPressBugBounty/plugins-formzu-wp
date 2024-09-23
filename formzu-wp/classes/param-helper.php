<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class FormzuParamHelper
{
    private function __construct()
    {
    }


    public static function have_value( $arg )
    {
        if (isset($arg)) {
            return true;
        }
        return false;
    }


    public static function isset_key( $data, $name )
    {
        if ( ! isset($data) || ! $data ) {
            return false;
        }
        if ( ! is_array($data) ) {
            return false;
        }
        if (is_array($name)) {
            $not_exist = FALSE;
            for ($i = 0, $l = count($name); $i < $l; $i++) {
                if ( ! array_key_exists($name[$i], $data) || ! isset($data[$name[$i]]) ) {
                    $not_exist = TRUE;
                    break;
                }
            }
            if ($not_exist) {
                return false;
            }
        }
        else {
            if ( ! array_key_exists($name, $data) || ! isset($data[$name]) ) {
                return false;
            }
        }
        return true;
    }


    public static function get_value_of_key( $data, $name )
    {
        if (self::isset_key($data, $name)) {
            return $data[$name];
        }
        return null;
    }


    public static function return_val_or_def( $data, $name, $default )
    {
        $value = self::get_value_of_key($data, $name);

        if ( ! $value && $value !== 0 && self::have_value($default) ) {
            $value = $default;
        }
        return $value;
    }


    public static function get_GET( $name, $default )
    {
        return self::return_val_or_def($_GET, $name, $default);
    }


    public static function get_POS( $name, $default )
    {
        return self::return_val_or_def($_POST, $name, $default);
    }


    public static function get_REQ( $name, $default )
    {
        return self::return_val_or_def($_REQUEST, $name, $default);
    }


    public static function is_equal( $data, $value )
    {
        if ($data !== $value) {
            return false;
        }
        return true;
    }


    public static function is_admin_page_of( $page = 'formzu-admin' )
    {
        global $pagenow;

        if ($pagenow && $pagenow != 'admin.php') {
            return false; 
        }

        /*
         * Fixed on 2024.02.26 by skagaya
         * - "Warning: Undefined array key"に対する修正
         *   (一部テーマのCSSキャッシュ削除時にこのエラーが発生)
         */
        if ( isset($_GET['page']) == false ) {
            return false;
        }

        $page_val = sanitize_title($_GET['page']);

        if ($page_val !== $page) {
            return false;
        }
        if ( ! is_admin() ) {
            return false;
        }
        return true;
    }

    public static function is_true( $value )
    {
        if ( $value === true 
            || $value == 1
            || $value == 'true') {
            return true;
        }
        return false;
    }

    public static function has_values( $args )
    {
        //if ( ! is_array($args) ) {
            //singleVar or Object
        //}

        $dont_has = FALSE;

        foreach ($args as $arg) {
            if ( ! self::have_value($arg) ) {
                $dont_has = TRUE;
                break;
            }
        }
        if ($dont_has) {
            return false;
        }
        return true;
    }

    public static function check_referer( $nonce_action, $nonce_key )
    {
        if ( ! self::isset_key($_REQUEST, $nonce_key) ) {
            return false;
        }

        //$nonce = filter_input(INPUT_GET, $nonce_key, FILTER_SANITIZE_STRING);
        //$nonce = filter_input(INPUT_POST, $nonce_key, FILTER_SANITIZE_STRING);

        if ( ! wp_verify_nonce($_REQUEST[$nonce_key], $nonce_action)) {
            return false;
        }
        return true;
    }

    public static function validate_form_id( $form_id )
    {
        if ( ! $form_id ) {
            return false;
        }

        preg_match("/^S[0-9]{3,9}/", $form_id, $matches);

        if ( ! $matches) {
            return false;
        }
        return $matches[0];
    }

}

