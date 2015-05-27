<?php

/**
 * 
 *
 * @author Zishan J.
 */

defined('ABSPATH') OR exit;

class GIML_Admin extends GIML_Base {
    
    private static $giml;
    
    function __construct() {
        add_action('admin_init', array(&$this, 'giml_admin_init'));
        add_action('admin_menu', array(&$this, 'giml_admin_menu'));
    }
    
    public static function giml_admin_init() {
        global $wp_version;

        $plugin_data = get_plugin_data(GIML_PLUGIN_ABSPATH, false);

        If (version_compare($wp_version, '3.6', '<')) {
            if (is_plugin_active(GIML_BASENAME)) {
                deactivate_plugins(GIML_BASENAME);
                wp_die("'" . $plugin_data['Name'] . "' requires WordPress 3.6 or higher! Deactivating plugin.<br /><br />Back to <a href='" . admin_url() . "'>WordPress admin</a>.");
            }
        }

        include(GIML_INCLUDES . 'models/group.php');
        include(GIML_INCLUDES . 'models/subgroup.php');
        include(GIML_INCLUDES . 'models/combo.php');
        include(GIML_INCLUDES . 'models/table.php');
        include(GIML_INCLUDES . 'models/section.php');
        include(GIML_INCLUDES . 'models/sectioncolumn.php');
        include(GIML_INCLUDES . 'models/column.php');

        $pages_with_editor_button = array('post.php', 'post-new.php');
        foreach ($pages_with_editor_button as $editor_page) {
            add_action("load-{$editor_page}", array(__CLASS__, 'giml_add_editor_buttons'));
        }
        
        add_action( 'load-plugins.php', array(__CLASS__, 'giml_plugins_page') );
        
        //TODO: increase nonce lifetime... following filter not working
        //add_filter( 'nonce_life', function () { return 168 * HOUR_IN_SECONDS; } );
        
        self::giml_load_admin_actions();
    }
    
    public function giml_admin_menu() {
        $hook_suffix = add_options_page(__('GI-Media Library', 'menu-giml'), __('GI-Media Library', 'menu-giml'), 'manage_options', 'gi-medialibrary', array(__CLASS__, 'giml_page'));
        add_action('load-' . $hook_suffix, array(__CLASS__, 'giml_manage'));
    }
    
