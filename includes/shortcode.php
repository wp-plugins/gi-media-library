<?php
/**
 * 
 *
 * @author Zishan J.
 */

defined('ABSPATH') OR exit;

class GIML_Shortcode extends GIML_BASE {
    private static $default_options = [
        'id' => 0,
        'group_id' => NULL,
        'default' => 'false',
        'show_pagination' => 'true',
        'items_per_page' => 10
    ];
    
    function __construct() {
        
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
        } else {
        
            add_shortcode('gi_medialibrary', [&$this, 'giml_shortcode']);

        }
    }
    
    public static function set_html_content_type() {
        return 'text/html';
    }
    
    public static function giml_shortcode($atts, $content="") {
        global $post;
        $html = '';
        
        $atts = shortcode_atts(self::$default_options, $atts);
        
        if ($atts['default'] !== 'true' && $atts['default'] !== '1')
            return '';

        require_once(GIML_INCLUDES . 'models/group.php');
        require_once(GIML_INCLUDES . 'models/subgroup.php');
        require_once(GIML_INCLUDES . 'models/combo.php');
        require_once(GIML_INCLUDES . 'models/table.php');
        require_once(GIML_INCLUDES . 'models/section.php');
        require_once(GIML_INCLUDES . 'models/sectioncolumn.php');
        require_once(GIML_INCLUDES . 'models/column.php');
        
        $subgroupid = null;
        if (isset($_GET['giml-id']))
            $subgroupid = intval($_GET['giml-id']);
	else
            $subgroupid = intval($atts['id']);
        
        $item_id = null;
        if (isset($_GET['giml-item']))
            $item_id = intval($_GET['giml-item']);
        
        $subgroup = new GIML_Subgroup();
        $data = new stdClass();
        
        $subgrp = $subgroup->get(false, $subgroupid);
        
        if (!empty($subgrp)) {
            $subgrp = $subgrp[0];
            
            if (!empty($subgrp['subgroupdownloadlink'])) {
                $subgrp['downloadsize'] = self::get_filesize($subgrp['subgroupdownloadlink']);
                $subgrp['downloadimage'] = unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($subgrp['subgroupdownloadlink'],'.'),1)];
                $subgrp['subgroupdownloadlabel'] = (empty($subgrp['subgroupdownloadlabel'])?self::get_title($subgrp['subgroupdownloadlink']):$subgrp['subgroupdownloadlabel']);
                $subgrp['subgroupdownloadlink'] = add_query_arg(['download-file'=> rawurlencode($subgrp['subgroupdownloadlink']), GIML_NONCE_NAME=>wp_create_nonce('download_file_' . $subgrp['subgroupdownloadlink'])], get_permalink($post->ID));
            }
            
            $data->subgroup = $subgrp;
            
            $group = new GIML_Group();
            $grp = $group->get(false, ($atts['group_id'])?intval($atts['id']):$subgrp['groupid']);
            array_shift($grp); //remove None entry
            if(!empty($grp)) {
                $data->group = $grp[0];
            }
            
            $table = new GIML_Table();
            $tablecolumns = $table->get_columns($subgroupid, FALSE);
            $data->tablecolumns = $tablecolumns;
            $table = $table->get($subgroupid)[0];
            $data->table = $table;
            
            if(intval($subgrp['subgroupshowcombo']) == 1) {
                $combo = new GIML_Combo();
                $comboitems = $combo->get_items($subgroupid, false);
                array_shift($comboitems); //remove None entry
                if(!empty($comboitems)) {
                    $data->combo = $combo->get($subgroupid)[0];
                    
                    foreach ($comboitems as &$item) {
                        if (!empty($item['playlistcomboitemdownloadlink'])) {
                            $item['downloadsize'] = self::get_filesize($item['playlistcomboitemdownloadlink']);
                            $item['downloadimage'] = unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($item['playlistcomboitemdownloadlink'],'.'),1)];
                            $item['playlistcomboitemdownloadlabel'] = (empty($item['playlistcomboitemdownloadlabel'])?self::get_title($item['playlistcomboitemdownloadlink']):$item['playlistcomboitemdownloadlabel']);
                            $item['playlistcomboitemdownloadlink'] = add_query_arg(['download-file'=> rawurlencode($item['playlistcomboitemdownloadlink']), GIML_NONCE_NAME=>wp_create_nonce('download_file_' . $item['playlistcomboitemdownloadlink'])], get_permalink($post->ID));
                        }
            
                        if ((intval($item['playlistcomboitemdefault']) == 1 && !isset($data->combo['defaultitem']))
                                || ($item['id']==$item_id)) {
                            $data->combo['defaultitem'] = $item;
                        }
                    }
                    if (!isset($data->combo['defaultitem']))
                        $data->combo['defaultitem'] = $comboitems[0];
                    
                    $data->combo['items'] = $comboitems;
                    
                }
            }
            
                
            $combo = new GIML_Combo();
            $comboitems = $combo->get_items($subgroupid, false);
            $tmp = array_shift($comboitems); //remove None entry

            if (empty($comboitems))
                $comboitems[] = $tmp;
            
            $section = new GIML_Section();
            //$sections = $section->get($table['id'], (empty($comboitems)?0:($itemId)?$itemId:$comboitems[0]['id']), false);
            $sections = $section->get($table['id'], ($item_id)?$item_id:$comboitems[0]['id'], false);
            $tmp = self::giml_table_playlist($subgroupid, $sections, get_permalink($post->ID));
            $data->sections = $tmp['sections'];
            $data->audioplaylist = $tmp['audioplaylist'];
            
            if ($data->audioplaylist) {
                wp_enqueue_style( 'wp-mediaelement' );
                wp_enqueue_script( 'wp-mediaelement' );
                wp_enqueue_script('mediaelement-playlist', GIML_URI . 'includes/views/site/js/vendors/mep-feature-playlist.js');
                wp_enqueue_style('mediaelement-playlist', GIML_URI . 'includes/views/site/js/vendors/mep-feature-playlist.css');
            }
        }
        
        wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
        wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
        wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
        wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
        wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
        wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/site/js/vendors/jquery.scrollTo.min.js');
        
        wp_enqueue_script('giml-site-app', GIML_URI . 'includes/views/site/js/app.js');
        wp_enqueue_script('giml-site-ctrl-shortcode', GIML_URI . 'includes/views/site/js/controllers/shortcode.js');
        
        wp_localize_script( 'giml-site-app', 'gimlData',
                ['URI' => GIML_URI,
                 'nonce' => GIML_NONCE,
                 'page_link' => get_permalink($post->ID),
                 'query_item_url' => add_query_arg(['giml-id'=>$subgroupid, 'giml-item'=> ''], get_permalink($post->ID)),
                 'ajax_url' => admin_url( 'admin-ajax.php' ),
                 'query_subgroupid_url' => add_query_arg('giml-id', '', get_permalink($post->ID)),
                 'data' => $data,
                 'shortcode' => [
                    'show_pagination' => (strtolower($atts['show_pagination'])==='true'),
                    'items_per_page' => (intval($atts['items_per_page'])>0)?intval($atts['items_per_page']):self::$default_options['items_per_page'],
                 ],
                 'settings' => [
                     'player_color' => get_site_option('giml_player_color')
                 ]
                ]);
        
        wp_enqueue_style('giml-site', GIML_URI . 'includes/views/site/css/site.css');
        if (get_site_option('giml_disable_jqueryui_css')==='false') {
            wp_enqueue_style('giml-jquery-ui', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
        }
        if (get_site_option('giml_disable_bootstrap_css')==='false') {
            wp_enqueue_style('giml-site-bootstrap', GIML_URI . 'includes/views/site/css/bootstrap/css/bootstrap.css');
            wp_enqueue_style('giml-site-bootstrap-theme', GIML_URI . 'includes/views/site/css/bootstrap/css/bootstrap-theme.min.css');
        }
        wp_enqueue_script('giml-site-bootstrap', GIML_URI . 'includes/views/site/js/vendors/bootstrap.min.js');
        
        $tpl = file_get_contents( GIML_INCLUDES . "views/site/templates/main.html");
        
        return $tpl;
    }
}

?>