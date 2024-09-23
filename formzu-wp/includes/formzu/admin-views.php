<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

function echo_formzu_admin_page() {
    $screen = get_current_screen();
    $page   = $screen->id;

    ?>
        <div class="wrap">
            <h2>フォーム管理（フォームズ）</h2>
            <i class="fa fa-external-link-square"></i><a style="margin-right: 12px;" href="https://www.formzu.com" target="_blank">フォームズトップページ</a>
            <i class="fa fa-frown-o"></i><a style="margin-right: 12px;" href="#" onClick="javascript:window.open('https://ws.formzu.net/dist/S95904411/', 'mailform1', 'toolbar=no, location=no, status=yes, menubar=yes, resizable=yes, scrollber=yes, width=600, height=550, top=50, left=50')">改善要望</a>
            <i class="fa fa-exclamation-triangle"></i><a style="margin-right: 12px;" href="#" onClick="javascript:window.open('https://ws.formzu.net/dist/S97257136/', 'mailform1', 'toolbar=no, location=no, status=yes, menubar=yes, resizable=yes, scrollber=yes, width=600, height=550, top=50, left=50')">不具合報告</a>

            <div id="poststuff" class="metabox-holder">
                <div class="postbox-conteiner formzu-container">
                    <form id="postbox-wrap-form" method="get" action="">
                    </form>
                        <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
                        <?php wp_nonce_field('meta-box-order',  'meta-box-order-nonce', false); ?>

                        <?php add_meta_box('formzu-create-box', '<span class="box-icon">1<i class="fa fa-external-link" aria-hidden="true"></i></span><span class="box-label">' . __(' フォームズへ移動して新しくフォームを作成します（別ページ）', 'formzu-admin') . '</span>', 'echo_create_formzu_form_body', $page); ?>
                        <?php add_meta_box('formzu-add-box',    '<span class="box-icon">2<i class="fa fa-plus" aria-hidden="true"></i></span><span class="box-label">' . __(' フォームズで作成したフォームIDを入力してください', 'formzu-admin') . '</span>', 'echo_add_formzu_form_body', $page); ?>
                        <?php add_meta_box('formzu-list-box',   '<span class="box-icon">3<i class="fa fa-list" aria-hidden="true"></i></span><span class="box-label">' . __(' フォーム一覧（WordPressに登録したフォーム）', 'formzu-admin') . '</span>', 'echo_formzu_list_body', $page); ?>

                        <?php do_meta_boxes($page, 'advanced', null); ?>
                </div>
            </div>


            <script type="text/javascript">
                (function($){
                    $(document).ready(function($){
                        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');

                        <?php
                        // Fixed at 2020.10.21
                        //   - Wordpress5.5への対応(metaboxの仕様変更に伴うもの)
                        global $wp_version;
                        if ( version_compare( $wp_version, '5.5', '>=' ) ) {
                        ?>
                        $('div.postbox-header').find('h2').removeClass('ui-sortable-handle').removeClass('hndle');
                        $('div.postbox-header').find('div.handle-actions').bind('click', function(e){
                            if ($(this).parent().parent().hasClass('closed')) {
                                $(this).parent().parent().removeClass('closed');
                            }
                            else {
                                $(this).parent().parent().addClass('closed');
                            }
                        });
                        $('.handle-order-higher').remove();
                        $('.handle-order-lower').remove();
                        $('.postbox-header').bind('click', function(e){
                            if ($(this).parent().hasClass('closed')) {
                                $(this).parent().removeClass('closed');
                            }
                            else {
                                $(this).parent().addClass('closed');
                            }
                        });
                        <?php
                        }
                        ?>

                        if (typeof postboxes !== 'undefined') {
                            postboxes.add_postbox_toggles('<?php echo $page; ?>');
                        }

                        $('#formzu-add-box .hndle').bind('click', function(e){
                            if (!$(this).parent().hasClass('closed')) {
                                $('#add-new-form-input').focus();
                            }
                        });

                        function addArrow(){
                            var $table      = $('#advanced-sortables').find('.postbox');
                            var $create_box = $('#formzu-create-box');
                            var $add_box    = $('#formzu-add-box');

                            var create_box_index = $table.index($create_box);
                            var add_box_index    = $table.index($add_box);
                            var list_box_index   = $table.index($('#formzu-list-box'));

                            var correct_order = create_box_index < add_box_index && add_box_index < list_box_index;
                            if (correct_order) {
                                if ($('.separate-arrow').length) {
                                    return;
                                }
                                var separate_arrow = '<div class="separate-arrow"><i class="fa fa-caret-down fa-5x" aria-hidden="true"></i></div>';
                                var separate_box = '<div class="separate-box">'
                                    + '<div class="formzu-mail-icon">'
                                    + '<i class="fa fa-envelope-o fa-2x" aria-hidden="true"></i>'
                                    + '<i class="fa fa-exclamation-circle fa-lg" aria-hidden="true"></i>'
                                    + '</div>'
                                    + '<span>フォーム作成通知メールのフォームIDを確認してください</span>'
                                    + '</div>';

                                $create_box.css('margin-bottom', '0')
                                    .after(separate_arrow)
                                    .after(separate_box)
                                    .after($(''))
                                    .after(separate_arrow);
                                $add_box.css('margin-bottom', '0').after(separate_arrow);
                            } else {
                                $('.separate-arrow').remove();
                                $('.separate-box').remove();
                                $create_box.css('margin-bottom', '50px');
                                $add_box.css('margin-bottom', '50px');
                            }
                        }

                        addArrow();
                        $('#advanced-sortables').bind('sortstop', function(e, ui){
                            addArrow();
                        });
                    });
                })(jQuery);
            </script>


        </div>
    <?php
}


