<?php

if ( ! defined('FORMZU_PLUGIN_PATH') || ! defined('ABSPATH') ) {
    die();
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class FormzuListTable extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct( array(
            'singular' => 'form',
            'plural'   => 'forms',
            'ajax'     => false
        ) );
    }

    //各カラムの表示に使うデータの使用法を定義
    function column_default($item, $column_name)
    {
        switch( $column_name ) {
            case 'number':
                return $item[$column_name] + 1;
            case 'name':
            case 'pagebutton':
            case 'shortcode':
            case 'items':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    //column_ + データ名で、カラム作成時に関数が実行される
    function column_name($item)
    {
        $actions = array(
            'login' => sprintf('<a href="#" class="%s" data-form-id="%s" data-url="https://ws.formzu.net/login_form.php?id=%s"><i class="fa fa-wrench" aria-hidden="true"></i>編集</a>',
                'formzu-login-button',
                $item['id'],
                $item['id']
            ),
            'reload' => sprintf('<a href="%s?page=%s&action=%s&name=%s&id=%s" class="%s" data-form-id="%s"><i class="fa fa-refresh" aria-hidden="true"></i>更新</a>',
                admin_url('admin.php'),
                'formzu-admin',
                'reload_form_data',
                $item['name'],
                $item['id'],
                'formzu-reload-button',
                $item['id']
            ),
            /*
             * Fixed at 2020.10.26
             *   - hrefがダブルクォーテーションで囲まれていなかったのを修正
             */
            'delete' => sprintf('<a href="%s"><i class="fa fa-times" aria-hidden="true"></i>登録解除</a>',
                wp_nonce_url(
                    admin_url(
                        'admin.php?' . sanitize_text_field('page=' . $_REQUEST['page'] . '&action=delete_form&id=' . $item['id'] . '&number=' . $item['number'] . '&widget_id=formzudefaultwidget-' . strval($item['number'] + 2))
                    ),
                    'delete_form-' . $item['id'],
                    'delete_nonce'
                )
            ),
        );

        $table_row = '%1$s <span style="color:silver;">(form_id: <a href="https://ws.formzu.net/fgen/%2$s/" target="_blank">%2$s</a>)</span>%3$s';

        if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'added' ) {
            if ( isset($_REQUEST['number']) && $_REQUEST['number'] == $item['number'] ) {
                $table_row = '<span class="new-item">NEW!</span>' . $table_row;
            }
        }

        return sprintf($table_row,
            $item['name'],
            $item['id'],
            $this->row_actions($actions)
        );
    }


    function column_pagebutton($item)
    {
        $url = sprintf(admin_url('admin.php?page=%s&action=%s&name=%s&id=%s'),
            'formzu-admin',
            'create_formzu_page',
            $item['name'],
            $item['id']
        );
        $nonce_url = wp_nonce_url($url, 'formzu_create_page', 'create_page_nonce');
        return '<a href="' . $nonce_url . '" class="button action"><div class="dashicons-before dashicons-admin-page">固定ページ作成</div></a>';
    }


    function column_widgetbutton($item)
    {
        $url = sprintf(admin_url('widgets.php?action=%s&name=%s&id=%s'),
            'create_formzu_default_widget',
            $item['name'],
            $item['id']
        );
        $nonce_url = wp_nonce_url($url, 'create_formzu_default_widget-' . $item['id'], 'create_widget_nonce');
        return '<a href="' . $nonce_url . '" class="button action"><i class="fa fa-code" aria-hidden="true"></i>ウィジェット作成</a>';
    }


    function column_shortcode($item)
    {
        /*
         * Fixed at 2020.10.26
         *   - onfocus属性内のセミコロンがダブルクォーテーションの外側に出ていたのを修正
         */
        return sprintf('<input type="text" style="font-size:12px; background:inherit;" onfocus="this.select();" readonly="readonly" value="[formzu form_id=&quot;%1$s&quot; tagname=&quot;iframe&quot;]" class="large-text code">',
            $item['id']
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'checked_forms_index',
            $item['number']
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
            'delete_forms'  => __( '消去', 'formzu-admin' ),
            'replace_forms' => __( '入れ替え', 'formzu-admin' ),
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


    //複数選択からの一括操作アクションの動作を定義
    function process_bulk_action()
    {
        if ( FormzuParamHelper::isset_key($_GET, '_wpnonce') ) {

            $nonce  = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce($nonce, $action) ) {
                $this->echo_notice_error( __('security check', 'formzu-admin'));
                return false;
            }
        }

        $action = $this->current_action();

        if ( ! $action ) {
            return false;
        }

        $form_data = FormzuOptionHandler::get_option('form_data', array());
        $indexes   = array();

        if ( FormzuParamHelper::isset_key($_GET, 'checked_forms_index') ) {
            $indexes = $_GET['checked_forms_index'];
        }
        elseif ( $action !== false && array_key_exists($action, $this->get_bulk_actions()) ){
            $this->echo_notice_error( __('チェックボックスをクリックして処理対象のフォームを指定してください。') );
            return false;
        }

        if ( 'delete_forms' === $action ) {
            for ($i = 0, $l = count($indexes); $i < $l; $i++) {
                if ( isset($form_data[$i]['number']) && $form_data[$i]['number'] == $indexes[$i] ) {
                    unset($form_data[$i]);
                }
            }

            $form_data = array_values($form_data);

            for ($i = 0, $l = count($form_data); $i < $l; $i++) {
                $form_data[$i]['number'] = $i;
            }
        }
        elseif ( 'replace_forms' == $action ) {
            if ( count($indexes) != 2 ) {
                $this->echo_notice_error( __('「入れ替え」操作は入れ替えたい二つのフォームを指定して実行してください。', 'formzu-admin') );
            }
            else {
                if ( $form_data[$indexes[1]]['number'] == $indexes[1] ) {

                    $temp = array_splice($form_data, $indexes[1], 1);
                    $temp[0]['number'] = $indexes[0];

                }
                if ( $form_data[$indexes[0]]['number'] == $indexes[0] ) {

                    $temp = array_splice($form_data, $indexes[0], 1, $temp);
                    $temp[0]['number'] = $indexes[1];

                }
                if ( ! empty($temp) ) {
                    array_splice($form_data, $indexes[1], 0, $temp);
                }
            }
        }
        else {
            return false;
        }
        FormzuOptionHandler::update_option('form_data', $form_data);
    }


    public function prepare_items()
    {
        $per_page = 100;
        
        $this->_column_headers = $this->get_column_info();
        $this->process_bulk_action();

        $data = FormzuOptionHandler::get_option('form_data', array());

        if ( isset($_REQUEST['s']) && $_REQUEST['s'] ) {

            $search = $_REQUEST['s'];
            $search = trim($search);
            $output = array();
            $len    = count($data);

            for ($i = 0; $i < $len; $i++) {
                if (strpos($data[$i]['name'], $search) !== false) {
                    $output[] = $data[$i];
                }
            }

            $data = $output;

        }

        //ソートに使う関数
        function usort_reorder($a, $b) {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'number';
            $order   = (!empty($_REQUEST['order']))   ? $_REQUEST['order']   : 'asc';
            $result  = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? -$result : $result;
        }

        usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();
        $total_items  = count($data);
        $data         = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->items  = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ) );
    }

    public function no_items()
    {
        _e( 'フォームが見つかりませんでした。フォームズでフォームを作成してください。' );
    }
}

