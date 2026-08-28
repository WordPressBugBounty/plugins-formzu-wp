<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function formzu_admin_notices() {
    // Fixed on 2026.08.20
    // - GETリクエストから通知用transientを生成する処理を廃止(CSRF対策)

    // Fixed on 2026.08.07
    // - 通知のstrposによる判定を廃止する代わりに出力をエスケープ
    //   (imgやsvgタグのon属性を利用した攻撃に対応するため)
    $allowed_notice_html = array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
            'rel'    => array(),
        ),
    );

    // Fixed on 2026.08.14
    // - get_transientの内容を確実に配列化するよう修正
    // - foreachの代わりにforループを使用するよう変更
    // Fixed on 2026.08.26
    // - get_transient()で取得した値の文字列型検証を追加
    $messages = get_transient('formzu-admin-errors');
    if ( is_string($messages) ) {
        $messages = array($messages);
    }
    elseif ( ! is_array($messages) ) {
        $messages = array();
    }
    $messages = array_values($messages);
?>
    <?php if ( ! empty($messages) ) : ?>
    <div class="updated notice my-error is-dismissible">
        <ul>
            <?php for ($i = 0, $c = count($messages); $i < $c; $i++): ?>
                <?php if ( ! is_string($messages[$i]) ) continue; ?>
                <li><?php echo wp_kses($messages[$i], $allowed_notice_html); ?></li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ( is_string($message = get_transient('formzu-admin-updated')) ) : ?>
    <div class="updated notice my-updated is-dismissible">
        <ul>
            <li><?php echo wp_kses($message, $allowed_notice_html); ?></li>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ( is_string($message = get_transient('formzu-admin-html')) ) : ?>
    <div class = "updated notice my-updated is-dismissible">
        <ul>
            <li><?php echo wp_kses($message, $allowed_notice_html); ?></li>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ( is_string($message = get_transient('formzu-admin-error')) ) : ?>
    <div class="updated notice my-error is-dismissible">
        <ul>
            <li><?php echo wp_kses($message, $allowed_notice_html); ?></li>
        </ul>
    </div>
    <?php endif; ?>
<?php
}
