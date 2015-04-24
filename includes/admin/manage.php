<?php

/**
 * 
 *
 * @author Zishan J.
 */

defined( 'ABSPATH' ) OR exit;

class GIML_Manage {
    private $menus_tabs = array();
    private $menus_group_key = "group/subgroup";
    private $menus_playlist_key = "playlist";
    private $menus_about_key = "about";
    private $menus_key = "gi-medialibrary";
    private $menus_settings_key = "settings";
    private $current_tab;
    
    function __construct() {
        include(GIML_INCLUDES . 'base/view.php');

        //$this->menus_tabs[$this->menus_managecategories_key] = "Manage Categories";
        $this->menus_tabs[$this->menus_group_key] = "Group/Subgroup";
        $this->menus_tabs[$this->menus_playlist_key] = "Playlist";
        $this->menus_tabs[$this->menus_settings_key] = "Settings";
        $this->menus_tabs["separator1"] = "";
        $this->menus_tabs["separator2"] = "";
        $this->menus_tabs[$this->menus_about_key] = "About";

        $this->current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->menus_group_key;
        $_GET['tab'] = $this->current_tab;
    }
    
    function manage_tabs() {
        
        echo '<h2 class="nav-tab-wrapper">GI-Media Library<span class="separator"></span>';
        foreach ($this->menus_tabs as $tab_key => $tab_caption) {
            if (strpos($tab_key, 'separator')===false) {
                $active = $this->current_tab == $tab_key ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menus_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
            } else {
                echo '<span class="separator"></span>';
            }
        }
        echo '</h2>';
        echo '<br/>';

        switch ($this->current_tab) {
            case $this->menus_group_key:
                $this->page_group();
                break;
            case $this->menus_about_key:
                $this->page_about();
                break;
            case $this->menus_playlist_key:
                $this->page_playlist();
                break;
            case $this->menus_settings_key;
                $this->page_settings();
                break;
            default:
                break;
        }
    }
    
