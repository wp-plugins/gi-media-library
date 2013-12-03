<?php

add_action( 'admin_init', 'gi_medialibrary_admin_init' );

add_action( 'admin_menu', 'gi_medialibrary_admin' );

add_action('wp_ajax_giml_get_groups', 'giml_get_groups');

add_action('wp_ajax_giml_group_add', 'giml_group_add');
add_action('wp_ajax_giml_group_edit', 'giml_group_edit');
add_action('wp_ajax_giml_group_delete', 'giml_group_delete');
add_action('wp_ajax_giml_group_update', 'giml_group_update');

add_action('wp_ajax_giml_get_shortcodedata', 'giml_get_shortcodedata');
add_action('wp_ajax_giml_get_subgroups', 'giml_get_subgroups');

add_action('wp_ajax_giml_subgroup_add', 'giml_subgroup_add');
add_action('wp_ajax_giml_subgroup_edit', 'giml_subgroup_edit');
add_action('wp_ajax_giml_subgroup_delete', 'giml_subgroup_delete');
add_action('wp_ajax_giml_subgroup_update', 'giml_subgroup_update');

add_action('wp_ajax_giml_get_playlistdata', 'giml_get_playlistdata');
add_action('wp_ajax_giml_get_playlistcombosections', 'giml_get_playlistcombosections');
add_action('wp_ajax_giml_get_playlistcombosectioncolumns', 'giml_get_playlistcombosectioncolumns');
add_action('wp_ajax_giml_get_playlistcolumns', 'giml_get_playlistcolumns');
add_action('wp_ajax_giml_insert', 'giml_insert');
add_action('wp_ajax_giml_update', 'giml_update');
add_action('wp_ajax_giml_edit', 'giml_edit');
add_action('wp_ajax_giml_delete', 'giml_delete');



//add_action( 'admin_notices', 'my_admin_notice' );

function gi_medialibrary_admin_init() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );
	
	If ( version_compare( $wp_version, '3.3.2', '<') ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin ); 
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3.2 or higher! Deactivating Plugin.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}

function gi_medialibrary_admin() {
	if (!wp_script_is('jquery-ui-tabs', 'queue')) wp_enqueue_script( 'jquery-ui-tabs' );
	if (!wp_script_is('jquery-ui-dialog', 'queue')) wp_enqueue_script( 'jquery-ui-dialog' );
	if (!wp_style_is('jquery-ui-css','queue')) {
		if (!wp_style_is('jquery-ui-css','registered')) {
			wp_register_style( 'jquery-ui-css',  plugins_url( 'css/jquery-ui.css', dirname(__FILE__) ) );//'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
		}
		wp_enqueue_style( 'jquery-ui-css' );
	}
	if (!wp_style_is('gi-style','queue')) {
		if (!wp_style_is('gi-style','registered')) {
			wp_register_style( 'gi-style', plugins_url( 'css/gi.css', dirname(__FILE__) ) );
		}
		wp_enqueue_style( 'gi-style');
	}
	
	add_options_page( 'GI-Media Library', 'GI-Media Library', 'manage_options', 'gi_medialibrary', 'gi_medialibrary_page');	
}

function gi_medialibrary_page() {
	// $tpl = file_get_contents( dirname(__FILE__) . '/tpl/admin.tpl');
	// print $tpl;
	
	//add_action( 'wp_ajax_group_add', 'group_add_callback');

	include_once( 'admin-template.php' );
	
}

function my_admin_notice($msg) {
	echo '<div class="updated"><p>' . $msg . '</p></div>';
}
?>