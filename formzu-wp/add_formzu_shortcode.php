<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

/*
 * Fixed on 2023.12.12 by skagaya
 * - 全体的な表記ゆれの修正
 * - コメントの修正(at -> on)
 */

 function add_formzu_shortcode($atts) {
    $atts = shortcode_atts(array(
        'form_id'       => 'No_form_id',
        'width'         => '600',
        'height'        => '',
        'mobile_height' => '',
        'tagname'       => 'iframe',
        'text'          => '',
        'thickbox'      => 'off',
        'new_window'    => 'off',
        'id'            => '',
        'class'         => '',
        'fixMobileSafari' => TRUE,
    ), $atts, 'formzu');

    $thickbox_on   = FALSE;
    $new_window_on = FALSE;

    global $wp_version;

    if ( version_compare($wp_version, '3.4', '>=') ) {
        $is_mobile = wp_is_mobile();
    } else {
        $is_mobile = FALSE;
    }

    /*
     * Fixed on 2023.12.06 by skagaya
     * - ショートコードのtagname設定におけるXSS脆弱性に対する修正
     * Fixed on 2023.12.12 by skagaya
     * - ショートコードの設定におけるXSS脆弱性に対する修正
     * - コードの整理
     * Fixed on 2023.12.15 by skagaya
     * - height,widthのパーセント値,autoへの対応が抜けていたのを修正
     */
    if ( ! preg_match('/^(S[0-9]{5,9})$/', $atts['form_id']) ) {
        return '<p>' . __('form_idは「S」と5桁以上・9桁以下の半角数字で構成される文字列のみ指定可能です。') . '</p>';
    }

    /*
    if ( ! ctype_digit($atts['width']) ) {
        if ( ! $atts['width'] == '' ) {
            return '<p>' . __('widthは半角数字のみ指定可能です。') . '</p>';
        }
    }

    if ( ! ctype_digit($atts['height']) ) {
        if ( ! $atts['height'] == '' ) {
            return '<p>' . __('heightは半角数字のみ指定可能です。') . '</p>';
        }
    }

    if ( ! ctype_digit($atts['mobile_height']) ) {
        if ( ! $atts['mobile_height'] == '' ) {
            return '<p>' . __('mobile_heightは半角数字のみ指定可能です。') . '</p>';
        }
    }
    */

    if ( isset($atts['width']) && $atts['width'] ) { 
        if ( preg_match('/^([0-9]+([a-zA-Z]{1,5}|%)?|auto)$/', $atts['width']) !== 1 ) {  
            return '<p>' . __('widthは長さ、パーセント値、autoのみ指定可能です。') . '</p>';
        }
    }

    if ( isset($atts['height']) && $atts['height'] ) { 
        if ( preg_match('/^([0-9]+([a-zA-Z]{1,5}|%)?|auto)$/', $atts['height']) !== 1 ) {  
            return '<p>' . __('heightは長さ、パーセント値、autoのみ指定可能です。') . '</p>';
        }
    }

    if ( isset($atts['mobile_height']) && $atts['mobile_height'] ) { 
        if ( preg_match('/^([0-9]+([a-zA-Z]{1,5}|%)?|auto)$/', $atts['mobile_height']) !== 1 ) {  
            return '<p>' . __('mobile_heightは長さ、パーセント値、autoのみ指定可能です。') . '</p>';
        }
    }

    if ( ! in_array($atts['tagname'], array('iframe', 'a')) ) {
        return '<p>' . __('tagnameは"<code>iframe</code>"か"<code>a</code>"のみ指定可能です。') . '</p>';
    }

    $atts['text'] = htmlspecialchars($atts['text'], ENT_QUOTES);

    if ( ! in_array($atts['thickbox'], array('on', 'off')) ) {
        return '<p>' . __('thickboxは"<code>on</code>"か"<code>off</code>"のみ指定可能です。') . '</p>';
    }

    if ( ! in_array($atts['new_window'], array('on', 'off')) ) {
        return '<p>' . __('new_windowは"<code>on</code>"か"<code>off</code>"のみ指定可能です。') . '</p>';
    }

    $atts['id'] = htmlspecialchars($atts['id'], ENT_QUOTES);
    $atts['class'] = htmlspecialchars($atts['class'], ENT_QUOTES);

    /*
     * Fixed on 2023.12.12 by skagaya
     * - 一部コードを移動
     */
    if ( $atts['tagname'] == 'a' && ! $is_mobile ) {
        if ( $atts['thickbox'] == 'on' || strpos( $atts['class'], 'thickbox') !== FALSE ) {
            $thickbox_on = TRUE;
        } elseif ( $atts['new_window'] == 'on' ) {
            $new_window_on = TRUE;
        }
    }

    $opts = array(
        'is_mobile'     => $is_mobile,
        'thickbox_on'   => $thickbox_on,
        'new_window_on' => $new_window_on,
    );

    $height = get_formzu_form_height($atts, $is_mobile);

    $shortcode_format = '<%tagname% %link% %id% %class% %style% %additional_atts% %onload%>%text%</%tagname%>';

    $shortcode_output = str_replace('%tagname%', $atts['tagname'], $shortcode_format);
    $shortcode_output = set_link_to_formzu($atts, $height, $opts, $shortcode_output);
    $shortcode_output = formzu_set_id_of_element($atts['id'], $shortcode_output);
    $shortcode_output = formzu_set_class_of_element($atts['class'], $opts, $shortcode_output);
    $shortcode_output = formzu_set_style_of_element($atts['width'], $height, $opts, $shortcode_output);
    $shortcode_output = formzu_set_additional_atts($atts['tagname'], $atts['form_id'], $height, $opts, $shortcode_output);
    $shortcode_output = str_replace('%text%', $atts['text'], $shortcode_output);

    /*
     * Fixed on 2020.06.23 by skagaya
     * - MacOS版Safariのiframeバグに対応
     * Fixed on 2020.10.26 by skagaya
     * - formzuWpInitialLoadが未定義のまま使用されていたのを修正
     */
    //if (($atts['tagname'] == 'iframe') && $is_mobile && $atts['fixMobileSafari']) {
    if ( ( $atts['tagname'] == 'iframe' ) && ( $is_mobile || is_mac_safari() ) ) {
        $onload_script = 'if (navigator.userAgent.match(/iPhone|iPad|Macintosh/) !== null && formzuWpInitialLoad == true) this.scrollIntoView(true);';
        $shortcode_output = str_replace('%onload%', 'onload="' . $onload_script . '"', $shortcode_output);
        $shortcode_output .= "\n"
                          . '<script>' . "\n"
                          . 'var formzuWpInitialLoad = false;' . "\n"
                          . 'if ( window.addEventListener ) {' . "\n"
                          . '   window.addEventListener( \'load\', formzuInitialSetting, false );' . "\n"
                          . '}' . "\n"
                          . 'else if ( window.attachEvent ) {' . "\n"
                          . '   window.attachEvent( \'onload\', formzuInitialSetting );' . "\n"
                          . '}' . "\n"
                          . 'else {' . "\n"
                          . '   window.onload = formzuInitialSetting;' . "\n"
                          . '}' . "\n"
                          . 'function formzuInitialSetting() {' . "\n"
                          . '   formzuWpInitialLoad = true;' . "\n"
                          . '}' . "\n"
                          . '</script>' . "\n";
    } else {
        $shortcode_output = str_replace('%onload%', '', $shortcode_output);
    }

    return $shortcode_output;
}