    private function page_group() {
        wp_enqueue_script('accordion');
        wp_enqueue_script('jquery-ui-position');
        wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/admin/js/vendors/jquery.scrollTo.min.js');
        //wp_enqueue_script('jquery.validation', GIRESTT_URI . 'js/jquery.validate.min.js');
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        //having conflict with underscore used by WP Media Library
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        
        wp_enqueue_script('admin-app', GIML_URI . 'includes/views/admin/js/app.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
                
        wp_enqueue_script('groups-controller', GIML_URI . 'includes/views/admin/js/controllers/groups.js');
        wp_enqueue_script('subgroups-controller', GIML_URI . 'includes/views/admin/js/controllers/subgroups.js');
        
        wp_enqueue_script('tinymce', '//tinymce.cachefly.net/4.1/tinymce.min.js');
        wp_enqueue_script('angularjs-tinymce', GIML_URI . 'includes/views/admin/js/vendors/ng-tinymce.js');
        
        wp_enqueue_script('angularjs-ui-select', GIML_URI . 'js/ui-select.min.js');
        wp_enqueue_style('angularjs-ui-select', GIML_URI . 'css/ui-select.css');
        wp_enqueue_style('angularjs-select2', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css');
        
        $group = new GIML_Group();
                
        $subgroup = new GIML_Subgroup();
        
        wp_localize_script( 'admin-app', 'gimlData',
                ['URI' => GIML_URI,
                'nonce' => GIML_NONCE,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'groups' => $group->get(),
                'subgroups' => null]);

        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-ui-css', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
        wp_enqueue_style('giml-admin-style', GIML_URI . 'includes/views/admin/css/admin.css');
        
        add_thickbox();
        
        $this->print_error();
        $this->print_success();

        //$cat = new GI_Catalog_Category();
        
        $view = new GIML_View("admin/group");
        //$view->set("categories", $cat->get_categories());
        $view->set("menu", (isset($_POST['menu'])) ? $_POST['menu'] : 'groups');
        
        
        
        $view->render();
    }
    
    private function page_playlist() {
        wp_enqueue_script('accordion');
        wp_enqueue_script('jquery-ui-position');
        wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/admin/js/vendors/jquery.scrollTo.min.js');
        //wp_enqueue_script('jquery.validation', GIRESTT_URI . 'js/jquery.validate.min.js');
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        //having conflict with underscore used by WP Media Library
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
        
        wp_enqueue_script('admin-app', GIML_URI . 'includes/views/admin/js/app.js');
                
        wp_enqueue_script('playlists-controller', GIML_URI . 'includes/views/admin/js/controllers/playlists.js');
        wp_enqueue_script('combo-controller', GIML_URI . 'includes/views/admin/js/controllers/combo.js');
        wp_enqueue_script('table-controller', GIML_URI . 'includes/views/admin/js/controllers/table.js');
        wp_enqueue_script('section-controller', GIML_URI . 'includes/views/admin/js/controllers/section.js');
        wp_enqueue_script('sectioncolumn-controller', GIML_URI . 'includes/views/admin/js/controllers/sectioncolumn.js');
        wp_enqueue_script('table-component', GIML_URI . 'includes/views/admin/js/components/table.js');
        wp_enqueue_script('column-controller', GIML_URI . 'includes/views/admin/js/controllers/column.js');
        
        wp_enqueue_script('tinymce', '//tinymce.cachefly.net/4.1/tinymce.min.js');
        wp_enqueue_script('angularjs-tinymce', GIML_URI . 'includes/views/admin/js/vendors/ng-tinymce.js');
        
        wp_enqueue_script('angularjs-ui-select', GIML_URI . 'js/ui-select.min.js');
        wp_enqueue_style('angularjs-ui-select', GIML_URI . 'css/ui-select.css');
        wp_enqueue_style('angularjs-select2', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css');
        
        $group = new GIML_Group();
                
        wp_localize_script( 'admin-app', 'gimlData',
                ['URI' => GIML_URI,
                'nonce' => GIML_NONCE,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'groups' => $group->get(),
                'subgroups' => null]);

        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-ui-css', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
        wp_enqueue_style('giml-admin-style', GIML_URI . 'includes/views/admin/css/admin.css');
        
        add_thickbox();
        
        $this->print_error();
        $this->print_success();

        //$cat = new GI_Catalog_Category();
        
        $view = new GIML_View("admin/playlist");
        //$view->set("categories", $cat->get_categories());
        $view->set("menu", (isset($_POST['menu'])) ? $_POST['menu'] : 'playlist');
                
        $view->render();
    }
    
    private function page_about() {
        wp_enqueue_script('postbox');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/admin/js/vendors/jquery.scrollTo.min.js');
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        //having conflict with underscore used by WP Media Library
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
        
        wp_enqueue_script('admin-app', GIML_URI . 'includes/views/admin/js/app.js');
        wp_localize_script( 'admin-app', 'gimlData',
                ['URI' => GIML_URI,
                'nonce' => GIML_NONCE,
                'ajax_url' => admin_url( 'admin-ajax.php' )]);
        
        wp_enqueue_script('tinymce', '//tinymce.cachefly.net/4.1/tinymce.min.js');
        wp_enqueue_script('angularjs-tinymce', GIML_URI . 'includes/views/admin/js/vendors/ng-tinymce.js');
        
        wp_enqueue_script('angularjs-ui-select', GIML_URI . 'js/ui-select.min.js');
        wp_enqueue_style('angularjs-ui-select', GIML_URI . 'css/ui-select.css');
        wp_enqueue_style('angularjs-select2', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css');
        
        wp_enqueue_script('about-controller', GIML_URI . 'includes/views/admin/js/controllers/about.js');
        
        wp_enqueue_style('jquery-ui', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
        wp_enqueue_style('giml-admin-style', GIML_URI . 'includes/views/admin/css/admin.css');
        
        $view = new GIML_View("admin/about");
        //$view->set("categories", $cat->get_categories());
        //$view->set("menu", (isset($_POST['menu'])) ? $_POST['menu'] : 'groups');
        
        $view->render();
    }
    
    private function page_settings() {
        wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/admin/js/vendors/jquery.scrollTo.min.js');
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        //having conflict with underscore used by WP Media Library
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
        
        wp_enqueue_script('admin-app', GIML_URI . 'includes/views/admin/js/app.js');
        
        $file = GIML_INCLUDES . 'views/site/templates/search-result.php';
        $error = false;
        if ( ! is_file( $file ) )
            $error = true;

	$content = '';
	if ( ! $error && filesize( $file ) > 0 ) {
            $f = fopen($file, 'r');
            $content = fread($f, filesize($file));
        }
        wp_localize_script( 'admin-app', 'gimlData',
                ['URI' => GIML_URI,
                'nonce' => GIML_NONCE,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'settings' => [
                    'search_bar_caption' => get_site_option('giml_search_bar_caption'),
                    'search_page_title' => get_site_option('giml_search_page_title'),
                    'disable_jqueryui_css' => get_site_option('giml_disable_jqueryui_css'),
                    'disable_bootstrap_css' => get_site_option("giml_disable_bootstrap_css"),
                    'player_color' => get_site_option('giml_player_color'),
                    'template' => $content
                ]
                ]);
        
        wp_enqueue_script('angularjs-tinymce', GIML_URI . 'includes/views/admin/js/vendors/ng-tinymce.js');
        
        wp_enqueue_script('angularjs-ui-select', GIML_URI . 'js/ui-select.min.js');
        wp_enqueue_style('angularjs-ui-select', GIML_URI . 'css/ui-select.css');
        wp_enqueue_style('angularjs-select2', 'http://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css');
        
        wp_enqueue_script('settings-controller', GIML_URI . 'includes/views/admin/js/controllers/settings.js');
        
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-ui', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
        wp_enqueue_style('giml-admin-style', GIML_URI . 'includes/views/admin/css/admin.css');
        
        $view = new GIML_View("admin/settings");
        $view->render();
    }
    
    private function print_error() {
        if (!empty($this->error))
            echo '<div class="error"><p>' . $this->error . '</p></div>';

        $this->error = "";
    }

    private function print_success() {
        if (!empty($this->success))
            echo '<div class="updated"><p>' . $this->success . '</p></div>';

        $this->success = "";
    }
}