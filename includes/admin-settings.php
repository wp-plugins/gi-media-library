<?php

add_action('admin_init', 'gi_medialibrary_admin_init');

add_action('admin_menu', 'gi_medialibrary_admin_menu');

//add_action( 'admin_notices', 'my_admin_notice' );

function gi_medialibrary_admin_init() {
    global $wp_version;
    $plugin = plugin_basename(__FILE__);
    $plugin_data = get_plugin_data(__FILE__, false);

    If (version_compare($wp_version, '3.4.2', '<')) {
        if (is_plugin_active($plugin)) {
            deactivate_plugins($plugin);
            wp_die("'" . $plugin_data['Name'] . "' requires WordPress 3.4.2 or higher! Deactivating Plugin.<br /><br />Back to <a href='" . admin_url() . "'>WordPress admin</a>.");
        }
    }

    $pages_with_editor_button = array('post.php', 'post-new.php');
    foreach ($pages_with_editor_button as $editor_page) {
        add_action("load-{$editor_page}", 'giml_add_editor_buttons');
    }

    add_action( 'load-plugins.php', 'giml_plugins_page' );
            
    giml_load_admin_actions();
}

function gi_medialibrary_admin_menu() {
    $hook_suffix = add_options_page('GI-Media Library', 'GI-Media Library', 'manage_options', 'gi_medialibrary', 'gi_medialibrary_page');
    //add_action('load-' . $hook_suffix, 'gi_load_admin_actions');
}

function giml_add_editor_buttons() {
    if (user_can_richedit()) {
        add_filter('mce_external_plugins', 'giml_mce_external_plugins');
        add_filter('mce_buttons', 'giml_mce_buttons');
        //add_filter( 'wp_title', 'mypage_title', 10, 3 );
    }
}

function gi_medialibrary_page() {
    if (!wp_script_is('postbox', 'queue'))
        wp_enqueue_script('postbox');
    if (!wp_script_is('jquery-ui-tabs', 'queue'))
        wp_enqueue_script('jquery-ui-tabs');
    if (!wp_script_is('jquery-ui-dialog', 'queue'))
        wp_enqueue_script('jquery-ui-dialog');
    if (!wp_style_is('jquery-ui-css', 'queue')) {
        if (!wp_style_is('jquery-ui-css', 'registered')) {
            wp_register_style('jquery-ui-css', GIML_URI . 'css/jquery-ui.css'); //'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
        }
        wp_enqueue_style('jquery-ui-css');
    }
    if (!wp_style_is('gi-style', 'queue')) {
        if (!wp_style_is('gi-style', 'registered')) {
            wp_register_style('gi-style', GIML_URI . 'css/gi.css');
        }
        wp_enqueue_style('gi-style');
    }
    if (!wp_style_is('giml-admin-style', 'queue')) {
        if (!wp_style_is('giml-admin-style', 'registered')) {
            wp_register_style('giml-admin-style', GIML_URI . 'css/admin.css');
        }
        wp_enqueue_style('giml-admin-style');
    }
    
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST['giml-admin-comment'])) {
            if (trim($_POST['giml-admin-comment'])!=='') {
                wp_mail('info@glareofislam.com', "GIML User Message from " . get_site_option('siteurl'), $_POST['giml-admin-comment']);
                print '<div class="updated"><p>Message sent successfully.</p></div>';
            }else{
                print '<div class="error"><p>Error: Message is left blank.</p></div>';
            }
        }
    }
    
    include_once( 'admin-template.php' );
}

function giml_mce_external_plugins($plugin_array) {
    if (!wp_script_is('jquery-ui-dialog', 'queue'))
        wp_enqueue_script('jquery-ui-dialog');
    if (!wp_style_is('jquery-ui-css', 'queue')) {
        if (!wp_style_is('jquery-ui-css', 'registered')) {
            wp_register_style('jquery-ui-css', GIML_URI . 'css/jquery-ui.css'); //'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
        }
        wp_enqueue_style('jquery-ui-css');
    }
    if (!wp_style_is('gi-style', 'queue')) {
        if (!wp_style_is('gi-style', 'registered')) {
            wp_register_style('gi-style', GIML_URI . 'css/gi.css');
        }
        wp_enqueue_style('gi-style');
    }
    $plugin_array['gi_medialibrary'] = apply_filters('giml_editor_plugin_load', plugins_url('js/editor_plugin.js', dirname(__FILE__)));
    return $plugin_array;
}

function giml_mce_buttons($buttons) {
    array_push($buttons, "|", "gi_medialibrary");
    return $buttons;
}

function giml_load_admin_actions() {
    $ajax_actions = array('giml_get_groups', 'giml_group_add', 'giml_group_edit', 'giml_group_delete', 'giml_group_update', 'giml_get_shortcodedata',
        'giml_get_subgroups', 'giml_subgroup_add', 'giml_subgroup_edit', 'giml_subgroup_delete', 'giml_subgroup_update',
        'giml_get_playlistdata', 'giml_get_playlistcombosections', 'giml_get_playlistcombosectioncolumns', 'giml_get_playlistcolumns',
        'giml_insert', 'giml_update', 'giml_edit', 'giml_delete', 'giml_change_search', 'nopriv_giml_change_search');
    foreach ($ajax_actions as $action) {
        add_action("wp_ajax_{$action}", $action);
    }
}

function giml_plugins_page() {
    add_filter( 'plugin_action_links_' . GIML_BASENAME, 'giml_add_plugin_action_links' );
    add_filter( 'plugin_row_meta', 'giml_add_description_row_meta', 10, 2 );
}

function giml_add_description_row_meta( array $links, $file ) {
     if (GIML_BASENAME == $file) {
        //$links[] = '<a href="http://glareofislam.com/faq/" title="' . esc_attr__('Frequently Asked Questions', 'giml') . '">' . __('FAQ', 'giml') . '</a>';
         $params['page'] = 'gi_medialibrary#aboutgiml';
        $url = add_query_arg( $params, admin_url( 'options-general.php' ) );
        $links[] = '<a href="' . $url . '">' . __('User\'s Manual', 'giml') . '</a>';
        $links[] = '<a href="http://wordpress.org/support/plugin/gi-media-library">' . __('Support', 'giml') . '</a>';
        $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8" title="' . esc_attr__('Support GI-Media Library with your donation!', 'giml') . '"><strong>' . __('Donate', 'giml') . '</strong></a>';
    }
    return $links;
}

function giml_add_plugin_action_links(array $links) {
    $params['page'] = 'gi_medialibrary';
    $url = add_query_arg( $params, admin_url( 'options-general.php' ) );
    $links[] = '<a href="' . $url . '">' . __( 'Plugin page', 'giml' ) . '</a>';
    return $links;
}
function giml_change_search() {
	
	if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
		include 'shortcode-ajax.php';	
	}

}
function nopriv_giml_change_search() {
    giml_change_search();
}
?>