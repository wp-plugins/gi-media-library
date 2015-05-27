<?php
defined('ABSPATH') OR exit;
/*
  Plugin Name: GI-Media Library
  Plugin URI: http://www.glareofislam.com/softwares/gimedialibrary.html
  Description: An easy to use plugin to display your course/media library in tabular form. You can use shortcode to display any specific resource in detail on any page/post. Widget is also available to list the available group/resource of media which will be displayed on any sidebar you drag/drop on.
  Version: 3.0.1
  Author: Zishan Javaid
  Author URI: http://www.glareofislam.com
  License: GPL v2
 */

/*
  Copyright (c) 2012-2015 Zishan Javaid

  Permission is hereby granted under GPL v2, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */

class GI_Media_Library {
    
    const GIML_DB_VERSION = 2.0;
    
    function __construct() {
        add_action('plugins_loaded', array(&$this, 'constants'), 1);

        /* Internationalize the text strings used. */
        //add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

        /* Load the functions files. */
        add_action('plugins_loaded', array(&$this, 'includes'), 3);

        add_action('init', array(&$this, 'init'));

        add_action('plugins_loaded', array(&$this, 'giml_update_db_check'), 4);

        register_activation_hook(__FILE__, array(&$this, 'giml_install'));
        
        register_uninstall_hook(__FILE__, array('GI_Media_Library', 'giml_uninstall'));
    }

    public static function constants() {
        global $wpdb;

        /* Set constant path to the giml plugin directory. */
        define('GIML_DIR', trailingslashit(plugin_dir_path(__FILE__)));

        /* Set constant path to the giml plugin URL. */
        define('GIML_URI', trailingslashit(plugin_dir_url(__FILE__)));

        /* Set the constant path to the giml includes directory. */
        define('GIML_INCLUDES', GIML_DIR . trailingslashit('includes'));

        define('GIML_BASENAME', plugin_basename(__FILE__));
        define('GIML_PLUGIN_ABSPATH', __FILE__);

        define('GIML_NONCE_NAME', 'gi-medialibrary');
        define('GIML_NONCE', wp_create_nonce(GIML_NONCE_NAME));
        define('GIML_URL', get_site_option('siteurl'));
        
        define('GIML_MEDIA_FORMATS', serialize([
            'mp3' => 'mp3-icon.gif',
            'pdf' => 'pdf-icon.gif',
            'ppt' => 'ppt-icon.jpg',
            'pps' => 'ppt-icon.jpg',
            'html' => 'txt-icon.gif',
            'htm' => 'txt-icon.gif',
            'txt' => 'txt-icon.gif',
            'flv' => 'video-icon.gif',
            'mp4' => 'mp4-icon.gif',
            'avi' => 'avi-icon.gif',
            'wmp' => 'wmp-icon.gif',
            'audio' => 'audio-icon.png',
            'download' => 'download-icon.png',
            'zip' => 'zip-icon.png',
            'video' => 'video-icon.gif',
            'youtube' => 'youtube-icon.png',
            'vimeo' => 'vimeo-icon.png'
        ]));
    }

    public static function includes() {
        require_once( GIML_INCLUDES . 'base/base.php' );
        require_once( GIML_INCLUDES . 'widget.php');
    }

    public static function init() {

        if (is_admin()) {
            require_once( GIML_INCLUDES . 'admin.php' );
            new GIML_Admin();
        } else {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
            require_once( GIML_INCLUDES . 'shortcode.php' );
            new GIML_Shortcode();
            require_once( GIML_INCLUDES . 'search-shortcode.php' );
            new GIML_Search_Shortcode();
        }
    }

