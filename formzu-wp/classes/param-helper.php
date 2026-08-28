<?php
// Fixed on 2026.08.17
// - true,falseの表記を小文字に統一

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
        if ( isset($arg) ) {
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
        if ( is_array($name) ) {
            $not_exist = false;
            for ($i = 0, $l = count($name); $i < $l; $i++) {
                if ( ! array_key_exists($name[$i], $data) || ! isset($data[$name[$i]]) ) {
                    $not_exist = true;
                    break;
                }
            }
            if ( $not_exist ) {
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
        if ( self::isset_key($data, $name) ) {
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
        if ( $data !== $value ) {
            return false;
        }
        return true;
    }


    public static function is_admin_page_of( $page = 'formzu-admin' )
    {
        global $pagenow;

        if ( $pagenow && $pagenow !== 'admin.php' ) {
            return false;
        }

        // Fixed on 2024.02.26
        // - "Warning: Undefined array key"に対する修正
        //   (一部テーマのCSSキャッシュ削除時にこのエラーが発生)
        // Fixed on 2026.08.18
        // - sanitize_title内のpreg_matchで発生するTypeErrorを防止
        if ( ! isset($_GET['page']) || ! is_string($_GET['page']) ) {
            return false;
        }

        $page_val = sanitize_title(wp_unslash($_GET['page']));

        if ( $page_val !== $page ) {
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
            || $value === 'true'
            || $value === 'TRUE'
            || $value === 1
            || $value === '1'
        ) {
            return true;
        }
        return false;
    }


    public static function has_values( $args )
    {
        $dont_has = false;

        foreach ($args as $arg) {
            if ( ! self::have_value($arg) ) {
                $dont_has = true;
                break;
            }
        }
        if ( $dont_has ) {
            return false;
        }
        return true;
    }


    // Fixed on 2026.08.18
    // - wp_verify_nonceにおけるWarning(Array to string conversion)を防止
    public static function check_referer( $nonce_action, $nonce_key )
    {
        if ( ! self::isset_key($_REQUEST, $nonce_key) ) {
            return false;
        }

        if ( ! is_string($_REQUEST[$nonce_key]) ) {
            return false;
        }

        $nonce = sanitize_text_field(wp_unslash($_REQUEST[$nonce_key]));
        if ( ! wp_verify_nonce($nonce, $nonce_action) ) {
            return false;
        }

        return true;
    }


    // Fixed on 2026.08.17
    // - フォームIDが文字列以外の場合にpreg_matchで発生するTypeErrorを防止
    // - フォームIDを部分一致から完全一致で検証するように変更
    // Fixed on 2026.08.18
    // - フォームIDの桁数を5～9桁に修正
    public static function validate_form_id( $form_id )
    {
        if ( ! is_string($form_id) ) {
            return false;
        }

        if ( preg_match("/^S[0-9]{5,9}$/D", $form_id) !== 1 ) {
            return false;
        }

        return $form_id;
    }
}
