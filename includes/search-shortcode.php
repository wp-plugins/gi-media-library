<?php
/**
 * 
 *
 * @author Zishan J.
 */

defined('ABSPATH') OR exit;

class GIML_Search_Shortcode extends GIML_BASE {
    private static $pageId = null;
    
    private static $default_options = [
        'show_pagination' => 'true',
        'items_per_page' => 10,
        'max_size' => 5
    ];
    
    function __construct() {
        if (isset($_POST['giml-search'])){
            $this->show_search_result($_POST['giml-search']);            
        }
        
        add_shortcode('giml_searchbar', [&$this, 'giml_searchbar']);
        
    }
    
    public static function set_html_content_type() {
        return 'text/html';
    }
    
    public static function giml_searchbar($atts, $content="") {
        
        $myPost = get_post();
        
        $atts = shortcode_atts(self::$default_options, $atts);
        
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
        
        wp_enqueue_script('giml-search-app', GIML_URI . 'includes/views/site/js/search-app.js');
        wp_enqueue_script('giml-ctrl-search', GIML_URI . 'includes/views/site/js/controllers/search.js');
        
        wp_localize_script( 'giml-search-app', 'gimlSearch',
                ['URI' => GIML_URI,
                 'nonce' => GIML_NONCE,
                 'page_link' => get_permalink($myPost->ID),
                 'ajax_url' => admin_url( 'admin-ajax.php' ),
                 /*'query_item_url' => add_query_arg(['giml-id'=>$subgroupid, 'giml-item'=> ''], get_permalink($myPost->ID)),
                 'query_subgroupid_url' => add_query_arg('giml-id', '', get_permalink($myPost->ID)),
                 'data' => $data,*/
                 'search' => [
                    'search_bar_caption' => get_site_option('giml_search_bar_caption'),
                    'show_pagination' => (strtolower($atts['show_pagination'])==='true'),
                    'items_per_page' => (intval($atts['items_per_page'])),
                    'max_size' => (intval($atts['max_size']))
                ]
                ]);
        
        wp_enqueue_script('giml-search-keyboard', GIML_URI . 'includes/views/site/js/vendors/keyboard.js');
        wp_enqueue_style('giml-search-keyboard', GIML_URI . 'includes/views/site/css/keyboard.css');
                
        wp_enqueue_style('giml-site', GIML_URI . 'includes/views/site/css/site.css');
        if (get_site_option('giml_disable_bootstrap_css')==='false') {
            wp_enqueue_style('giml-site-bootstrap', GIML_URI . 'includes/views/site/css/bootstrap/css/bootstrap.css');
            wp_enqueue_style('giml-site-bootstrap-theme', GIML_URI . 'includes/views/site/css/bootstrap/css/bootstrap-theme.min.css');
        }
        wp_enqueue_script('giml-site-bootstrap', GIML_URI . 'includes/views/site/js/vendors/bootstrap.min.js');
        
        $tpl = file_get_contents( GIML_INCLUDES . "views/site/templates/search-bar.html");
        
        return $tpl;
    }
    
    public static function giml_template_function( $template ) {
        if (get_post_type() === 'giml_search') {
            if (isset($_GET['download-file'])) {
                $file = rawurldecode(stripslashes($_GET['download-file']));
                if (isset($_GET[GIML_NONCE_NAME]) && wp_verify_nonce($_GET[GIML_NONCE_NAME], 'download_file_' . $file)) {
                    $fileinfo = pathinfo($file);
                    if($fileinfo['dirname']!==".") {
			$file = htmlspecialchars_decode($file);
                        header('Content-type: application/x-msdownload', true, 200);
                        header("Content-Disposition: attachment; filename=" . basename($file));
                        header("Pragma: no-cache, must-revalidate");
                        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                        @readfile($file);
                    }
                }
            } elseif (isset($_GET['video-file'])) {
                $file = rawurldecode(stripslashes($_GET['video-file']));
                if (isset($_GET[GIML_NONCE_NAME]) && wp_verify_nonce($_GET[GIML_NONCE_NAME], 'video_file_' . $file)) {
                    $src = '<iframe id="giml-video" src="//' . $file . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

                    //TODO: Need to test this output
                    echo $src;
                }
            }
            if (is_single()) {
                
                return GIML_INCLUDES . 'views/site/templates/search-result.php';
            }
        }
        return $template;
    }

    public static function giml_flush_rules(){
	$rules = get_option( 'rewrite_rules' );

	if ( ! isset( $rules['(.*)giml/search/result$'] ) ) {
		global $wp_rewrite;
	   	$wp_rewrite->flush_rules();
	}
    }

    // Adding a new rule
    public static function giml_insert_rewrite_rules( $rules )
    {
            $newrules = array();
            $newrules['(.*)giml/search/result$'] = 'index.php?post_type=giml_search&p=' . self::$pageId;
            return $newrules + $rules;
    }

    // Adding the var so that WP recognizes it
    public static function giml_insert_query_vars( $vars )
    {
        array_push($vars, 'p', 'post_type');
        return $vars;
    }
    
