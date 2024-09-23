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
        $control_ops = array(
            #'width'  => 400,
            #'height' => 350,
        );

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
        if ( FormzuParamHelper::isset_key($par, 'form_widget_data') ) {
            //$form_id = explode(' ', $par['form_widget_data'])[0];
            $form_id_height = explode(' ', $par['form_widget_data']);
            $form_id = $form_id_height[0];
        }
        if ( !isset($form_id) ) {
            $form_id = '';
        }

        $id = $this->get_field_id('form_widget_data');
        $name = $this->get_field_name('form_widget_data');
        $form_data = FormzuOptionHandler::get_option('form_data', array());

        ?>
        <p>
            リンクさせるフォーム：<br>
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
                <?php for ($i = 0, $l = count($form_data); $i < $l; $i++) : ?>
                    <?php $data = $form_data[$i]; ?>
                    <?php $value = $data['id'] . ' ' . $data['height'] . ' ' . $data['mobile_height']; ?>
                    <option value="<?php echo $value; ?>" <?php echo ($form_id == $data['id'] || $i + 1 == $l) ? 'selected' : ''; ?>><?php echo $data['name']; ?></option>
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
            <input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr($title); ?>" class="widefat" placeholder="タイトル文字列を入力（空で非表示）" />
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
            <input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr($form_text); ?>" class="widefat" placeholder="リンク文字列を入力（空で非表示）" />
        </p>
        <?php
    }


    public function echo_widget_position_setting( $par )
    {
        $form_position = FormzuParamHelper::return_val_or_def($par, 'form_position', 'normal');
        $id = $this->get_field_id('form_position');
        $name = $this->get_field_name('form_position');

        ?>
        <p>
            表示する位置：<br>
            <radiogroup>
                <label><input type="radio" name="<?php echo $name; ?>" value="left_bottom" <?php echo checked('left_bottom', $form_position); ?>><span>左下</span></label>
                <label><input type="radio" name="<?php echo $name; ?>" value="normal" <?php echo checked('normal', $form_position); ?>><span>通常</span></label>
                <label><input type="radio" name="<?php echo $name; ?>" value="right_bottom" <?php echo checked('right_bottom', $form_position); ?>><span>右下</span></label>
            </radiogroup>
        </p>
        <?php
    }


    public function echo_widget_plan_setting( $par )
    {
        $form_plan = FormzuParamHelper::return_val_or_def($par, 'form_plan', 'modal_window');
        $id = $this->get_field_id('form_plan');
        $name = $this->get_field_name('form_plan');

        ?>
        <p>
            画面を開く方式：<br>
            <radiogroup class="formzu-radiogroup">
                <label><input type="radio" name="<?php echo $name; ?>" value="modal_window" <?php echo checked('modal_window', $form_plan); ?>><span>モーダル画面</span></label>
                <label><input type="radio" name="<?php echo $name; ?>" value="new_tab" <?php echo checked('new_tab', $form_plan); ?>><span>別タブ</span></label>
                <label><input type="radio" name="<?php echo $name; ?>" value="new_window" <?php echo checked('new_window', $form_plan); ?>><span>別画面</span></label>
            </radiogroup>
        </p>
        <?php
    }


    public function echo_widget_titlelink_setting( $par )
    {
        $form_titlelink = FormzuParamHelper::return_val_or_def($par, 'form_titlelink', 'off');
        $id = $this->get_field_id('form_titlelink');
        $name = $this->get_field_name('form_titlelink');

        ?>
        <p>
            <label>タイトルをリンクとして使う：</label>
            <input type="checkbox" name="<?php echo $name; ?>" value="on" <?php echo checked('on', $form_titlelink); ?>>
        </p>
        <?php
    }


    public function form( $par )
    {
        $this->echo_widget_form_setting($par);
        $this->echo_widget_title_setting($par);
        $this->echo_widget_text_setting($par);
        $this->echo_widget_position_setting($par);
        $this->echo_widget_plan_setting($par);
        $this->echo_widget_titlelink_setting($par);
    }


    public function update( $new_instance, $old_instance )
    {
        return $new_instance;
    }


    public function echo_fixed_before_widget( $args, $par )
    {
        $position = $par['form_position'];

        global $wp_version;

        if (version_compare($wp_version, '3.4', '>=')) {
            $is_mobile = wp_is_mobile();
        }
        else {
            $is_mobile = false;
        }

        if ($position == 'normal' || $is_mobile) {
            return $args['before_widget'];
        }

        $html_text = str_replace(' class="', ' class="formzu-fixed-widget ', $args['before_widget']);

        if ($position == 'left_bottom') {
            $left_or_right = 'right: auto; left: ';
        }
        elseif ( $position == 'right_bottom' ) {
            $left_or_right = 'right: ';
        }

        $style_pos = strpos($html_text, ' style="');

        if ($style_pos !== false) {
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

        if ($plan == 'new_tab') {
            return '[formzu form_id="%s" text="%s" tagname="a"]';
        }
        if ($plan == 'modal_window') {
            return '[formzu form_id="%s" text="%s" thickbox="on" tagname="a"]';
        }
        if ($plan == 'new_window') {
            return '[formzu form_id="%s" text="%s" new_window="on" tagname="a"]';
        }
        return false;
    }


    public function widget( $args, $par )
    {
        echo $this->echo_fixed_before_widget($args, $par);
        echo $args['before_title'];

        $form_shortcode = $this->echo_form_shortcode($par);
        $form_widget_data = explode(' ', $par['form_widget_data']);
        $form_id = $form_widget_data[0];
        $form_height = $form_widget_data[1];
        $form_mobile_height = $form_widget_data[2];
        $titlelink_on = FALSE;

        if (isset($par['form_titlelink']) && $par['form_titlelink'] == 'on') {
            $titlelink_on = 'TRUE';
        }
        if ($titlelink_on) {
            $html = sprintf($form_shortcode, $form_id, $par['title']);
            echo do_shortcode($html);
        }
        else {
            echo $par['title'];
        }

        echo $args['after_title'];

        if ($titlelink_on) {
            echo $par['form_text'];
        }
        else {
            $html = sprintf($form_shortcode, $form_id, $par['form_text']);
            echo do_shortcode($html);
        }

        echo $args['after_widget'];

    }
}