function echo_create_formzu_form_body() {
?>
    <div class="panel">
        <div class="panel-content">

            <div id="goto-formzu-page-button" class="large-button">フォームズを表示する</div>
            <!--
            <div id="open-formzu-page-button" class="large-button">別タブでフォームズを表示する</div>
            -->
            <!--
            <div id="open-formzu-page-button" class="large-button" style="margin: 0 0 20px 0;">別タブでフォームズを表示する</div>
            <div id="goto-formzu-page-button" class="large-button">同じ画面でフォームズを表示する</div>
            -->

        </div>
    </div>
<?php
}

/*
 * Fixed at 2020.11.18
 *   - add-new-form-dataのinput要素の並び順を変更
 */
function echo_add_formzu_form_body() {
?>
    <div class="panel">
        <div class="panel-content">
            <div><form id="dummy-form"></form></div>
            <!-- フォームが消されてしまうので、↑にダミーを設置 -->
            <!-- postbox-wrap-formにadd-new-form-data formが内包されてしまっているせいで消されてしまう。別の箇所にhiddenで設置するべき？-->

            <form id="add-new-form-data" method="post" action="">
                <?php wp_nonce_field( 'formzu-new-form-save', 'add-new-form' ); ?>
                <div id="add-new-form-container">
                    <label for="add-new-form-input" id="add-new-form-label">フォームID</label>
                    <input type="text" id="add-new-form-input" name="form_id_URL" placeholder="例）S12345678">
                    <span id="add-new-form-submit" class="large-button">取得する</span>
                </div>
                <div style="display: inline-block; width: 100%;">
                    <span style="margin: 0 0 0 132px;">※フォームURLも入力できます。</span>
                </div>
                <input type="hidden" name="hidden_id" value="" />
                <input type="hidden" name="hidden_title" value="" />
                <input type="hidden" name="hidden_items" value="" />
                <input type="hidden" name="hidden_height" value="" />
                <input type="hidden" name="hidden_mobile_height" value="" />
            </form>

        </div>
    </div>

<?php
}

/*
 * Fixed at 2020.11.18
 *   - reload-form-dataのinput要素の並び順を変更
 */
function echo_formzu_list_body() {
    $form_data = FormzuOptionHandler::get_option( 'form_data' );
    $list_table = new FormzuListTable();

    if (isset($_POST['s'] )){
        $list_table->prepare_items($_POST['s']);
    }
    else {
        $list_table->prepare_items();
    }

?>
    <?php if ( ! empty( $_REQUEST['s'] ) ) {
        echo sprintf( '<span class="subtitle"> ' . __( '検索結果：', 'formzu-admin' ) . '%s </span>', esc_html( $_REQUEST['s'] ) );
    }?>
    <form id="forms-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_html( wp_strip_all_tags( $_REQUEST['page'] ) ); ?>" />
        <?php $list_table->search_box( __( '検索', 'formzu-admin' ), 'formzu_search'); ?>
        <?php $list_table->display(); ?>
    </form>
        <form id="reload-form-data" method="post" action="">
            <?php wp_nonce_field( 'formzu-reload-form-save', 'reload-form-data' ); ?>
            <input type="hidden" name="hidden_id" value="" />
            <input type="hidden" name="hidden_title" value="" />
            <input type="hidden" name="hidden_items" value="" />
            <input type="hidden" name="hidden_height" value="" />
            <input type="hidden" name="hidden_mobile_height" value="" />
        </form>
<?php
}

