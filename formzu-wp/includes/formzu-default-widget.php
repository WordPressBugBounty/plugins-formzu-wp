<?php
if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function register_formzu_default_widgets() {
    register_widget('FormzuDefaultWidget');
}

class FormzuDefaultWidget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array(
            'description' => 'フォームズのフォームを設置するウィジェット',
        );
        $control_ops = array();

        parent::__construct(
            false,
            'Formzu フォーム ウィジェット',
            $widget_ops,
            $control_ops
        );
    }


    function get_instance()
    {
    }


    public function echo_widget_form_setting( $par )
    {
        // Fixed on 2026.08.24
        // - $par, form_widget_dataの検証を追加

        if ( ! is_array($par) ) {
            $par = array();
        }

        $form_widget_data = array();
        $form_id = '';
        $validated_form_id = null;

        if ( isset($par['form_widget_data']) && is_string($par['form_widget_data']) ) {
            $form_widget_data = preg_split('/[[:space:]]+/', trim($par['form_widget_data']), 3);

            if ( is_array($form_widget_data) && isset($form_widget_data[0]) ) {
                $validated_form_id = FormzuParamHelper::validate_form_id($form_widget_data[0]);
                if ( $validated_form_id && $validated_form_id === $form_widget_data[0] ) {
                    $form_id = $validated_form_id;
                }
            }
        }

        $id = $this->get_field_id('form_widget_data');
        $name = $this->get_field_name('form_widget_data');

        $stored_form_data = FormzuOptionHandler::get_option('form_data', array());
        if ( ! is_array($stored_form_data) ) {
            $stored_form_data = array();
        }

        $stored_form_data = array_values($stored_form_data);
        $form_data = array();

        for ($i = 0, $l = count($stored_form_data); $i < $l; $i++) {
            $data = $stored_form_data[$i];
            if (
                ! is_array($data)
                || ! isset(
                    $data['id'],
                    $data['name'],
                    $data['height'],
                    $data['mobile_height']
                )
            ) {
                continue;
            }

            if ( ! is_string($data['id']) || ! is_string($data['name']) ) {
                continue;
            }

            $validated_form_id = FormzuParamHelper::validate_form_id($data['id']);

            if (
                ! $validated_form_id
                || $validated_form_id !== $data['id']
            ) {
                continue;
            }

            if (
                ( ! is_int($data['height']) && ! is_string($data['height']) )
                || ( ! is_int($data['mobile_height']) && ! is_string($data['mobile_height']) )
            ) {
                continue;
            }

            $height = strval($data['height']);
            $mobile_height = strval($data['mobile_height']);

            if (
                preg_match('/^[0-9]+$/D', $height) !== 1
                || preg_match('/^[0-9]+$/D', $mobile_height) !== 1
                || absint($height) <= 0
                || absint($mobile_height) <= 0
            ) {
                continue;
            }

            $form_data[] = array(
                'id' => $validated_form_id,
                'name' => $data['name'],
                'height' => absint($height),
                'mobile_height' => absint($mobile_height)
            );
        }

        // Fixed on 2026.08.19
        // - ウィジェットの「リンクさせるフォーム」が勝手に(表示上)最後のフォームに切り替わる不具合を修正
        $selected_form_id = '';
        $form_count = count($form_data);
        $form_id_exists = false;

        for ($i = 0; $i < $form_count; $i++) {
            if ( isset($form_data[$i]['id']) && $form_data[$i]['id'] === $form_id ) {
                $selected_form_id = $form_id;
                $form_id_exists = true;
                break;
            }
        }
        if ( ! $form_id_exists && $form_count > 0 ) {
            $selected_form_id = $form_data[$form_count - 1]['id'];
        }

        // Fixed on 2026.08.07
        // - フォームID,フォーム名をエスケープ
        // Fixed on 2026.08.10
        // - selectタグ内のnameのエスケープを修正
        // - optionタグ内のvalue,テキストをエスケープ
        // Fixed on 2026.08.19
        // - ウィジェットの「リンクさせるフォーム」が勝手に(表示上)最後のフォームに切り替わる不具合を修正
        //   (selectedが複数設定されていた)
        ?>
        <p>
            リンクさせるフォーム：<br>
            <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
                <?php for ($i = 0, $l = count($form_data); $i < $l; $i++) : ?>
                    <?php $data = $form_data[$i]; ?>
                    <?php $value = $data['id'] . ' ' . $data['height'] . ' ' . $data['mobile_height']; ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php echo ( ! empty($selected_form_id) ? ($selected_form_id === $data['id']) : ($i + 1 === $l) ) ? 'selected' : ''; ?>><?php echo esc_html($data['name']); ?></option>
                <?php endfor; ?>
            </select>
        </p>
        <?php
    }


    public function echo_widget_title_setting( $par )
    {
        $title = FormzuParamHelper::return_val_or_def($par, 'title', '');
        $id = $this->get_field_id('title');
        $name = $this->get_field_name('title');

        ?>
        <p>
            表示するタイトル文字列：<br>
            <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" placeholder="タイトル文字列を入力（空で非表示）" />
        </p>
        <?php
    }


    public function echo_widget_text_setting( $par )
    {
        $form_text = FormzuParamHelper::return_val_or_def($par, 'form_text', '');
        $id = $this->get_field_id('form_text');
        $name = $this->get_field_name('form_text');

        ?>
        <p>
            表示するリンク文字列：<br>
            <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($form_text); ?>" class="widefat" placeholder="リンク文字列を入力（空で非表示）" />
        </p>
        <?php
    }


    // Fixed on 2026.08.20
    // - radiogroupはHTML Standardに存在しないため
    //   fieldsetとlegendに置き換え
    public function echo_widget_position_setting( $par )
    {
        $form_position = FormzuParamHelper::return_val_or_def($par, 'form_position', 'normal');
        $id = $this->get_field_id('form_position');
        $name = $this->get_field_name('form_position');

        $left_bottom_id  = $id . '-left-bottom';
        $normal_id       = $id . '-normal';
        $right_bottom_id = $id . '-right-bottom';

        ?>

        <fieldset class="formzu-radiogroup">
            <legend>表示する位置:</legend>
            <label for="<?php echo esc_attr($left_bottom_id); ?>">
                <input type="radio" id="<?php echo esc_attr($left_bottom_id); ?>" name="<?php echo esc_attr($name); ?>" value="left_bottom" <?php checked('left_bottom', $form_position); ?>><span>左下</span>
            </label>
            <label for="<?php echo esc_attr($normal_id); ?>">
                <input type="radio" id="<?php echo esc_attr($normal_id); ?>" name="<?php echo esc_attr($name); ?>" value="normal" <?php checked('normal', $form_position); ?>><span>通常</span>
            </label>
            <label for="<?php echo esc_attr($right_bottom_id); ?>">
                <input type="radio" id="<?php echo esc_attr($right_bottom_id); ?>" name="<?php echo esc_attr($name); ?>" value="right_bottom" <?php checked('right_bottom', $form_position); ?>><span>右下</span>
            </label>
        </fieldset>

        <?php
    }


    // Fixed on 2026.08.20
    // - radiogroupはHTML Standardに存在しないため
    //   fieldsetとlegendに置き換え
    public function echo_widget_plan_setting( $par )
    {
        $form_plan = FormzuParamHelper::return_val_or_def($par, 'form_plan', 'modal_window');
        $id = $this->get_field_id('form_plan');
        $name = $this->get_field_name('form_plan');

        $modal_window_id = $id . '-modal-window';
        $new_tab_id      = $id . '-new-tab';
        $new_window_id   = $id . '-new-window';

        ?>

        <fieldset class="formzu-radiogroup">
            <legend>画面を開く方式:</legend>
            <label for="<?php echo esc_attr($modal_window_id); ?>">
                <input type="radio" id="<?php echo esc_attr($modal_window_id); ?>" name="<?php echo esc_attr($name); ?>" value="modal_window" <?php checked('modal_window', $form_plan); ?>><span>モーダル画面</span>
            </label>
            <label for="<?php echo esc_attr($new_tab_id); ?>">
                <input type="radio" id="<?php echo esc_attr($new_tab_id); ?>" name="<?php echo esc_attr($name); ?>" value="new_tab" <?php checked('new_tab', $form_plan); ?>><span>別タブ</span>
            </label>
            <label for="<?php echo esc_attr($new_window_id); ?>">
                <input type="radio" id="<?php echo esc_attr($new_window_id); ?>" name="<?php echo esc_attr($name); ?>" value="new_window" <?php checked('new_window', $form_plan); ?>><span>別画面</span>
            </label>
        </fieldset>

        <?php
    }


    public function echo_widget_titlelink_setting( $par )
    {
        $form_titlelink = FormzuParamHelper::return_val_or_def($par, 'form_titlelink', 'off');
        $id = $this->get_field_id('form_titlelink');
        $name = $this->get_field_name('form_titlelink');

        ?>
        <p>
            <label for="<?php echo esc_attr($id); ?>">タイトルをリンクとして使う：</label>
            <input type="checkbox" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="on" <?php checked('on', $form_titlelink); ?>>
        </p>
        <?php
    }


    // Fixed on 2026.08.24
    // - $parの検証を追加(不完全な場合は既定値へ置き換え)
    public function form( $par )
    {
        if ( ! is_array($par) ) {
            $par = array();
        }

        $par['form_widget_data'] = (
            isset($par['form_widget_data'])
            && is_string($par['form_widget_data'])
        ) ? $par['form_widget_data'] : '';

        $par['title'] = (
            isset($par['title'])
            && is_string($par['title'])
        ) ? $par['title'] : '';

        $par['form_text'] = (
            isset($par['form_text'])
            && is_string($par['form_text'])
        ) ? $par['form_text'] : '';

        $par['form_position'] = (
            isset($par['form_position'])
            && is_string($par['form_position'])
            && in_array(
                $par['form_position'],
                array('left_bottom', 'normal', 'right_bottom'),
                true
            )
        ) ? $par['form_position'] : 'normal';

        $par['form_plan'] = (
            isset($par['form_plan'])
            && is_string($par['form_plan'])
            && in_array(
                $par['form_plan'],
                array('modal_window', 'new_tab', 'new_window'),
                true
            )
        ) ? $par['form_plan'] : 'modal_window';

        $par['form_titlelink'] = (
            isset($par['form_titlelink'])
            && is_string($par['form_titlelink'])
            && $par['form_titlelink'] === 'on'
        ) ? 'on' : 'off';

        $this->echo_widget_form_setting($par);
        $this->echo_widget_title_setting($par);
        $this->echo_widget_text_setting($par);
        $this->echo_widget_position_setting($par);
        $this->echo_widget_plan_setting($par);
        $this->echo_widget_titlelink_setting($par);
    }


    // Fixed on 2026.08.10
    // - ウィジェット保存時の検証・サニタイジング
    public function update( $new_instance, $old_instance )
    {
        if ( ! is_array($new_instance) ) {
            return false;
        }

        // サニタイジング用インスタンス
        $instance = array();

        // form_widget_data: "フォームID PCブラウザ用のHeight モバイル用のHeight"

        if (
            ! isset($new_instance['form_widget_data'])
            || ! is_string($new_instance['form_widget_data'])
        ) {
            return false;
        }
        $form_widget_data = trim($new_instance['form_widget_data']);
        $form_widget_parts = preg_split('/[[:space:]]+/', $form_widget_data);
        if ( ! is_array($form_widget_parts) || count($form_widget_parts) !== 3 ) {
            return false;
        }

        if (
            preg_match('/^[0-9]+$/D', $form_widget_parts[1]) !== 1
            || preg_match('/^[0-9]+$/D', $form_widget_parts[2]) !== 1
        ) {
            return false;
        }

        $requested_form_id = $form_widget_parts[0];
        $form_id = FormzuParamHelper::validate_form_id($requested_form_id);
        if ( ! $form_id || $form_id !== $requested_form_id ) {
            return false;
        }

        // 登録済みフォームデータを検索
        $form_data = FormzuOptionHandler::get_option('form_data', array());
        if ( ! is_array($form_data) ) {
            return false;
        }

        $registered_form = false;
        for ($i = 0, $l = count($form_data); $i < $l; $i++) {
            if ( ! isset($form_data[$i]) || ! is_array($form_data[$i]) ) {
                continue;
            }

            if (
                isset($form_data[$i]['id'])
                && is_string($form_data[$i]['id'])
                && $form_data[$i]['id'] === $form_id
            ) {
                $registered_form = $form_data[$i];
                break;
            }
        }
        if ( $registered_form === false ) {
            return false;
        }

        if (
            ! isset($registered_form['height'])
            || ! is_scalar($registered_form['height'])
            || preg_match('/^[0-9]+$/D', strval($registered_form['height'])) !== 1
        ) {
            return false;
        }

        if (
            ! isset($registered_form['mobile_height'])
            || ! is_scalar($registered_form['mobile_height'])
            || preg_match('/^[0-9]+$/D', strval($registered_form['mobile_height'])) !== 1
        ) {
            return false;
        }

        $form_height = absint($registered_form['height']);
        $form_mobile_height = absint($registered_form['mobile_height']);
        if ( ! $form_height || ! $form_mobile_height ) {
            return false;
        }

        // 登録済みフォームデータのフォームID,PCブラウザ用Height,モバイル用Heightを結合
        $instance['form_widget_data'] = sprintf('%s %d %d',
            $form_id,
            $form_height,
            $form_mobile_height
        );

        // フォームタイトル
        $instance['title'] = '';
        if ( isset($new_instance['title']) && is_string($new_instance['title']) ) {
            $instance['title'] = sanitize_text_field($new_instance['title']);
        }

        // フォームリンク文字列
        $instance['form_text'] = '';
        if ( isset($new_instance['form_text']) && is_string($new_instance['form_text']) ) {
            $instance['form_text'] = sanitize_text_field($new_instance['form_text']);
        }

        // フォームウィジェットの表示位置
        $instance['form_position'] = 'normal';
        if ( isset($new_instance['form_position']) && is_string($new_instance['form_position']) ) {
            $form_position = sanitize_key($new_instance['form_position']);
            if ( in_array($form_position, array('left_bottom', 'normal', 'right_bottom'), true) ) {
                $instance['form_position'] = $form_position;
            }
        }

        // フォームウィジェットの表示方式
        $instance['form_plan'] = 'modal_window';
        if ( isset($new_instance['form_plan']) && is_string($new_instance['form_plan']) ) {
            $form_plan = sanitize_key($new_instance['form_plan']);
            if ( in_array($form_plan, array('modal_window', 'new_tab', 'new_window'), true) ) {
                $instance['form_plan'] = $form_plan;
            }
        }

        // チェックボックス
        $instance['form_titlelink'] = 'off';
        if ( isset($new_instance['form_titlelink']) && is_string($new_instance['form_titlelink']) ) {
            $form_titlelink = sanitize_key($new_instance['form_titlelink']);
            if ( $form_titlelink === 'on' ) {
                $instance['form_titlelink'] = 'on';
            }
        }

        return $instance;
    }


    public function echo_fixed_before_widget( $args, $par )
    {
        $position = $par['form_position'];

        global $wp_version;

        if ( version_compare($wp_version, '3.4', '>=') ) {
            $is_mobile = wp_is_mobile();
        }
        else {
            $is_mobile = false;
        }

        if ( $position === 'normal' || $is_mobile ) {
            return $args['before_widget'];
        }

        $html_text = str_replace(' class="', ' class="formzu-fixed-widget ', $args['before_widget']);

        $left_or_right = '';
        if ( $position === 'left_bottom' ) {
            $left_or_right = 'right: auto; left: ';
        }
        elseif ( $position === 'right_bottom' ) {
            $left_or_right = 'right: ';
        }

        $style_pos = strpos($html_text, ' style="');

        if ( $style_pos !== false ) {
            $html_text = str_replace(' style="', ' style="' . $left_or_right . '0; bottom: 20px;', $html_text);
        }
        else {
            $html_text = substr_replace($html_text, ' style="' . $left_or_right . '0; bottom: 20px;" ', -1, 0);
        }
        return  $html_text;
    }


    public function echo_form_shortcode( $par )
    {
        $plan = $par['form_plan'];

        if ( $plan === 'new_tab' ) {
            return '[formzu form_id="%s" text="%s" tagname="a"]';
        }
        if ( $plan === 'modal_window' ) {
            return '[formzu form_id="%s" text="%s" thickbox="on" tagname="a"]';
        }
        if ( $plan === 'new_window' ) {
            return '[formzu form_id="%s" text="%s" new_window="on" tagname="a"]';
        }
        return false;
    }


    // Fixed on 2026.08.10
    // - ウィジェットの出力におけるフォームタイトル,テキストのエスケープ
    // Fixed on 2026.08.19
    // - 不完全なウィジェットデータによるWarning,TypeErrorの防止
    public function widget( $args, $par )
    {
        if ( ! is_array($args) || ! is_array($par) ) {
            return;
        }

        // form_widget_data
        // - "フォームID PCブラウザ用Height モバイル用Height"
        if ( ! isset($par['form_widget_data']) || ! is_string($par['form_widget_data']) ) {
            return;
        }

        // form_widget_data分割
        $form_widget_data = preg_split('/[[:space:]]+/', trim($par['form_widget_data']));
        if ( ! is_array($form_widget_data) || count($form_widget_data) !== 3 ) {
            return;
        }

        // フォームID検証
        $form_id = $form_widget_data[0];
        $form_id = FormzuParamHelper::validate_form_id($form_id);
        if ( ! $form_id || $form_id !== $form_widget_data[0] ) {
            return;
        }

        // フォーム高さ検証
        if (
            preg_match('/^[0-9]+$/D', $form_widget_data[1]) !== 1
            || preg_match('/^[0-9]+$/D', $form_widget_data[2]) !== 1
            || absint($form_widget_data[1]) <= 0
            || absint($form_widget_data[2]) <= 0
        ) {
            return;
        }

        // フォームタイトル設定
        $form_title = '';
        if ( isset($par['title']) && is_string($par['title']) ) {
            $form_title = sanitize_text_field($par['title']);
        }

        // フォームリンク文字列設定
        $form_text = '';
        if ( isset($par['form_text']) && is_string($par['form_text']) ) {
            $form_text = sanitize_text_field($par['form_text']);
        }

        // フォームウィジェット表示位置設定
        $form_position = 'normal';
        if (
            isset($par['form_position'])
            && is_string($par['form_position'])
            && in_array($par['form_position'], array('left_bottom', 'normal', 'right_bottom'), true)
        ) {
            $form_position = $par['form_position'];
        }

        // フォームウィジェット表示方式設定
        $form_plan = 'modal_window';
        if (
            isset($par['form_plan'])
            && is_string($par['form_plan'])
            && in_array($par['form_plan'], array('modal_window', 'new_tab', 'new_window'), true)
        ) {
            $form_plan = $par['form_plan'];
        }

        // チェックボックス設定
        $form_titlelink = 'off';
        if ( isset($par['form_titlelink']) && is_string($par['form_titlelink']) ) {
            $form_titlelink = sanitize_key($par['form_titlelink']);
        }

        // 下位メソッド(echo_form_shortcode,echo_fixed_before_widget)に検証済みの値を渡す
        $par['form_position'] = $form_position;
        $par['form_plan'] = $form_plan;

        $args['before_widget'] = (
            isset($args['before_widget']) && is_string($args['before_widget'])
        ) ? $args['before_widget'] : '';

        $before_title = (
            isset($args['before_title']) && is_string($args['before_title'])
        ) ? $args['before_title'] : '';

        $after_title = (
            isset($args['after_title']) && is_string($args['after_title'])
        ) ? $args['after_title'] : '';

        $after_widget = (
            isset($args['after_widget']) && is_string($args['after_widget'])
        ) ? $args['after_widget'] : '';

        $form_shortcode = $this->echo_form_shortcode($par);
        $before_widget = $this->echo_fixed_before_widget($args, $par);

        if ( ! is_string($form_shortcode) || ! is_string($before_widget) ) {
            return;
        }

        // 必須データの検証完了後に出力する
        echo $before_widget;

        echo $before_title;
        if ( $form_titlelink === 'on' ) {
            $html = sprintf($form_shortcode, $form_id, $form_title);
            echo do_shortcode($html);
        }
        else {
            echo esc_html($form_title);
        }
        echo $after_title;

        if ( $form_titlelink === 'on' ) {
            echo esc_html($form_text);
        }
        else {
            $html = sprintf($form_shortcode, $form_id, $form_text);
            echo do_shortcode($html);
        }

        echo $after_widget;
    }
}
