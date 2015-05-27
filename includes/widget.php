<?php
/**
 * 
 *
 * @author Zishan J.
 */
defined('ABSPATH') OR exit;

add_action('widgets_init', 'GIML_Widget::register_me');

class GIML_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                __CLASS__, // Base ID
                __('GI-Media Library', 'gimedialibrary'), // Name
                ['description' => __('Display the widget with media library selection on sidebar.', 'gimedialibrary')] // Args
        );
    }

    public function widget($args, $instance) {

        global $post;

        $currentsubgroup = null;

        if(isset($_GET['giml-id']))
            $currentsubgroup = $_GET['giml-id'];

        $pattern = "\[(\[?)(gi\_medialibrary)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)";
        //$pattern = get_shortcode_regex();     //doesn't works in visual composer when GIML shortcode is used as child element
        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'gi_medialibrary', $matches[2] ) ) {
            
            $groups = [];
            $current_group_id = null;
            
            require_once(GIML_INCLUDES . 'models/group.php');
            require_once(GIML_INCLUDES . 'models/subgroup.php');
        
            foreach (array_keys($matches[2], 'gi_medialibrary') as $key) {
            
                $subgroup = new GIML_Subgroup();
                $group = new GIML_Group();
                $has_default = false;
                $shortcode = $matches[0][$key];
            
                preg_match('/[ ]+id *= *["\']?([0-9]+)["\']+/i', $shortcode, $match);
                $id = $match[1];
                preg_match('/default *= *["\']?([true|false]+)["\']+/i', $shortcode, $match);
                $default = null;
                if (empty($match))
                    preg_match('/default *= *["\']?([0-1]+)["\']+/i', $shortcode, $match); //for backward compatibility
                if (!empty($match)) {
                    if ($match[1] === "true" || $match[1] === '1') {
                        $default = $id;
                        $has_default = true;
                    }
                }
                
                preg_match('/[ ]+group_id *= *["\']?([0-9]+)["\']+/i', $shortcode, $match);
                $group_id = ($match)?$match[1]:null;
                
                if (!empty($id)) {
                    $subgrp = $subgroup->get(false, $id, $group_id);
                    if (is_null($subgrp)) {
                        break;
                    }elseif(intval($subgrp[0]['groupid']) == 0) {
                        break;
                    }
                    
                    $tmp = $group->get(false, $subgrp[0]['groupid']);
                    array_shift($tmp); //remove None entry
                    if(!empty($tmp) && $tmp[0]['id']>0) {
                        $tmp = $tmp[0];
                    }
                    
                    //if (!is_null($default)) {
                    if (!empty($currentsubgroup)) {
                        $tmp['default'] = $currentsubgroup;
                    }elseif ($has_default) {
                        $tmp['default'] = $default;//($has_default)?$default:0;
                    }
                    $tmp['subgroups'] = $subgroup->get(false, null, $subgrp[0]['groupid']);
                    $groups[] = $tmp;
                    if ($has_default)
                        $current_group_id = count($groups) - 1;
                }
            }

            if (!empty($groups)) {
                echo $args['before_widget'];

                if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
                
                wp_enqueue_script('jquery-ui-accordion');
        
                wp_enqueue_script('jquery-ui-position');
                wp_enqueue_script('giml-jquery.menu', GIML_URI . 'js/jquery.ui.menu.min.js');
                
                wp_enqueue_script('angularjs', GIML_URI . 'js/angular.min.js');
                wp_enqueue_script('angularjs-sanitize', GIML_URI . 'js/angular-sanitize.min.js');
                wp_enqueue_script('angularjs-animate', GIML_URI . 'js/angular-animate.min.js');
                wp_enqueue_script('lodash', GIML_URI . 'js/lodash.min.js');
                wp_enqueue_script('underscore.string', GIML_URI . 'js/underscore.string.min.js');
                wp_enqueue_script('jquery-scrollto', GIML_URI . 'includes/views/site/js/vendors/jquery.scrollTo.min.js');

                wp_enqueue_script('giml-site-app', GIML_URI . 'includes/views/site/js/app.js');

                wp_enqueue_script('giml-site-app-ctrl-widget', GIML_URI . 'includes/views/site/js/controllers/widget.js');
                wp_enqueue_script('giml-site-app-comp-menu', GIML_URI . 'includes/views/site/js/components/menu.js');
                wp_enqueue_script('giml-site-app-comp-widget', GIML_URI . 'includes/views/site/js/components/widget.js');

                if (get_site_option('giml_disable_jqueryui_css')==='false') {
                    wp_enqueue_style('giml-jquery-ui', GIML_URI . 'css/smoothness/jquery-ui-1.10.4.custom.min.css');
                }
                
                if (!empty($currentsubgroup)) {
                    $current_group_id = $subgroup->get(false, $currentsubgroup);
                    $current_group_id = (empty($current_group_id)) ? $current_group_id : $current_group_id[0]['groupid'];
                }/* else {
                    $current_group_id = "";
                }*/

                wp_localize_script('giml-site-app', 'gimlWidgetData', [
                    'URI' => GIML_URI,
                    'groups' => $groups,
                    'current_group_id' => $current_group_id
                ]);
                
                $css = "";
                if (!empty($instance['css'])) {
                    $css = '<style type="text/css">' . $instance['css'] . "</style>";
                } else {
                    wp_enqueue_style('giml-widget', GIML_URI . 'includes/views/site/css/widget.css');
                }
                echo $css . '<div id="giml-widget" ng-controller="Widget as widget">
                        <gi-widget widget-id="giml-widget-accordion"></gi-widget>
                    </div>';

                echo $args['after_widget'];
            }
        }

        /* if (!empty($title)) {
          echo $args['before_title'] . $title . $args['after_title'];
          } */
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = "";
        }
        if (isset($instance['css'])) {
            $css = $instance['css'];
        } else {
            $css = file_get_contents(GIML_INCLUDES . "views/site/css/widget.css");
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('css'); ?>"><?php _e('CSS:'); ?></label> 
            <textarea class="widefat" id="<?php echo $this->get_field_id('css'); ?>" name="<?php echo $this->get_field_name('css'); ?>" cols="20" rows="16"><?php echo esc_textarea($css); ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['css'] = (!empty($new_instance['css']) ) ? strip_tags($new_instance['css']) : '';
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

    public static function register_me() {

        register_widget(__CLASS__);
    }
}
?>