    public static function giml_add_editor_buttons() {
        add_action('admin_print_footer_scripts', array(__CLASS__, 'giml_shortcode_html'));
        
        if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) && user_can_richedit() ) {
            add_filter( 'mce_buttons', array(__CLASS__, 'giml_mce_buttons') );
            add_filter( 'mce_external_plugins', array(__CLASS__, 'giml_mce_external_plugins') );
        }
    }
    
    public static function giml_plugins_page() {
        add_filter( 'plugin_action_links_' . GIML_BASENAME, array(__CLASS__, 'giml_add_plugin_action_links') );
        add_filter( 'plugin_row_meta', array(__CLASS__, 'giml_add_description_row_meta'), 10, 2 );
    }
    
    public static function giml_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        echo '<div class="wrap">';
        self::$giml->manage_tabs();
        echo '<br class="clear" />';
        echo "</div>";
    }
    
    public static function giml_manage() {
        include_once GIML_INCLUDES . 'admin/manage.php';

        self::$giml = new GIML_Manage();
    }
    
    public static function giml_mce_buttons($buttons) {
        array_push($buttons, "|", "btn_gimedialibrary");
        return $buttons;
    }
    
    public static function giml_mce_external_plugins($plugin_array) {
        wp_enqueue_script('jquery-ui-dialog');
        //wp_enqueue_script('jquery.validation', GIRESTT_URI . 'js/jquery.validate.min.js');
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        //having conflict with underscore used by WP Media Library
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
        
        wp_enqueue_script('giml-admin-app', GIML_URI . 'includes/views/admin/js/app.js');
        
        wp_enqueue_script('angularjs-tinymce', GIML_URI . 'includes/views/admin/js/vendors/ng-tinymce.js');
        
        wp_enqueue_script('angularjs-ui-select', GIML_URI . 'js/ui-select.min.js');
        wp_enqueue_style('angularjs-ui-select', GIML_URI . 'css/ui-select.css');
        wp_enqueue_style('angularjs-select2', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css');
        
        wp_localize_script( 'giml-admin-app', 'gimlData',
                ['URI' => GIML_URI,
                'nonce' => GIML_NONCE,
                'ajax_url' => admin_url( 'admin-ajax.php' )]);
        
        wp_enqueue_script('giml-admin-controller-shortcode', GIML_URI . 'includes/views/admin/js/controllers/shortcode.js');
        
        wp_enqueue_style('giml-jquery-ui-css', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
        
        
        $plugin_array['gi_medialibrary'] = GIML_URI . 'includes/views/admin/js/editor_plugin.js';
        return $plugin_array;
    }
    
    public static function giml_shortcode_html() {
        $script = file_get_contents(GIML_URI . 'includes/views/admin/templates/shortcode.html');
        echo $script;
    }
    
    public static function giml_load_admin_actions() {
        $ajax_actions = ['giml_group_add', 'giml_group_update', 'giml_group_delete', 'giml_group_subgroups_get', 'giml_groups_get',
            'giml_subgroup_add', 'giml_subgroup_update', 'giml_subgroup_delete', 'giml_subgroup_downloadlink_update',
            'giml_playlist_get',
            'giml_combo_update', 'giml_combo_items_add', 'giml_combo_items_update', 'giml_combo_items_delete',
            'giml_table_update', 'giml_table_columns_add', 'giml_table_columns_update', 'giml_table_columns_delete',
            'giml_comboitem_sections_get', 'giml_section_add', 'giml_section_update', 'giml_section_delete',
            'giml_section_columns_get', 'giml_section_columns_add', 'giml_section_columns_update', 'giml_section_columns_delete',
            'giml_columns_get', 'giml_columns_add', 'giml_columns_update', 'giml_columns_delete',
            'giml_save_settings',
            'giml_table_playlist_get', 'nopriv_giml_table_playlist_get',
            'giml_search_result_page_changed', 'nopriv_giml_search_result_page_changed',
	    'giml_send_message'];
        
        foreach ($ajax_actions as $action) {
            add_action("wp_ajax_{$action}", array(__CLASS__, $action));
        }
    }
    
    public static function giml_add_description_row_meta( array $links, $file ) {
        if (GIML_BASENAME == $file) {
            //$links[] = '<a href="http://glareofislam.com/faq/" title="' . esc_attr__('Frequently Asked Questions', 'giml') . '">' . __('FAQ', 'giml') . '</a>';
            $params['page'] = 'gi-medialibrary#aboutgiml';
            $url = add_query_arg( $params, admin_url( 'options-general.php' ) );
            $links[] = '<a href="' . $url . '">' . __('User\'s Manual', 'giml') . '</a>';
            $links[] = '<a href="http://wordpress.org/support/plugin/gi-media-library">' . __('Support', 'giml') . '</a>';
            $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8" title="' . esc_attr__('Support GI-Media Library with your donation!', 'giml') . '"><strong>' . __('Donate', 'giml') . '</strong></a>';
        }
        return $links;
    }

    public static function giml_add_plugin_action_links(array $links) {
        $params['page'] = 'gi-medialibrary';
        $url = add_query_arg( $params, admin_url( 'options-general.php' ) );
        $links[] = '<a href="' . $url . '">' . __( 'Plugin page', 'giml' ) . '</a>';
        return $links;
    }
    
    //AJAX Functions
    
    public static function giml_group_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $group = new GIML_Group();
            $result = $group->add($_POST['rows']);
            
            $data = [
                'groups' => $group->get()
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Group(s) added successfully.', 'giml')), 'data' => $data]));
            }
        }
    }

    public static function giml_group_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $group = new GIML_Group();
            $result = $group->update($_POST['rows']);
            
            $data = [
                'groups' => $group->get()
            ];

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Group(s) updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }

    public static function giml_group_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $group = new GIML_Group();
            $result = $group->delete($_POST['ids']);
            
            $data = [
                'groups' => $group->get()
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Group(s) deleted successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_groups_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $group = new GIML_Group();
            die(json_encode(['success' => true, 'message' => self::print_success(__('Groups retrieved successfully.', 'giml')), 'data' => $group->get()]));
        }
    }
    
    public static function giml_group_subgroups_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $subgroup = new GIML_Subgroup();
            die(json_encode(['success' => true, 'message' => self::print_success(__('Subgroups retrieved successfully.', 'giml')), 'data' => $subgroup->get_groupsubgroups($_POST['groupid'])]));
        }
    }
    
    public static function giml_subgroup_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $subgroup = new GIML_Subgroup();
            $result = $subgroup->add($_POST['rows']);
            
            $data = [
                'subgroups' => ($_POST['groupid'] == '')?null:$subgroup->get_groupsubgroups($_POST['groupid'])
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Subgroup(s) added successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_subgroup_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $subgroup = new GIML_Subgroup();
            $result = $subgroup->update($_POST['rows']);
            
            $data = [
                'subgroups' => ($_POST['groupid'] == '')?null:$subgroup->get_groupsubgroups($_POST['groupid'])
            ];

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Subgroup(s) updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_subgroup_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $subgroup = new GIML_Subgroup();
            $result = $subgroup->delete($_POST['ids']);
            
            $data = [
                'subgroups' => ($_POST['groupid'] == '')?null:$subgroup->get_groupsubgroups($_POST['groupid'])
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Subgroup(s) deleted successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_subgroup_downloadlink_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $subgroup = new GIML_Subgroup();
            $result = $subgroup->update($_POST['data']);
            
            $data = null;

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_playlist_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $combo = new GIML_Combo();
            $table = new GIML_Table();
            $data = [
                'combo'=>$combo->get($_POST['subgroupid']),
                'combo_items'=>$combo->get_items($_POST['subgroupid'], false),
                'table' => $table->get($_POST['subgroupid']),
                'table_columns' => $table->get_columns($_POST['subgroupid'], false)
            ];
            die(json_encode(['success' => true, 'message' => self::print_success(__('Data retrieved successfully.', 'giml')), 'data' => $data]));
        }
    }
    
    public static function giml_combo_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $combo = new GIML_Combo();
            $result = $combo->update($_POST['data']);
            
            $data = null;

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_combo_items_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $combo = new GIML_Combo();
            $result = $combo->items_update($_POST['rows']);
            
            $data = [
                'items' => $combo->get_items($_POST['subgroupid'], false)
            ];

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Combo item(s) updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_combo_items_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $combo = new GIML_Combo();
            $result = $combo->items_add($_POST['rows']);
            
            $data = [
                'items' => $combo->get_items($_POST['subgroupid'], false)
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Combo item(s) added successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_combo_items_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $combo = new GIML_Combo();
            $result = $combo->items_delete($_POST['ids']);
            
            $data = [
                'items' => $combo->get_items($_POST['subgroupid'], false)
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Combo item(s) deleted successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_table_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $table = new GIML_Table();
            $result = $table->update($_POST['data']);
            
            $data = null;

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_table_columns_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $table = new GIML_Table();
            $result = $table->columns_update($_POST['rows']);
            
            $data = [
                'columns' => $table->get_columns($_POST['subgroupid'], false)
            ];

            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Table column(s) updated successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_table_columns_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $table = new GIML_Table();
            $result = $table->columns_add($_POST['rows']);
            
            $data = [
                'columns' => $table->get_columns($_POST['subgroupid'], false)
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Table column(s) added successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_table_columns_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            $table = new GIML_Table();
            $result = $table->columns_delete($_POST['ids']);
            
            $data = [
                'columns' => $table->get_columns($_POST['subgroupid'], false)
            ];
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => $data]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Table column(s) deleted successfully.', 'giml')), 'data' => $data]));
            }
        }
    }
    
    public static function giml_comboitem_sections_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $section = new GIML_Section();
            die(json_encode(['success' => true, 'message' => self::print_success(__('Sections retrieved successfully.', 'giml')), 'data' => $section->get($tableid, $comboitemid)]));
        }
    }
    
    public static function giml_section_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $section = new GIML_Section();
            $result = $section->add($rows);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Section(s) added successfully.', 'giml')), 'data' => $section->get($tableid, $comboitemid, false)]));
            }
        }
    }
    
    public static function giml_section_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $section = new GIML_Section();
            $result = $section->update($rows);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Section(s) updated successfully.', 'giml')), 'data' => $section->get($tableid, $comboitemid, false)]));
            }
        }
    }
    
    public static function giml_section_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $section = new GIML_Section();
            $result = $section->delete($ids);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Section(s) deleted successfully.', 'giml')), 'data' => $section->get($tableid, $comboitemid, false)]));
            }
        }
    }
    
    public static function giml_section_columns_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $seccol = new GIML_SectionColumn();
            die(json_encode(['success' => true, 'message' => self::print_success(__('Section columns data retrieved successfully.', 'giml')), 'data' => $seccol->get($sectionId, $subgroupId)]));
        }
    }
    
    public static function giml_section_columns_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $seccol = new GIML_SectionColumn();
            $result = $seccol->update($rows);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Section columns data updated successfully.', 'giml')), 'data' => $seccol->get($sectionId, $subgroupId)]));
            }
        }
    }
    
    public static function giml_section_columns_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $seccol = new GIML_SectionColumn();
            $result = $seccol->add($rows);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Section columns data added successfully.', 'giml')), 'data' => $seccol->get($sectionId, $subgroupId)]));
            }
        }
    }
    
    public static function giml_section_columns_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $seccol = new GIML_SectionColumn();
            $result = $seccol->delete($sectionId);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Section columns data deleted successfully.', 'giml')), 'data' => $seccol->get($sectionId, $subgroupId)]));
            }
        }
    }
    
    public static function giml_columns_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $col = new GIML_Column();
            die(json_encode(['success' => true, 'message' => self::print_success(__('Columns data retrieved successfully.', 'giml')), 'data' => $col->get($sectionId, $subgroupId)]));
        }
    }
    
    public static function giml_columns_update() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $col = new GIML_Column();
            $result = $col->update($rows);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Columns data updated successfully.', 'giml')), 'data' => $col->get($sectionId, $subgroupId)]));
            }
        }
    }
    
    public static function giml_columns_add() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $col = new GIML_Column();
            $result = $col->add($rows);
            
            if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Columns data added successfully.', 'giml')), 'data' => $col->get($sectionId, $subgroupId)]));
            }
        }
    }
    
    public static function giml_columns_delete() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $col = new GIML_Column();
            $result = $col->delete($ids);
            
           if (is_wp_error($result)) {
                self::check_error($result);
                die(json_encode(['success' => false, 'message' => self::print_error(), 'data' => null]));
            } else {
                die(json_encode(['success' => true, 'message' => self::print_success(__('Columns data deleted successfully.', 'giml')), 'data' => $col->get($sectionId, $subgroupId)]));
            }
        }
    }
    
    public static function giml_save_settings() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            foreach ($_POST['settings'] as $key=>$val) {
                if ($key !== 'template')
                    update_site_option('giml_' . $key, sanitize_text_field(trim($val)));
                else {
                    $newcontent = wp_unslash( $val );
                    $file = GIML_INCLUDES . 'views/site/templates/search-result.php';
                    if ( is_writeable( $file ) ) {
                        // is_writable() not always reliable, check return value. see comments @ http://uk.php.net/is_writable
                        $f = fopen( $file, 'w+' );
                        if ( $f !== false ) {
                            fwrite( $f, $newcontent );
                            fclose( $f );
                        }
                    }
                }
            }
            die(json_encode(['success' => true, 'message' => self::print_success(__('Settings saved successfully.', 'giml')), 'data' => []]));
        }
    }
    
    public static function giml_table_playlist_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $section = new GIML_Section();
            $sections = $section->get($tableId, $itemId, false);
            die(json_encode(['success' => true, 'data' => self::giml_table_playlist($subgroupId, $sections, $pageLink)]));
        }
    }
    public static function nopriv_giml_table_playlist_get() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $section = new GIML_Section();
            $sections = $section->get($tableId, $itemId, false);
            die(json_encode(['success' => true, 'data' => self::giml_table_playlist($subgroupId, $sections, $pageLink)]));
        }
    }
    
    public static function giml_search_result_page_changed() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $data = self::search(trim($search_string), $page_link, true, intval($items_per_page), (intval($current_page) * intval($items_per_page)) - intval($items_per_page));
            die(json_encode(['success' => true, 'data' => $data]));
        }
    }
    public static function nopriv_giml_search_result_page_changed() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            extract($_POST);
            $data = self::search(trim($search_string), $page_link, true, intval($items_per_page), intval($current_page) * intval($items_per_page) - intval($items_per_page));
            die(json_encode(['success' => true, 'data' => $data]));
        }
    }
    public static function giml_send_message() {
        if (!empty($_POST) && check_ajax_referer(GIML_NONCE_NAME)) {
            add_filter( 'wp_mail_content_type', array(__CLASS__, 'set_html_content_type') );
            $headers = 'From: ' . $_POST['message']['name'] . ' <' . $_POST['message']['email'] . '>' . "\r\n";
            wp_mail('giml-support@glareofislam.com', $_POST['message']['subject'], 'From Site: ' . get_site_option('siteurl') . "<br/><p>" . nl2br($_POST['message']['message']) . "</p>", $headers);
            remove_filter( 'wp_mail_content_type', array(__CLASS__, 'set_html_content_type'));
            die(json_encode(['success' => true, 'message' => self::print_success(__('Message sent successfully.', 'giml')), 'data' => []]));
            
        }
    }
    public static function set_html_content_type() {
        return 'text/html';
    }
}