/*
 * Added on 2020.06.23
 * - wp_is_mobile()でMacOS版Safariがfalse判定されるため判定用関数を追加
 * Fixed on 2021.07.28
 * - HTTP_USER_AGENTが==で比較されていたのを修正
 */
function is_mac_safari() {
    if ( strstr($_SERVER['HTTP_USER_AGENT'], 'Macintosh') ) {
        return TRUE;
    }

    return FALSE;
}

function get_formzu_form_height($atts, $is_mobile) {
    $height_prop_name = $is_mobile ? 'mobile_height' : 'height';

    if ( empty($atts[$height_prop_name]) ) {
        $height = get_height_from_formzu_option($height_prop_name, $atts['form_id']);
    } else {
        $height = $atts[$height_prop_name];
    }

    if ( ! $height ) {
        $height = $is_mobile ? '900' : '800';
    }

    return $height;
}


function get_height_from_formzu_option($height_prop_name, $form_id) {
    $found_form = FormzuOptionHandler::find_option('form_data', array('id' => $form_id));

    if ( ! $found_form ) {
        return FALSE;
    }

    if ( ! isset($found_form['item'][$height_prop_name]) ) {
        return FALSE;
    }

    return $found_form['item'][$height_prop_name];
}


function set_link_to_formzu($atts, $height, $opts, $format) {
    $link = '';

    if ( $atts['tagname'] === 'a' ) {
        $link = 'href="';
    } elseif ( $atts['tagname'] === 'iframe' ) {
        $link = 'src="';
    }

    if ( $opts['is_mobile'] ) {
        $link .= 'https://ws.formzu.net/sfgen/' . $atts['form_id'];
    } elseif ( $opts['new_window_on'] ) {
        $link = 'href="javascript:void(0)';
    } else {
        $link .= 'https://ws.formzu.net/fgen/' . $atts['form_id'];
    }

    if ( $opts['thickbox_on'] ) {
        $link .= '?wppug=1&TB_iframe=true&width=' . $atts['width'] . '&height=' . $height;
    } elseif ( ! $opts['new_window_on'] ) {
        if ( strpos($link, '?') ) {
            $link .= '&wppug=1';
        } else {
            $link .= '?wppug=1';
        }
    }

    $link .= '"';

    $shortcode_format = str_replace('%link%', $link, $format);

    return $shortcode_format;
}


