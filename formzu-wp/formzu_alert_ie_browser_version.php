<?php
// Fixed on 2020.11.25
// - ブラウザのバージョン比較方法を修正
function formzu_alert_ie_browser_version() {
    global $is_IE;

    if ( $is_IE ):
    ?>
    <script>
    var userAgent = window.navigator.userAgent.toLowerCase();
    var trident = userAgent.indexOf('trident');
    if ( trident !== -1 ) {
        var trident_ver = userAgent.substr(trident + 8, 1);
        if ( trident_ver < 5 ) {
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
