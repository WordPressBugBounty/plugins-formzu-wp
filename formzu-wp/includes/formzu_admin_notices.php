<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function formzu_admin_notices() {
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'reloaded') {
        if (isset($_REQUEST['name']) && isset($_REQUEST['oldname'])) {

            $message = sanitize_text_field($_REQUEST['name']) . '（旧:' . sanitize_text_field($_REQUEST['oldname']) . '） に関するデータ（タイトル、ショートコード、項目）を更新しました。';

        }
        elseif (isset($_REQUEST['name'])) {

            $message = sanitize_text_field($_REQUEST['name']) . ' に関するデータ（タイトル、ショートコード、項目）を更新しました。';

        }
        else {

            $message = 'Error : 更新に失敗しました。';

        }
        set_transient( 'formzu-admin-updated', $message, 3 );
    }
    if ( FormzuParamHelper::isset_key($_REQUEST, array('error', 'message')) ) {
        set_transient( 'formzu-admin-errors', $_REQUEST['message'], 3 );
    }
?>

    <?php if ( $messages = get_transient( 'formzu-admin-errors' ) ) : ?>
    <div class = "updated notice my-error is-dismissible">
        <ul>
            <?php foreach( $messages as $message ): ?>
                <li><?php echo esc_html($message); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ( $message = get_transient( 'formzu-admin-updated' ) ) : ?>
    <div class = "updated notice my-updated is-dismissible">
        <ul>
            <?php if ( strpos($message, 'script') || strpos($message, 'iframe') ) : ?>
                <li><?php echo esc_html($message); ?></li>
            <?php else : ?>
                <li><?php echo $message; ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ( $message = get_transient( 'formzu-admin-html' ) ) : ?>
    <div class = "updated notice my-updated is-dismissible">
        <ul>
            <?php if ( strpos($message, 'script') || strpos($message, 'iframe') ) : ?>
                <li><?php echo esc_html($message); ?></li>
            <?php else : ?>
                <li><?php echo $message; ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ( $message = get_transient( 'formzu-admin-error' ) ) : ?>
    <div class = "updated notice my-error is-dismissible">
        <ul>
            <?php if ( strpos($message, 'script') || strpos($message, 'iframe') ) : ?>
                <li><?php echo esc_html($message); ?></li>
            <?php else : ?>
                <li><?php echo $message; ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

<?php
}