    private function show_search_result() {
        self::$pageId = get_site_option('giml_search_page');
                
        $postType = register_post_type ('giml_search', [
            'labels' => array(
                'name' => 'GIML Search',
            ),
            'public' => true,
            'show_ui' => false,
            'query_var' => false,
            'rewrite' => false,
            'capability_type' => 'page',
            'can_export' => false,
        ]);
        if (!self::$pageId) {
            
            $page = [
                'ID' => false,
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_category' => false,
                'post_content' => '',
                'post_excerpt' => '',
                'post_parent' => 0,
                'post_password' => '',
                'post_status' => 'publish',
                'post_title' => get_site_option('giml_search_page_title'),
                'post_name' => 'giml-search-results',
                'post_type' => 'giml_search',
                'tags_input' => '',
                'to_ping' => ''
            ];

            $pageId = wp_insert_post($page, true);
            if( !is_wp_error( $pageId ) ) {
                self::$pageId = $pageId;
                update_site_option('giml_search_page', $pageId);
            }
        }else{
            wp_update_post([
                'ID' => self::$pageId,
                'post_title' => get_site_option('giml_search_page_title')
                ]);
        }
        
        add_filter( 'template_include', [&$this, 'giml_template_function'], 99 );
        
        //rewrite rules
        add_filter( 'rewrite_rules_array', [&$this, 'giml_insert_rewrite_rules'] );
        add_filter( 'query_vars', [&$this, 'giml_insert_query_vars'] );
        add_action( 'wp_loaded', [&$this, 'giml_flush_rules'] );
        
        add_filter( 'the_content', [&$this, 'get_giml_search_data'] );
    }
    
    public static function get_giml_search_data () {
        $data = self::search(trim($_POST['giml-search']), get_permalink(get_post()->ID), ($_POST['show-pagination']==='true'), intval($_POST['items-per-page']));
        
        if (empty($data['groups']))
            return '<div><h2>No matching results found.</h2></div>';
        else {        
            wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
            wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
            wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
            wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
            wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
            wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/site/js/vendors/jquery.scrollTo.min.js');
            
            wp_enqueue_script('angular-ui-pagination', GIML_URI . 'includes/views/site/js/vendors/ui-bootstrap-pagination-0.12.1.min.js');
            wp_enqueue_script('angular-ui-pagination-tpl', GIML_URI . 'includes/views/site/js/vendors/ui-bootstrap-pagination-tpls-0.12.1.min.js');
                        
            wp_enqueue_script('giml-searchresult-app', GIML_URI . 'includes/views/site/js/searchresult-app.js');
            wp_enqueue_script('giml-ctrl-search-result', GIML_URI . 'includes/views/site/js/controllers/searchresult.js');

            wp_localize_script( 'giml-searchresult-app', 'gimlSearchResult',
                    ['URI' => GIML_URI,
                     'nonce' => GIML_NONCE,
                     'page_link' => get_permalink(get_post()->ID),
                     'ajax_url' => admin_url( 'admin-ajax.php' ),
                     'settings' => [
                         'player_color' => get_site_option('giml_player_color'),
                         'pagination' => [
                             'items_per_page' => intval($_POST['items-per-page']),
                             'show' => ($_POST['show-pagination']==='true'),
                             'max_size' => intval($_POST['max-size']),
                             'total_items' => intval($data['total_items']),
                             'searched_string' => trim($_POST['giml-search'])
                         ]
                     ],
                     'data' => $data,                     
                    ]);

            if (!empty($data['audioplaylist'])) {
                wp_enqueue_style( 'wp-mediaelement' );
                wp_enqueue_script( 'wp-mediaelement' );
                wp_enqueue_script('mediaelement-playlist', GIML_URI . 'includes/views/site/js/vendors/mep-feature-playlist.js');
                wp_enqueue_style('mediaelement-playlist', GIML_URI . 'includes/views/site/js/vendors/mep-feature-playlist.css');
            }

            wp_enqueue_style('giml-site', GIML_URI . 'includes/views/site/css/site.css');
            if (get_site_option('giml_disable_jqueryui_css')==='false') {
                wp_enqueue_style('giml-jquery-ui', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
            }
            if (get_site_option('giml_disable_bootstrap_css')==='false') {
                wp_enqueue_style('giml-site-bootstrap', GIML_URI . 'includes/views/site/css/bootstrap/css/bootstrap.css');
                wp_enqueue_style('giml-site-bootstrap-theme', GIML_URI . 'includes/views/site/css/bootstrap/css/bootstrap-theme.min.css');
            }
            wp_enqueue_script('giml-site-bootstrap', GIML_URI . 'includes/views/site/js/vendors/bootstrap.min.js');

            $tpl = file_get_contents( GIML_INCLUDES . "views/site/templates/search-result.html");
            return '<div class="row"><h2>Search results for <i>' . stripslashes($_POST['giml-search']) . '</i></h2></div><br class="hr-separator"/>' . $tpl;
        }
    }
}

?>
