<?php

/*
 * Fixed at 2020.11.25
 *   - ブラウザのバージョン比較方法を修正
 */
function formzu_alert_ie_browser_version() {
    global $is_IE;

    if ($is_IE):
    ?>
    <script>
    /*
    var is_IE = <?php //echo esc_js($is_IE); ?>;
    if (is_IE) {
        var version = window.navigator.appVersion.toLowerCase();
        var ver = version.substr(version.indexOf('msie') + 5, 1);
        if (ver != 9 && ver != 1) {
            alert('お使いのブラウザはサポートが終了しています。\nブラウザを最新版にアップグレードするか、\n他のWebブラウザーに移行してください。');
        }
    }
    */

    var userAgent = window.navigator.userAgent.toLowerCase();
    var trident = userAgent.indexOf('trident');
    if (trident != -1) {
        var trident_ver = userAgent.substr(trident + 8, 1);
        if (trident_ver < 5) {
            alert('お使いのブラウザはサポートが終了しています。\nブラウザを最新版にアップグレードするか、\n他のWebブラウザーに移行してください。');
        }
    }
    else {
        alert('お使いのブラウザはサポートが終了しています。\nブラウザを最新版にアップグレードするか、\n他のWebブラウザーに移行してください。');
    }
    </script>
    <?php
    endif;
}

