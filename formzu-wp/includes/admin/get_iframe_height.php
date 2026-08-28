<?php
// Fixed on 2026.08.17
// - true,falseの表記を小文字に統一

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


// Fixed on 2026.08.12
// - フォームページの取得方法を変更
// Fixed on 2026.08.18
// - 権限確認を追加
// Fixed on 2026.08.27
// - 接頭辞を追加
function formzu_get_iframe_height() {
    if ( ! check_ajax_referer('formzu_get_iframe_height', 'security', false) ) {
        die('security error');
    }
    if ( ! isset($_POST['id']) || ! is_string($_POST['id']) ) {
        die('parameter error');
    }

    if ( ! current_user_can('manage_options') ) {
        die('capability error');
    }

    $requested_form_id = wp_unslash($_POST['id']);
    $form_id = FormzuParamHelper::validate_form_id($requested_form_id);

    if ( ! $form_id || $form_id !== $requested_form_id ) {
        die('invalid form id');
    }

    if ( strlen(strval($form_id)) > 10 ) {
        die('too long form id');
    }

    $formzu_url = 'https://ws.formzu.net/';
    $normal_url = $formzu_url . 'fgen/'  . $form_id . '/';
    $mobile_url = $formzu_url . 'sfgen/' . $form_id . '/';

    $error_array = array();

    $normal_page = formzu_get_remote_page($normal_url, $error_array);
    $mobile_page = formzu_get_remote_page($mobile_url, $error_array);

    if ( $normal_page !== false && $mobile_page !== false ) {
        wp_send_json(array($normal_page, $mobile_page, $error_array));
    } else {
        wp_send_json(array($error_array));
    }

    /*
    $page_array  = array();
    $default_ini = ini_get('allow_url_fopen');

    ini_set('allow_url_fopen', 1);

    if (ini_get('allow_url_fopen')) {
        // file_get_contents
        try {
            $normal_page = file_get_contents($normal_url);
            $mobile_page = file_get_contents($mobile_url);
            if ($normal_page && $mobile_page) {
                ini_set('allow_url_fopen', $default_ini);
                die( json_encode(array($normal_page, $mobile_page, $error_array)) );
            }
            else if (count($http_response_header) > 0) {
                $status_code   = explode(' ', $http_response_header[0]);
                $error_array[] = $status_code[1];
                $error_array[] = 'allow_url_fopen = ' . ini_get('allow_url_fopen');
            }
        } catch (Exception $e) {
            $error_array[] = $e->getMessage();
            $error_array[] = 'file_get_contents is failed';

        }
    }
    else {
        // curl
        try {
            $normal_curl = curl_init();
            curl_setopt($normal_curl, CURLOPT_URL, $normal_url);
            curl_setopt($normal_curl, CURLOPT_HEADER, 0);
            $normal_page = curl_exec($normal_curl);

            $mobile_curl = curl_init();
            curl_setopt($mobile_curl, CURLOPT_URL, $mobile_url);
            curl_setopt($mobile_curl, CURLOPT_HEADER, 0);
            $mobile_page = curl_exec($mobile_curl);

            if ($normal_page && $mobile_page) {
                ini_set('allow_url_fopen', $default_ini);
                die( json_encode(array($normal_page, $mobile_page, $error_array)) );
            }
            else if (count($http_response_header) > 0) {
                $status_code   = explode(' ', $http_response_header[0]);
                $error_array[] = $status_code[1];
                $error_array[] = 'allow_url_fopen = ' . ini_get('allow_url_fopen');
            }
        } catch (Exception $e) {
            $error_array[] = $e->getMessage();
            $error_array[] = 'curl is failed';
        }
    }

    try {
        $normal_page = get_by_curl_open($normal_url);
        $mobile_page = get_by_curl_open($mobile_url);
        if ($normal_page && $mobile_page) {
            ini_set('allow_url_fopen', $default_ini);
            die( json_encode(array($normal_page, $mobile_page, $error_array)) );
        }
    } catch (Exception $e) {
        $error_array[] = $e->getMessage();
        $error_array[] = 'get_by_curl_open is failed';
    }

    try {
        $normal_page = get_by_socket_open($normal_url);
        $mobile_page = get_by_socket_open($mobile_url);
        if ($normal_page && $mobile_page) {
            ini_set('allow_url_fopen', $default_ini);
            die( json_encode(array($normal_page, $mobile_page, $error_array)) );
        }
    } catch (Exception $e) {
        $error_array[] = $e->getMessage();
        $error_array[] = 'get_by_socket_open is failed';
    }

    ini_set('allow_url_fopen', $default_ini);
    die( json_encode(array($error_array)) );
    */
}


// Fixed on 2026.08.27
// - 現状使用していないためコメントアウト
/*
function get_by_curl_open($url) {
    $ch = curl_init();
    if ( ! $ch ) {
        throw new Exception('no curl error');
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


function get_by_socket_open($url) {
    $result = '';
    $parts  = parse_url($url);
    $host   = $parts['host'];
    $path   = $parts['path'];
    $fp     = fsockopen($host, 80, $errno, $errstr, 30);

    if ( ! $fp ) {
        throw new Exception("$errstr ($errno)<br />\n");
    }
    else {
        $out = "GET $path HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n\r\n";

        fwrite($fp, $out);
        while ( ! feof($fp) ) {
            $result .= fgets($fp, 1024);
        }
    }
    fclose($fp);

    $page_header_pos = strpos($result, '<!DOC');
    $result = substr_replace($result, '', 0, $page_header_pos);
    $result = substr_replace($result, '', strlen($result) - 5, 4);

    return $result;
}
*/


// Added on 2026.08.12
// - WordPress HTTP APIでフォームのページを取得する
// Fixed on 2026.08.20
// - wp_remote_get()をwp_safe_remote_get()に変更(SSRF対策)
// - 取得可能なレスポンスのサイズは32MBまでに制限
function formzu_get_remote_page($url, &$error) {
    $response_size_limit = 32 * MB_IN_BYTES;
    $response = wp_safe_remote_get($url, array(
        'timeout' => 30,
        'limit_response_size' => $response_size_limit
    ));
    if ( is_wp_error($response) ) {
        $error[] = $response->get_error_message();
        return false;
    }

    $status_code = intval(wp_remote_retrieve_response_code($response));
    if ( $status_code < 200 || $status_code >= 300 ) {
        $error[] = 'HTTP status code: ' . strval($status_code);
        return false;
    }

    $response_body = wp_remote_retrieve_body($response);
    if ( ! is_string($response_body) || empty($response_body) ) {
        $error[] = 'Empty response body';
        return false;
    }

    return $response_body;
}
