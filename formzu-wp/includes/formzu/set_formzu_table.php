<?php
if ( ! defined('FORMZU_PLUGIN_PATH') || ! defined('ABSPATH') ) {
    die();
}

if ( ! class_exists('WP_List_Table') ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class FormzuListTable extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'form',
            'plural'   => 'forms',
            'ajax'     => false
        ) );
    }


    // Fixed on 2026.08.07
    // - 出力をエスケープ
    // Fixed on 2026.08.20
    // - switch文をif文で置き換え
    // Fixed on 2026.08.25
    // - $itemが配列かどうかの検証を追加
    // - $item内の各要素の型検証を追加
    //各カラムの表示に使うデータの使用法を定義
    function column_default($item, $column_name)
    {
        if ( ! is_array($item) || ! isset($item[$column_name]) ) {
            return '';
        }

        if ( $column_name === 'number' ) {
            return is_numeric($item[$column_name]) ? intval($item[$column_name]) + 1 : '';
        }
        elseif ( $column_name === 'name' ) {
            return is_string($item[$column_name]) ? esc_html($item[$column_name]) : '';
        }
        elseif ( $column_name === 'pagebutton' || $column_name === 'shortcode' || $column_name === 'items' ) {
            if ( ! is_string($item[$column_name]) ) {
                return '';
            }
            return wp_kses(
                $item[$column_name],
                array(
                    'br' => array(),
                )
            );
        }
        else {
            return print_r($item, true);
        }
    }


    // Fixed on 2026.08.10
    // - 処理の見直し
    // - アクションリンクのエスケープ処理
    // - ログインページURLの変更
    //column_ + データ名で、カラム作成時に関数が実行される
    function column_name($item)
    {
        $form_id = '';
        if ( isset($item['id']) && is_string($item['id']) ) {
            $form_id = FormzuParamHelper::validate_form_id($item['id']);
        }

        $form_name = '';
        if ( isset($item['name']) && is_string($item['name']) ) {
            $form_name = $item['name'];
        }

        $item_number = 0;
        if ( isset($item['number']) && is_numeric($item['number']) ) {
            $item_number = absint($item['number']);
        }

        $login_url = 'https://www.formzu.com/login_form/' . rawurlencode($form_id);

        $reload_url = add_query_arg(
            array(
                'page'   => 'formzu-admin',
                'action' => 'reload_form_data',
                'id'     => $form_id,
                'name'   => $form_name
            ),
            admin_url('admin.php')
        );

        $delete_url = add_query_arg(
            array(
                'page'   => 'formzu-admin',
                'action' => 'delete_form',
                'id'     => $form_id,
                'number' => $item_number,
                'widget_id' => 'formzudefaultwidget-' . strval($item_number + 2)
            ),
            admin_url('admin.php')
        );
        $delete_url = wp_nonce_url(
            $delete_url,
            'delete_form-' . $form_id,
            'delete_nonce'
        );

        $actions = array(
            'login' => sprintf('<a href="#" class="%s" data-form-id="%s" data-url="%s"><i class="fa fa-wrench" aria-hidden="true"></i>編集</a>',
                'formzu-login-button',
                esc_attr($form_id),
                esc_url($login_url)
            ),
            'reload' => sprintf('<a href="%s" class="%s" data-form-id="%s"><i class="fa fa-refresh" aria-hidden="true"></i>更新</a>',
                esc_url($reload_url),
                'formzu-reload-button',
                esc_attr($form_id)
            ),
            'delete' => sprintf('<a href="%s"><i class="fa fa-times" aria-hidden="true"></i>登録解除</a>',
                esc_url($delete_url)
            )
        );

        $form_url = 'https://ws.formzu.net/fgen/' . rawurlencode($form_id);

        $table_row = sprintf('%s <span style="color:silver;">(form_id: <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>)</span>%s',
            esc_html($form_name),
            esc_url($form_url),
            esc_html($form_id),
            $this->row_actions($actions)
        );

        if ( isset($_REQUEST['action']) && is_string($_REQUEST['action']) && sanitize_key(wp_unslash($_REQUEST['action'])) === 'added' ) {
            if ( isset($_REQUEST['number']) && absint($_REQUEST['number']) === $item_number ) {
                $table_row = '<span class="new-item">NEW!</span>' . $table_row;
            }
        }

        return $table_row;
    }


    // Fixed on 2026.08.10
    // - 処理の見直し
    // - アクションリンクのエスケープ処理
    function column_pagebutton($item)
    {
        $form_id = '';
        if ( isset($item['id']) && is_string($item['id']) ) {
            $form_id = FormzuParamHelper::validate_form_id($item['id']);
        }

        $form_name = '';
        if ( isset($item['name']) && is_string($item['name']) ) {
            $form_name = $item['name'];
        }

        $url = add_query_arg(
            array(
                'page'   => 'formzu-admin',
                'action' => 'create_formzu_page',
                'id'     => $form_id,
                'name'   => $form_name
            ),
            admin_url('admin.php')
        );
        $nonce_url = wp_nonce_url($url, 'formzu_create_page', 'create_page_nonce');

        return sprintf('<a href="%s" class="button action"><div class="dashicons-before dashicons-admin-page">固定ページ作成</div></a>',
            esc_url($nonce_url)
        );
    }


    // Fixed on 2026.08.10
    // - 処理の見直し
    // - アクションリンクのエスケープ処理
    function column_widgetbutton($item)
    {
        $form_id = '';
        if ( isset($item['id']) && is_string($item['id']) ) {
            $form_id = FormzuParamHelper::validate_form_id($item['id']);
        }

        $form_name = '';
        if ( isset($item['name']) && is_string($item['name']) ) {
            $form_name = $item['name'];
        }

        $url = add_query_arg(
            array(
                'action' => 'create_formzu_default_widget',
                'id'     => $form_id,
                'name'   => $form_name
            ),
            admin_url('widgets.php')
        );
        $nonce_url = wp_nonce_url($url, 'create_formzu_default_widget-' .$form_id, 'create_widget_nonce');

        return sprintf('<a href="%s" class="button action"><i class="fa fa-code" aria-hidden="true"></i>ウィジェット作成</a>',
            esc_url($nonce_url)
        );
    }


    // Fixed on 2026.08.10
    // - 処理の見直し
    // - ショートコードのエスケープ処理
    function column_shortcode($item)
    {
        $form_id = '';
        if ( isset($item['id']) && is_string($item['id']) ) {
            $form_id = FormzuParamHelper::validate_form_id($item['id']);
        }

        $shortcode_value = sprintf('[formzu form_id="%s" tagname="iframe"]', $form_id);

        return sprintf('<input type="text" style="font-size:12px; background:inherit;" onfocus="this.select();" readonly="readonly" value="%s" class="large-text code">',
            esc_attr($shortcode_value)
        );
    }


    // Fixed on 2026.08.10
    // - 代入値のチェック
    function column_cb($item)
    {
        $item_number = 0;
        if ( isset($item['number']) && is_numeric($item['number']) ) {
            $item_number = absint($item['number']);
        }

        return sprintf('<input type="checkbox" name="%s[]" value="%s" />',
            'checked_forms_index',
            $item_number
        );
    }


    //列の見出しを付ける
    public function get_columns()
    {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'number'       => 'No.',
            'name'         => __('タイトル', 'formzu-admin'),
            'pagebutton'   => __('固定ページ作成ボタン', 'formzu-admin'),
            'widgetbutton' => __('ウィジェット作成ボタン', 'formzu-admin'),
            'shortcode'    => __('ショートコード', 'formzu-admin'),
            'items'        => __('項目', 'formzu-admin')
        );
        return $columns;
    }


    //ソートできる列を定義
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'number' => array('number', true),
            'name'   => array('name', false),
        );
        return $sortable_columns;
    }


    //複数選択アクションの見出しを定義
    function get_bulk_actions()
    {
        $actions = array(
            'delete_forms'  => __('消去', 'formzu-admin'),
            'replace_forms' => __('入れ替え', 'formzu-admin'),
        );
        return $actions;
    }


    function echo_notice_error($inner_text)
    {
        ?>
        <div class="notice my-error is-dismissible">
            <ul>
                <li><?php echo esc_html($inner_text); ?></li>
            </ul>
        </div>
        <?php
    }


    // Fixed on 2026.08.07
    // - _wpnonceに起因する脆弱性を修正
    // Fixed on 2026.08.27
    // - capabilityの確認を追加
    //複数選択からの一括操作アクションの動作を定義
    function process_bulk_action()
    {
        $action = $this->current_action();
        if ( ! $action ) {
            return false;
        }

        $bulk_actions = $this->get_bulk_actions();
        if ( ! is_string($action) || ! array_key_exists($action, $bulk_actions) ) {
            return false;
        }

        if (
            ! current_user_can('manage_options')
            || (
                $action === 'delete_forms'
                && ! current_user_can('edit_theme_options')
            )
        ) {
            $this->echo_notice_error(__('この操作を実行する権限がありません。', 'formzu-admin'));
            return false;
        }

        if ( ! isset($_GET['_wpnonce']) || ! is_string($_GET['_wpnonce']) ) {
            $this->echo_notice_error(__('セキュリティチェックに失敗しました。', 'formzu-admin'));
            return false;
        }

        $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));
        $nonce_action = 'bulk-' . $this->_args['plural'];
        if ( ! wp_verify_nonce($nonce, $nonce_action) ) {
            $this->echo_notice_error(__('セキュリティチェックに失敗しました。', 'formzu-admin'));
            return false;
        }

        if ( ! isset($_GET['checked_forms_index']) || ! is_array($_GET['checked_forms_index']) ) {
            $this->echo_notice_error(__('チェックボックスをクリックして処理対象のフォームを指定してください。', 'formzu-admin'));
            return false;
        }

        $requested_indexes = wp_unslash($_GET['checked_forms_index']);
        $indexes = array();

        for ($i = 0, $j = count($requested_indexes); $i < $j; $i++) {
            if ( ! is_scalar($requested_indexes[$i]) || preg_match('/^[0-9]+$/D', strval($requested_indexes[$i])) !== 1 ) {
                $this->echo_notice_error(__('処理対象の指定が不正です。', 'formzu-admin'));
                return false;
            }

            $indexes[] = intval($requested_indexes[$i]);
        }

        // Fixed on 2026.08.25
        // - form_dataが配列かどうかの検証とエラー処理を追加
        // - form_dataのインデックスを連番にする処理を追加
        $form_data = FormzuOptionHandler::get_option('form_data', array());
        if ( ! is_array($form_data) ) {
            $this->echo_notice_error(__('保存されているフォームデータが不正です。', 'formzu-admin'));
            return false;
        }
        $form_data = array_values($form_data);

        // Fixed on 2026.08.27
        // - フォーム一括消去後にウィジェットの削除処理を行うための
        //   削除したフォームIDを保持する配列を追加
        $deleted_ids = array();

        // Fixed on 2026.08.25
        // - 一括削除処理の際のインデックスの検証、form_data各要素が配列かどうかの検証、
        //   form_data各要素のnumberの検証を追加
        // - 削除対象のフォームIDを$deleted_idsに追加する処理を追加
        if ( 'delete_forms' === $action ) {
            for ($i = 0, $l = count($indexes); $i < $l; $i++) {
                $form_index = $indexes[$i];
                $deleted_id = '';

                if (
                    isset($form_data[$form_index])
                    && is_array($form_data[$form_index])
                    && isset($form_data[$form_index]['number'])
                    && is_numeric($form_data[$form_index]['number'])
                    && intval($form_data[$form_index]['number']) === $form_index
                ) {
                    $deleted_id = (
                        isset($form_data[$form_index]['id']) && is_string($form_data[$form_index]['id'])
                    ) ? FormzuParamHelper::validate_form_id($form_data[$form_index]['id']) : '';
                    unset($form_data[$form_index]);
                }

                if (
                    is_string($deleted_id)
                    && $deleted_id !== ''
                    && ! in_array($deleted_id, $deleted_ids, true)
                ) {
                    $deleted_ids[] = $deleted_id;
                }
            }

            $form_data = array_values($form_data);

            for ($i = 0, $l = count($form_data); $i < $l; $i++) {
                if ( ! is_array($form_data[$i]) ) {
                    continue;
                }
                $form_data[$i]['number'] = $i;
            }
        }
        elseif ( 'replace_forms' === $action ) {
            // Fixed on 2026.08.18
            // - 入れ替え時のインデックスのチェックを追加
            // - 入れ替え時にフォームのNo.が同一になる不具合を修正

            if ( count($indexes) !== 2 ) {
                $this->echo_notice_error(__('「入れ替え」操作は入れ替えたい二つのフォームを指定して実行してください。', 'formzu-admin'));
            }
            elseif ( $indexes[0] === $indexes[1] ) {
                $this->echo_notice_error(__('「入れ替え」操作は同じフォームを指定して実行することはできません。', 'formzu-admin'));
            }
            elseif ( ! isset($form_data[$indexes[0]],  $form_data[$indexes[1]]) ) {
                $this->echo_notice_error(__('「入れ替え」操作の対象となるフォームが見つかりませんでした。', 'formzu-admin'));
            }
            elseif ( ! is_array($form_data[$indexes[0]]) || ! is_array($form_data[$indexes[1]]) ) {
                $this->echo_notice_error(__('「入れ替え」操作の対象となるフォームのデータが不正です。', 'formzu-admin'));
            }
            else {
                $first_form_data  = $form_data[$indexes[0]];
                $second_form_data = $form_data[$indexes[1]];

                $form_data[$indexes[0]] = $second_form_data;
                $form_data[$indexes[1]] = $first_form_data;

                $form_data[$indexes[0]]['number'] = $indexes[0];
                $form_data[$indexes[1]]['number'] = $indexes[1];
            }
        }
        else {
            return false;
        }

        FormzuOptionHandler::update_option('form_data', $form_data);

        // Fixed on 2026.08.27
        // - 一括消去したフォームのウィジェット(サイドバー内含む)を削除
        for ($i = 0, $l = count($deleted_ids); $i < $l; $i++) {
            $deleted_keys = delete_from_widget_contents($deleted_ids[$i]);
            delete_from_sidebars_widgets($deleted_keys);
        }
    }


    // Fixed on 2026.08.14
    // - ソート処理(usort_reorder)をクラスメソッド内関数からクラスメソッドへ移動(関数の再宣言によるFatal errorを防止)
    // - ソート対象と並び順のリクエスト値のチェックを追加
    // Fixed on 2026.08.18
    // - number列が複数桁の場合に正しくソートされない不具合を修正
    public function usort_reorder($a, $b)
    {
        $orderby = 'number';
        $sortable_columns = $this->get_sortable_columns();

        if ( isset($_REQUEST['orderby']) && is_string($_REQUEST['orderby']) ) {
            $requested_orderby = sanitize_key(wp_unslash($_REQUEST['orderby']));
            if ( isset($sortable_columns[$requested_orderby]) ) {
                $orderby = $requested_orderby;
            }
        }

        $order = 'desc';
        if ( isset($_REQUEST['order']) && is_string($_REQUEST['order']) ) {
            $requested_order = sanitize_key(wp_unslash($_REQUEST['order']));
            if ( $requested_order === 'asc' || $requested_order === 'desc' ) {
                $order = $requested_order;
            }
        }

        $first_value = '';
        if ( is_array($a) && isset($a[$orderby]) && is_scalar($a[$orderby]) ) {
            $first_value = strval($a[$orderby]);
        }

        $second_value = '';
        if ( is_array($b) && isset($b[$orderby]) && is_scalar($b[$orderby]) ) {
            $second_value = strval($b[$orderby]);
        }

        if ( $orderby === 'number' ) {
            $result = strnatcmp($first_value, $second_value);
        }
        else {
            $result = strcmp($first_value, $second_value);
        }

        return ( $order === 'asc' ) ? $result : -$result;
    }


    // Fixed on 2026.08.18
    // - クエリパラメータの文字列チェックを追加
    public function prepare_items()
    {
        $per_page = 100;

        $this->_column_headers = $this->get_column_info();
        $this->process_bulk_action();

        // Fixed on 2026.08.25
        // - form_dataが配列かどうかの検証を追加
        // - form_dataのインデックスを連番にする処理を追加
        $data = FormzuOptionHandler::get_option('form_data', array());
        if ( ! is_array($data) ) {
            $data = array();
        }
        $data = array_values($data);

        // Fixed on 2026.08.25
        // - 表示に必要なフィールド(フォームID)が存在するかの検証を追加
        // - 表示に必要なフィールドの型検証を追加(デフォルト値を空文字に設定)
        // - 検証済みのデータを$dataに再代入する処理を追加
        $validated_data = array();
        for ($i = 0, $l = count($data); $i < $l; $i++) {
            if ( ! is_array($data[$i]) || ! isset($data[$i]['id']) || ! is_string($data[$i]['id']) ) {
                continue;
            }

            $form_id = FormzuParamHelper::validate_form_id($data[$i]['id']);
            if ( ! $form_id ) {
                continue;
            }

            $form_name = '';
            if ( isset($data[$i]['name']) && is_string($data[$i]['name']) ) {
                $form_name = $data[$i]['name'];
            }

            $form_items = '';
            if ( isset($data[$i]['items']) && is_string($data[$i]['items']) ) {
                $form_items = $data[$i]['items'];
            }

            $validated_data[] = array(
                'id'     => $form_id,
                'name'   => $form_name,
                'number' => $i,
                'items'  => $form_items,
            );
        }
        $data = $validated_data;

        if ( isset($_REQUEST['s']) && is_string($_REQUEST['s']) ) {

            $search = trim(wp_unslash($_REQUEST['s']));
            $output = array();
            $len    = count($data);

            for ($i = 0; $i < $len; $i++) {
                if ( strpos($data[$i]['name'], $search) !== false ) {
                    $output[] = $data[$i];
                }
            }

            $data = $output;

        }

        usort($data, array($this, 'usort_reorder'));

        $current_page = $this->get_pagenum();
        $total_items  = count($data);
        $data         = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->items  = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

    public function no_items()
    {
        _e( 'フォームが見つかりませんでした。フォームズでフォームを作成してください。' );
    }
}
