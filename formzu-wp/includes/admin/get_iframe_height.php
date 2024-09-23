<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function get_iframe_height() {
    if ( ! check_ajax_referer('get_iframe_height', 'security', FALSE) ) {
        die('security error');
    }
    if ( ! isset($_POST['id']) ) {
        die('parameter error');
    }
    if ( ! strlen(strval($_POST['id'])) > 10 ) {
        die('too long form id');
    }

    $id_num      = $_POST['id'];
    $formzu_url  = 'https://ws.formzu.net/';
    $normal_url  = $formzu_url . 'fgen/'  . $id_num . '/';
    $mobile_url  = $formzu_url . 'sfgen/' . $id_num . '/';
    $error_array = array();
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
}


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
    $result = "";
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


