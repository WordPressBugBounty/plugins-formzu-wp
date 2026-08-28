<?php
// Fixed on 2026.08.17
// - true,falseの表記を小文字に統一

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}


function echo_formzu_link_navmenu_setting() {
    // Fixed on 2026.08.10
    // - 権限(Capability)の確認を追加
    //   必要な権限: edit_theme_options
    if ( ! current_user_can('edit_theme_options') ) {
        die('capability error');
    }

    if ( ! check_ajax_referer(FORMZU_NAVMENU_NONCE, 'security', false) ) {
        die('security error');
    }
    if ( ! isset($_POST['id']) ) {
        die('parameter error');
    }

    $id = FormzuParamHelper::validate_form_id($_POST['id']);

    if ( ! $id ) {
        die('sanitize error 1 = ' . $id);
    }

    $found_form = FormzuOptionHandler::find_option('form_data', array('id' => $id));

    // Fixed on 2026.08.25
    // - form_dataの検証を追加
    if ( ! $found_form ) {
        die('not found form');
    }
    if ( ! isset($found_form['item']['name']) || ! is_string($found_form['item']['name']) ) {
        die('invalid form data');
    }

    $form_name = sanitize_text_field($found_form['item']['name']);

    $menu_item_data = array(
        'menu-item-title' => $form_name,
    );

    $item_ids   = array();
    $item_ids[] = wp_update_nav_menu_item(0, 0, $menu_item_data);

    // Fixed on 2026.08.19
    // - メニュー項目の作成に失敗した場合のWP_Error処理を追加
    if ( is_wp_error($item_ids[0]) || ! is_int($item_ids[0]) || $item_ids[0] <= 0 ) {
        die('menu item error');
    }

    $menu_obj = get_post($item_ids[0]);
    $menu_obj = wp_setup_nav_menu_item($menu_obj);

    $menu_obj->label      = $menu_obj->title;
    $menu_obj->type_label = 'フォームズ フォームリンク';
    $menu_obj->url        = FORMZU_FORM_URL . $found_form['item']['id'] . '/';
    $menu_obj->type       = 'formzu_link';
    $menu_obj->object     = 'post_type_formzu_link';

    FormzuOptionHandler::update_option('new_navmenu_item_id', strval($item_ids[0]));
    FormzuOptionHandler::update_option('new_navmenu_form_url', FORMZU_FORM_URL . $found_form['item']['id'] . '/');

    $menu_items = array();
    $menu_items[] = $menu_obj;

    $args = array(
        'after'       => '',
        'before'      => '',
        'link_after'  => '',
        'link_before' => '',
        'walker'      => new Walker_Nav_Menu_Edit(),
    );

    do_action( 'save_post_' . $menu_obj->post_type, $item_ids[0], $menu_items[0]);
    set_navmenu_hidden_input();
    die( walk_nav_menu_tree( $menu_items, 0, (object) $args ) );
}


function set_navmenu_hidden_input() {
    // Added on 2026.08.10
    // - 権限(Capability)の確認
    //   必要な権限: edit_theme_options
    if ( ! current_user_can('edit_theme_options') ) {
        return false;
    }

    $formzu_link_items = get_formzu_link_items();
    $formzu_link_items = add_new_formzu_link_item($formzu_link_items);

    if ( empty($formzu_link_items) ) {
        return false;
    }

    global $wp_version;

    if ( version_compare(phpversion(), '5.3.0', '>=') ) {
        $formzu_link_items = json_encode($formzu_link_items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    else {
        $formzu_link_items = json_encode($formzu_link_items);
    }

    ?>
    <script>
        (function($){
            $(document).ready(function(){

                var formzu_link_items = <?php echo $formzu_link_items ?>;

                if ( ! formzu_link_items || ! (formzu_link_items instanceof Array) ) {
                    return false;
                }

                for (var i = 0, l = formzu_link_items.length; i < l; i++) {

                    var item = formzu_link_items[i];

                    if ( !item || !item.item_id || !item.form_url ) {
                        continue;
                    }

                    var item_id  = formzu_link_items[i].item_id;
                    var form_url = formzu_link_items[i].form_url;

                    if ( item_id === '' || form_url === '') {
                        continue;
                    }

                    var $settings_elem = $('#menu-item-settings-' + item_id);

                    var $hidden_url = $('<input>').attr({
                        'type' : 'hidden',
                        'id'   : 'edit-menu-item-url-' + item_id,
                        'name' : 'menu-item-url[' + item_id + ']',
                        'value': form_url
                    });
                    $settings_elem.append($hidden_url);
                }
            });
        })(jQuery);
    </script>
    <?php
}


// Fixed on 2026.08.21
// - 存在しないメニューが残っていることによるPHP Warningを防止
function get_formzu_link_items() {
    $locations = get_nav_menu_locations();

    if ( ! is_array($locations) || empty($locations) ) {
        return  array();
    }

    $formzu_link_items = array();

    foreach ($locations as $term_id) {

        if ( ! is_scalar($term_id) ) {
            continue;
        }

        $menu = wp_get_nav_menu_object($term_id);

        if (
            ! $menu
            || is_wp_error($menu)
            || ! isset($menu->term_id)
            || ! is_scalar($menu->term_id)
        ) {
            continue;
        }

        $menu_items = wp_get_nav_menu_items($menu->term_id);

        if ( ! is_array($menu_items) ) {
            continue;
        }

        foreach ($menu_items as $menu_item) {
            if (
                ! is_object($menu_item)
                || ! isset($menu_item->object)
                || $menu_item->object !== 'post_type_formzu_link'
                || ! isset($menu_item->ID)
                || ! is_scalar($menu_item->ID)
                || ! isset($menu_item->url)
                || ! is_string($menu_item->url)
            ) {
                continue;
            }

            $item_id  = get_post_meta($menu_item->ID, '_menu_item_object_id', true);
            $form_url = $menu_item->url;

            if (
                ! is_scalar($item_id)
                || empty($item_id)
                || empty($form_url)
            ) {
                continue;
            }

            $formzu_link_items[] = array(
                'item_id'  => $item_id,
                'form_url' => $form_url,
            );

        }

    }
    return $formzu_link_items;
}


function add_new_formzu_link_item($formzu_link_items) {
    $action = FormzuParamHelper::get_REQ('action', false);

    if ( $action !== FORMZU_NAVMENU_NONCE ) {
        return $formzu_link_items;
    }

    $item_id  = FormzuOptionHandler::get_option('new_navmenu_item_id');
    $form_url = FormzuOptionHandler::get_option('new_navmenu_form_url');

    if ( empty($item_id) || empty($form_url) ) {
        return $formzu_link_items;
    }

    $formzu_link_items[] = array(
        'item_id'  => $item_id,
        'form_url' => $form_url,
    );

    FormzuOptionHandler::delete_option('new_navmenu_item_id');
    FormzuOptionHandler::delete_option('new_navmenu_form_url');

    return $formzu_link_items;
}
