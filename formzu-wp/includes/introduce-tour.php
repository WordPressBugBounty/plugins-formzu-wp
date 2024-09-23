<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

class Formzu_Plugin_Tour
{
    const POINTER_CLOSE_ID = 'formzu_close_tour';
    private static $instance;

    public static function get_instance()
    {
        if ( self::is_closed_tour() ) {
            return false;
        }
        if ( ! (self::$instance instanceof self) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    private function __construct()
    {
        global $wp_version;

        if ( version_compare($wp_version, '3.4', '<') )
            return false;

        if ( self::tour_is_closing_now() ) {
            $dismissed_pointers = self::get_closed_data_array();
            $dismissed_pointers[] = 'formzu_close_tour';
            self::set_closed_data_array($dismissed_pointers);
        }
        else {
            $this->queue_tour();
        }
    }


    public static function tour_is_closing_now()
    {
        if ( filter_input(INPUT_GET, 'formzu_close_tour') && wp_verify_nonce(filter_input(INPUT_GET, 'nonce'),'formzu-close-tour') ) {
            return true;
        }
        return false;
    }


    public static function is_closed_tour()
    {
        $dismissed_pointers = self::get_closed_data_array(); 
        if ( in_array(self::POINTER_CLOSE_ID, $dismissed_pointers) ) {
            return true;
        }
        return false;
    }


    public static function reset_closing_tour()
    {
        $dismissed_pointers = self::get_closed_data_array(); 
        $index_of_closed = array_search(self::POINTER_CLOSE_ID, $dismissed_pointers);
        if ( $index_of_closed !== false ) {
            array_splice($dismissed_pointers, $index_of_closed, 1);
            self::set_closed_data_array($dismissed_pointers);
        }
    }


    public static function get_closed_data_array()
    {
        $dismissed_pointers = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        return $dismissed_pointers;
    }

    
    public static function set_closed_data_array($dismissed_pointers)
    {
        update_user_meta(get_current_user_id(), 'dismissed_wp_pointers', implode(',', $dismissed_pointers));
    }


    public function queue_tour()
    {
        if ( !current_user_can('manage_options') ) {
            return; 
        }

        $dismissed_pointers = self::get_closed_data_array(); 

        if ( in_array(self::POINTER_CLOSE_ID, $dismissed_pointers) ) {
        }
        else {
            wp_enqueue_style('wp-pointer');
            wp_enqueue_script('wp-pointer');
            add_action('admin_print_footer_scripts', array($this, 'intro_tour'));
        }
    }


    public function get_pointer_items()
    {
        $h3 = '<h3>' . __('welcome to formzu', 'formzu-admin') . '</h3>';
        $left_center = array(
            'edge'  => 'left',
            'align' => 'center'
        );
        $plugins_location = 'window.location="' . admin_url('plugins.php');
        $admin_location = 'window.location="' . admin_url('admin.php');
        $adminpages = array(
            'formzu-admin' => array(
                array(
                    'content'   => $h3 . '<p>' . __('「1 フォームズのページへ移動して新しくフォームを作成」をクリックしてください。', 'formzu-admin') . '</p>'
                                . '<p>' . __('フォームズのページを表示するボタンが２つ表示されます。', 'formzu-admin') . '</p>'
                                . '<p>' . __('フォームズのページでフォームを作成したのち、このページでフォームを追加することで、WordPress上でフォームが使用可能になります。', 'formzu-admin') . '</p>',
                    'id'        => 'formzu-create-box',
                    'position'  => $left_center,
                    'button2'   => __('次へ', 'formzu-admin'),
                    'button3'   => __('前へ', 'formzu-admin'),
                    'function2' => $admin_location . '?page=formzu-admin&tour_counter=1";',
                    'function3' => $plugins_location . '";',
                ),
                array(
                    'content'   => $h3 . '<p>' . __('「2 フォームズで作成したフォームIDを入力してください」をクリックしてください。', 'formzu-admin') . '</p>'
                                . '<p>' . __('フォームズのページで作成したフォームをWordPressへ登録するための登録フォームが表示されます。', 'formzu-admin') . '</p>'
                                . '<p>' . __('ここでフォームを登録することで、WordPress上で使用可能になります。', 'formzu-admin') . '</p>',
                    'id'        => 'formzu-add-box',
                    'position'  => $left_center,
                    'button2'   => __('次へ', 'formzu-admin'),
                    'button3'   => __('前へ', 'formzu-admin'),
                    'function2' => $admin_location . '?page=formzu-admin&tour_counter=2";',
                    'function3' => $admin_location . '?page=formzu-admin";',
                ),
                array(
                    'content'   => $h3 . '<p>' . __('「3 フォーム一覧」をクリックしてください。', 'formzu-admin') . '</p>'
                                . '<p>' . __('WordPressで使用可能なフォームの一覧が表示されます。', 'formzu-admin') . '</p>'
                                . '<p>' . __('ここでフォームの設置・編集・更新・削除などを行います。', 'formzu-admin') . '</p>',
                    'id'        => 'formzu-list-box',
                    'position'  => $left_center,
                    'button2'   => __('次へ', 'formzu-admin'),
                    'button3'   => __('前へ', 'formzu-admin'),
                    'function2' => $admin_location . '?page=how-to-use";',
                    'function3' => $admin_location . '?page=formzu-admin&tour_counter=1";',
                ),
                array(
                    'content'   => $h3 . '<p>' . __('以上で説明を終了します。', 'formzu-admin') . '</p>'
                                . '<p>' . __('「1 フォームズのページへ移動して新しくフォームを作成」をクリックして、フォームの作成を開始してください。', 'formzu-admin') . '</p>',
                    'id'        => 'formzu-create-box',
                    'position'  => $left_center,
                    'button2'   => __('使い方を読む', 'formzu-admin'),
                    'function2' => $admin_location . '?page=how-to-use";',
                ),
            ),
            'how-to-use' => array(
                array(
                    'content'   => $h3 . '<p>' . __('この画面は当プラグインの使い方の説明画面です。', 'formzu-admin') . '</p>'
                                . '<p>' . __('基本的な使い方に加え、フォームを設置する方法について書かれています。', 'formzu-admin') . '</p>'
                                . '<p>' . __('手順がわからない場合はこちらを確認してみてください。', 'formzu-admin') . '</p>',
                    'id'        => 'post-body',
                    'position'  => $left_center,
                    'button2'   => __('次へ', 'formzu-admin'),
                    'button3'   => __('前へ', 'formzu-admin'),
                    'function2' => $admin_location . '?page=formzu-admin&tour_counter=3";',
                    'function3' => $admin_location . '?page=formzu-admin&tour_counter=2";',
                ),
            ),
            'plugin_page' => array(
                array(
                    'content'   => $h3 . '<p>こちらの「フォームズ」をクリックして開始してください。</p>',
                    'id'        => 'toplevel_page_formzu-admin',
                    'position'  => array(
                            'edge'  => 'top',
                            'align' => 'left'
                    ),
                    'button2'   => __('次へ', 'formzu-admin'),
                    'function2' => $admin_location . '?page=formzu-admin"',
                ),
            ),
        );
        return $adminpages;
    }


    public function intro_tour()
    {
        $adminpages = $this->get_pointer_items();
        $page = '';
        $screen = get_current_screen();

        if ( isset($_GET['page']) ) {
            $page = $_GET['page'];
        }
        if ( empty($page) && isset($screen->id)) {
            $page = $screen->id;
        }
        //条件：現在開いているページがプラグイン一覧だったら
        //if( strpos(admin_url('plugins.php'), explode('?', add_query_arg(array()))[0] ) !== false ) {
        $url_and_param = explode('?', add_query_arg(array()));
        if( strpos(admin_url('plugins.php'), $url_and_param[0] ) !== false ) {
            $page = 'plugin_page';
        }
        //同じページで複数の案内を表示させるためのカウンター
        if ( isset($_GET['tour_counter']) ) {
            $index = intval($_GET['tour_counter']);
        }
        if ( empty($index) ) {
            $index = 0;
        }

        $page_item = $adminpages[$page][$index];
        $button2 = '';
        $button3 = '';
        $function2 = '';
        $function3 = '';
        $opt_arr = array();

        if ( ! empty($page_item['id']) ) {
            $id = '#' . $page_item['id'];
        }
        else {
            $id = '#' . $screen->id;
        }
        if ( $page != '' && in_array($page, array_keys($adminpages)) ) {

            $align = (is_rtl()) ? 'right' : 'left';
            $opt_arr = array(
                'content'  => $page_item['content'],
                'position' => array(
                    'edge'  => (!empty($page_item['position']['edge'])) ? $page_item['position']['edge'] : 'left',
                    'align' => (!empty($page_item['position']['align'])) ? $page_item['position']['align'] : $align,
                ),
                'pointerWidth' => 400,
            );

            if ( isset($page_item['button2']) ) {
                $button2 = (!empty($page_item['button2'])) ? $page_item['button2'] : __('次へ', 'formzu-admin');
            }
            if ( isset($page_item['button3']) ) {
                $button3 = (!empty($page_item['button3'])) ? $page_item['button3'] : __('前へ', 'formzu-admin');
            }
            if ( isset($page_item['function2']) ) {
                $function2 = $page_item['function2'];
            }
            if ( isset($page_item['function3']) ) {
                $function3 = $page_item['function3'];
            }
        }
        $this->echo_pointer($id, $opt_arr, $button2, $function2, $button3, $function3);
    }


    public function echo_pointer($selector, $options, $button2 = false, $button2_function = '', $button3 = false, $button3_function = '') {
?>
        <script>
            //<![CDATA[
            (function($){
                var formzu_pointer_options = <?php echo json_encode($options); ?>;

                formzu_pointer_options = $.extend(formzu_pointer_options, {
                    buttons: function(event, t){
                        button = $('<a id="pointer-close" style="margin-left: 5px" class="button-secondary">' + '<?php echo __('これ以降の説明を表示しない', 'formzu-admin'); ?>' + '</a>');
                        button.bind('click.pointer', function(){
                            t.element.pointer('close');
                        });
                        return button;
                    },
                    close: function(){
                    }
                });

                var setup = function(){
                    $('<?php echo $selector; ?>').pointer(formzu_pointer_options).pointer('open');

                    <?php if ( $button2 ) { ?>
                        $('#pointer-close').after('<a id="pointer-next" class="button-primary">' + '<?php echo $button2; ?>' + '</a>').css('margin-left', '5px');
                    <?php } ?>
                    $('#pointer-next').bind('click', function(){
                        <?php echo $button2_function; ?>
                    });

                    <?php if ( $button3 ) { ?>
                        $('#pointer-close').after('<a id="pointer-previous" class="button-primary">' + '<?php echo $button3; ?>' + '</a>').css('margin-left', '5px');
                    <?php } ?>
                    $('#pointer-previous').bind('click', function(){
                        <?php echo $button3_function; ?>
                    });

                    $('#pointer-close').bind('click', function(){
                        $.post(ajaxurl, {
                            pointer: '<?php echo self::POINTER_CLOSE_ID; ?>',
                            action: 'dismiss-wp-pointer'
                        });
                    });
                };

                if(formzu_pointer_options.position && formzu_pointer_options.position.defer_loading){
                    $(window).bind('load.wp-pointers', setup);
                }else{
                    $(document).ready(setup);
                }
            })(jQuery);
            //]]>
        </script>
<?php
    }
}