function formzu_set_id_of_element($elem_id, $format) {
    $id = '';

    if ( ! empty($elem_id) ) {
        $id = ' id="' . $elem_id . '"';
    }

    $shortcode_format = str_replace('%id%', $id, $format);

    return $shortcode_format;
}


function formzu_set_class_of_element($elem_class, $opts, $format) {
    $class = '';

    if ( ! empty($elem_class) ) {
        $class = ' class="' . $elem_class;
    }

    if ( $opts['thickbox_on'] && ! $opts['is_mobile'] ) {
        if ( $class === '' ) {
            $class = ' class="thickbox';
        } else {
            $class .= ' thickbox';
        }
    }

    if ( $class !== '' ) {
        $class .= '"';
    }

    $shortcode_format = str_replace('%class%', $class, $format);

    return $shortcode_format;
}


function formzu_set_style_of_element($width, $height, $opts, $format) {
    $style = '';

    if ( ! $opts['new_window_on'] ) {
        $style = ' style="height: ' . $height . 'px; max-width: ' . $width . 'px; width: 100%; border: 0;"';
    }

    $shortcode_format = str_replace('%style%', $style, $format);

    return $shortcode_format;
}


function formzu_set_additional_atts($tagname, $form_id, $height, $opts, $format) {
    $additional_atts = '';

    if ($tagname === 'a') {
        $additional_atts .= ' target="_blank"';
    }

    if ( ! $opts['is_mobile'] && $opts['new_window_on'] ) {
        $additional_atts .= ' onClick="javascript:window.open(\''
             . 'https://ws.formzu.net/fgen/' . $form_id . '?wppug=1\', '
             . '\'mailform1\', \'toolbar=no, location=no, status=yes, menubar=yes, resizable=yes, scrollbars=yes, '
             . 'width=600, height=' . $height . ', top=100, left=100\')"';
    }

    if ( $opts['thickbox_on'] ) {
        $additional_atts .= ' data-origin-height="' . $height . '"';
    }

    $shortcode_format = str_replace('%additional_atts%', $additional_atts, $format);

    return $shortcode_format;
}