    public static function giml_install() {

        $installed_ver = get_site_option("giml_db_version");

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        if (!$installed_ver) {
            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "group (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  grouplabel tinytext,
		  grouprightlabel tinytext,
		  groupleftlabel tinytext,
		  groupcss varchar(45) DEFAULT NULL,
		  groupdirection varchar(3) DEFAULT 'ltr',
		  createddate datetime NOT NULL,
		  updateddate timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            dbDelta($sql);

            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlistcolumn (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  rowid int(11) NOT NULL,
		  playlistsortorder int(11) NOT NULL DEFAULT '10',
		  playlistcolumnsectionid int(11) DEFAULT NULL,
		  playlisttablecolumnid int(11) DEFAULT NULL,
		  playlistcolumntext longtext,
		  playlistcolumncss varchar(45) DEFAULT NULL,
		  playlistcolumndirection varchar(3) DEFAULT NULL,
		  createddate datetime DEFAULT NULL,
		  updateddate timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY playlistcolumntablesectioncolumnid (playlisttablecolumnid),
		  KEY playlistcolumnsectionid (playlistcolumnsectionid)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlistcombo (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  subgroupid int(11) NOT NULL,
		  playlistcombolabel tinytext,
		  playlistcombodirection varchar(3) DEFAULT 'ltr',
		  playlistcombocss varchar(45) DEFAULT NULL,
		  updateddate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY SUBGROUP_COMBO_ID (subgroupid)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlistcomboitem (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  playlistcomboid int(11) DEFAULT NULL,
		  playlistcomboitemlabel tinytext,
		  playlistcomboitemsortorder int(11) DEFAULT '10',
		  playlistcomboitemdownloadlink tinytext,
		  playlistcomboitemdownloadlabel tinytext,
		  playlistcomboitemdownloadcss varchar(45) DEFAULT NULL,
		  playlistcomboitemdescription text,
		  playlistcomboitemdefault tinyint(1) DEFAULT '0',
		  createddate datetime NOT NULL,
		  updateddate timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY GROUP_GRPCOMBO_ID (playlistcomboid)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlistsection (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  playlisttableid int(11) DEFAULT NULL,
		  playlistsectioncomboitemid int(11) DEFAULT NULL,
		  playlistsectionlabel longtext,
		  playlistsectioncss varchar(45) DEFAULT NULL,
		  playlistsectionsortorder int(11) DEFAULT '10',
		  playlistsectiondownloadlink tinytext,
		  playlistsectiondownloadlabel tinytext,
		  playlistsectiondownloadcss varchar(45) DEFAULT NULL,
		  playlistsectiondirection varchar(3) DEFAULT 'ltr',
		  playlistsectionhide tinyint(1) DEFAULT '0',
		  createddate datetime DEFAULT NULL,
		  updateddate timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY tablesectionid (playlisttableid),
		  KEY comboitemsectionid (playlistsectioncomboitemid)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlistsectioncolumn (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  playlistsectionid int(11) DEFAULT NULL,
		  playlisttablecolumnid int(11) DEFAULT NULL,
		  playlistsectiontablecolumntext longtext,
		  playlistsectiontablecolumncss varchar(45) DEFAULT NULL,
		  playlistsectiontablecolumndirection varchar(3) DEFAULT 'ltr',
		  createddate datetime DEFAULT NULL,
		  updateddate timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY playlisttablesectioncolumnid (playlisttablecolumnid),
		  KEY playlistsectionid (playlistsectionid)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlisttable (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  subgroupid int(11) NOT NULL,
		  playlisttablecss tinytext,
		  updateddate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY playlisttablesubgroup (subgroupid)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "playlisttablecolumn (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  playlisttableid int(11) NOT NULL,
		  playlisttablecolumnlabel tinytext NOT NULL,
		  playlisttablecolumncss tinytext,
		  playlisttablecolumndirection varchar(3) DEFAULT 'ltr',
		  playlisttablecolumnsortorder int(11) DEFAULT '10',
		  playlisttablecolumntype varchar(45) DEFAULT 'text',
		  createddate datetime DEFAULT NULL,
		  updateddate timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY playlisttablecolumnid (playlisttableid)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);


            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "subgroup (
		  id int(11) NOT NULL AUTO_INCREMENT,
                  subgrouplabel tinytext NOT NULL,
		  subgrouprightlabel tinytext,
		  subgroupleftlabel tinytext,
		  subgroupcss varchar(45) DEFAULT NULL,
		  subgroupdescription text,
		  subgroupdirection varchar(3) DEFAULT 'ltr',
		  subgroupsortorder int(11) DEFAULT '10',
		  createddate datetime NOT NULL,
		  updateddate timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  subgroupdownloadlink tinytext,
		  subgroupdownloadlabel tinytext,
		  subgroupdownloadcss varchar(45) DEFAULT NULL,
		  subgroupshowfilter tinyint(1) DEFAULT '1',
		  subgroupshowcombo tinyint(1) DEFAULT '1',
		  PRIMARY KEY  (id)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta($sql);
            
            $sql = "CREATE TABLE " . GIML_TABLE_PREFIX . "groupsubgroup (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    groupid int(11) NOT NULL,
                    subgroupid int(11) NOT NULL,
                    PRIMARY KEY  (id),
                    KEY group_subgroupid_idx (groupid),
                    KEY subgroup_subgroupid_idx (subgroupid)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            dbDelta($sql);

            global $wpdb;
            $wpdb->hide_errors();
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . 'groupsubgroup 
                    ADD CONSTRAINT GROUP_GROUPID FOREIGN KEY (groupid) REFERENCES ' . GIML_TABLE_PREFIX . 'group (id) ON DELETE CASCADE ON UPDATE NO ACTION,
                    ADD CONSTRAINT SUBGROUP_SUBGROUPID FOREIGN KEY (subgroupid) REFERENCES ' . GIML_TABLE_PREFIX . 'subgroup (id) ON DELETE CASCADE ON UPDATE NO ACTION;';
            $wpdb->query($sql);
                
            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlistcolumn
		  ADD CONSTRAINT " . GIML_TABLE_PREFIX . "playlistcolumn_ibfk_1 FOREIGN KEY (playlistcolumnsectionid) REFERENCES " . GIML_TABLE_PREFIX . "playlistsection (id) ON DELETE CASCADE ON UPDATE NO ACTION,
		  ADD CONSTRAINT " . GIML_TABLE_PREFIX . "playlistcolumn_ibfk_2 FOREIGN KEY (playlisttablecolumnid) REFERENCES " . GIML_TABLE_PREFIX . "playlisttablecolumn (id) ON DELETE CASCADE ON UPDATE NO ACTION;";
            $wpdb->query($sql);

            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlistcombo
		  ADD CONSTRAINT SUBGROUP_COMBO_ID FOREIGN KEY (subgroupid) REFERENCES " . GIML_TABLE_PREFIX . "subgroup (id) ON DELETE CASCADE ON UPDATE NO ACTION;";
            $wpdb->query($sql);

            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlistcomboitem
		  ADD CONSTRAINT COMBO_GRPCOMBO_ID FOREIGN KEY (playlistcomboid) REFERENCES " . GIML_TABLE_PREFIX . "playlistcombo (id) ON DELETE CASCADE ON UPDATE NO ACTION;";

            $wpdb->query($sql);

            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlistsection
		  ADD CONSTRAINT comboitemsectionid FOREIGN KEY (playlistsectioncomboitemid) REFERENCES " . GIML_TABLE_PREFIX . "playlistcomboitem (id) ON DELETE CASCADE ON UPDATE NO ACTION,
		  ADD CONSTRAINT tablesectionid FOREIGN KEY (playlisttableid) REFERENCES " . GIML_TABLE_PREFIX . "playlisttable (id) ON DELETE CASCADE ON UPDATE NO ACTION;";

            $wpdb->query($sql);

            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlistsectioncolumn
		  ADD CONSTRAINT playlistsectionid FOREIGN KEY (playlistsectionid) REFERENCES " . GIML_TABLE_PREFIX . "playlistsection (id) ON DELETE CASCADE ON UPDATE NO ACTION,
		  ADD CONSTRAINT playlisttablesectioncolumnid FOREIGN KEY (playlisttablecolumnid) REFERENCES " . GIML_TABLE_PREFIX . "playlisttablecolumn (id) ON DELETE CASCADE ON UPDATE NO ACTION;";

            $wpdb->query($sql);

            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlisttable
		  ADD CONSTRAINT playlisttablesubgroup FOREIGN KEY (subgroupid) REFERENCES " . GIML_TABLE_PREFIX . "subgroup (id) ON DELETE CASCADE ON UPDATE NO ACTION;";

            $wpdb->query($sql);

            
            $sql = "ALTER TABLE " . GIML_TABLE_PREFIX . "playlisttablecolumn
		  ADD CONSTRAINT playlisttablecolumnid FOREIGN KEY (playlisttableid) REFERENCES " . GIML_TABLE_PREFIX . "playlisttable (id) ON DELETE CASCADE ON UPDATE NO ACTION;";

            $wpdb->query($sql);


            
            $sql = "SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'";
            $wpdb->query($sql);

            $sql = "INSERT INTO " . GIML_TABLE_PREFIX . "group (id, createddate) VALUES (0, NOW())";
            $wpdb->query($sql);
            $sql = "INSERT INTO " . GIML_TABLE_PREFIX . "playlistcomboitem (id, createddate) VALUES (0, NOW())";
            $wpdb->query($sql);

            $wpdb->show_errors();
            
            update_site_option("giml_db_version", self::GIML_DB_VERSION);
            update_site_option('giml_disable_jqueryui_css', 'false');
            update_site_option('giml_disable_bootstrap_css', 'false');
            update_site_option('giml_search_bar_caption', 'Search in GI-Media Library...');
            update_site_option('giml_search_page_title', 'GI-Media Library Search Result');
            update_site_option('giml_player_color', '#222');
        }
    }

    public static function giml_uninstall() {
        global $wpdb;
        
        $wpdb->query('SET foreign_key_checks=0;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'group;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'groupsubgroup;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlistcolumn;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlistcombo;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlistcomboitem;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlistsection;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlistsectioncolumn;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlisttable;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'playlisttablecolumn;');
        $wpdb->query('DROP TABLE IF EXISTS ' . GIML_TABLE_PREFIX . 'subgroup;');
        $wpdb->query('SET foreign_key_checks=1;');
        
        delete_site_option('giml_db_version');
        delete_site_option('giml_disable_jqueryui_css');
        delete_site_option('giml_disable_bootstrap_css');
        delete_site_option('giml_search_bar_caption');
        delete_site_option('giml_search_page_title');
        delete_site_option('giml_player_color');
        
        $postId = get_site_option('giml_search_page');
        if ($postId) {
            wp_delete_post($postId, true);
            delete_site_option('giml_search_page');
        }
        
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
    
    public static function giml_update_db_check() {
        global $wpdb;
        if ((get_site_option('giml_db_version'))) {
            if (floatval(get_site_option('giml_db_version')) < floatval(2)) {
                $sql = 'CREATE TABLE ' . GIML_TABLE_PREFIX . 'groupsubgroup (
                        id int(11) NOT NULL AUTO_INCREMENT,
                        groupid int(11) NOT NULL,
                        subgroupid int(11) NOT NULL,
                        PRIMARY KEY  (id),
                        KEY GROUP_GROUPID_idx (groupid),
                        KEY SUBGROUP_SUBGROUPID_idx (subgroupid),
                        CONSTRAINT GROUP_GROUPID FOREIGN KEY (groupid) REFERENCES ' . GIML_TABLE_PREFIX . 'group (id) ON DELETE CASCADE ON UPDATE NO ACTION,
                        CONSTRAINT SUBGROUP_SUBGROUPID FOREIGN KEY (subgroupid) REFERENCES ' . GIML_TABLE_PREFIX . 'subgroup (id) ON DELETE CASCADE ON UPDATE NO ACTION
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
                $wpdb->query($sql);
                
                $sql = 'INSERT INTO ' . GIML_TABLE_PREFIX . 'groupsubgroup(groupid, subgroupid) select grp.id as groupid, subgrp.id as subgroupid FROM ' . GIML_TABLE_PREFIX . 'group as grp, ' . GIML_TABLE_PREFIX . 'subgroup as subgrp WHERE subgrp.groupid = grp.id';
                $wpdb->query($sql);
                
                /*$sql = 'ALTER TABLE ' . GIML_TABLE_PREFIX . 'subgroup 
                        DROP FOREIGN KEY GROUP_SUBGROUP_ID;';
                $wpdb->query($sql);*/
                $sql = 'ALTER TABLE ' . GIML_TABLE_PREFIX . 'subgroup 
                        DROP COLUMN groupid;';/*,
                        DROP INDEX GROUP_SUBGROUP_ID;';*/
                $wpdb->query($sql);
                
                update_site_option("giml_db_version", self::GIML_DB_VERSION);
            
                update_site_option('giml_search_bar_caption', 'Search in GI-Media Library&hellip;');
                update_site_option('giml_search_page_title', 'GI-Media Library Search Result');
                update_site_option('giml_player_color', '#222');
            }
        }
    }

}

global $wpdb;

define('GIML_TABLE_PREFIX', $wpdb->prefix . "giml_");

new GI_Media_Library();
?>