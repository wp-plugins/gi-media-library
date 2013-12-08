<?php
/*
Plugin Name: GI-Media Library
Plugin URI: http://www.glareofislam.com/softwares/gimedialibrary.html
Description: An easy to use plugin to display your course/media library in tabular form. You can use shortcode to display any specific resource in detail on any page/post. Widget is also available to list the available group/resource of media which will be displayed on any sidebar you drag/drop on.
Version: 2.1.0
Author: Zishan Javaid
Author URI: http://www.glareofislam.com
License: GPL v2
*/

/*
Copyright (c) 2012-2013 Zishan Javaid

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

//add_action( 'wp', 'detect_shortcode' );
//add_action( 'the_content', 'check_content');





class GI_Media_Library {
	
	function __construct() {
		global $giml_db;
	
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );
		
		/* Internationalize the text strings used. */
		//add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );
		
		add_action( 'init', array( &$this, 'init' ) );
		
		//add_action( 'plugins_loaded', array( &$this, 'giml_update_db_check' ), 4 );

		register_activation_hook( __FILE__, array( &$this, 'giml_install' ) );

	}
	
	function constants() {
            define( 'GIML_DB_VERSION', '1.0' );

            /* Set constant path to the giml plugin directory. */
            define( 'GIML_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

            /* Set constant path to the giml plugin URL. */
            define( 'GIML_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

            /* Set the constant path to the giml includes directory. */
            define( 'GIML_INCLUDES', GIML_DIR. trailingslashit( 'includes' ) );

            define( 'GIML_BASENAME', plugin_basename(__FILE__) );
	}
	
	function includes() {
		require_once( GIML_INCLUDES . 'gi-medialibrary-db.php' );
		require_once( GIML_INCLUDES . 'widget.php' );
		
	}
	
	function init() {
		global $giml_db;
		
		$giml_db = new gi_medialibrary_db();

		//if inside admin
		if ( is_admin() )
		{
                    require_once( GIML_INCLUDES . 'admin-settings.php' );

		}
		
		require_once( GIML_INCLUDES . 'admin-functions.php' );
		require_once( GIML_INCLUDES . 'shortcode.php' );
		
	}

	function giml_install() {
	   global $wpdb;
	   
	   $table_prefix = $wpdb->prefix . "giml_";
	   $installed_ver = get_site_option( "giml_db_version" );

	   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	   
            if (!$installed_ver)
            {
		$sql = "CREATE TABLE `{$table_prefix}group` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `grouplabel` tinytext,
		  `grouprightlabel` tinytext,
		  `groupleftlabel` tinytext,
		  `groupcss` varchar(45) DEFAULT NULL,
		  `groupdirection` varchar(3) DEFAULT 'ltr',
		  `createddate` datetime NOT NULL,
		  `updateddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );

		$sql = "CREATE TABLE `{$table_prefix}playlistcolumn` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `rowid` int(11) NOT NULL,
		  `playlistsortorder` int(11) NOT NULL DEFAULT '10',
		  `playlistcolumnsectionid` int(11) DEFAULT NULL,
		  `playlisttablecolumnid` int(11) DEFAULT NULL,
		  `playlistcolumntext` longtext,
		  `playlistcolumncss` varchar(45) DEFAULT NULL,
		  `playlistcolumndirection` varchar(3) DEFAULT NULL,
		  `createddate` datetime DEFAULT NULL,
		  `updateddate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `playlistcolumntablesectioncolumnid` (`playlisttablecolumnid`),
		  KEY `playlistcolumnsectionid` (`playlistcolumnsectionid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}playlistcombo` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `subgroupid` int(11) NOT NULL,
		  `playlistcombolabel` tinytext,
		  `playlistcombodirection` varchar(3) DEFAULT 'ltr',
		  `playlistcombocss` varchar(45) DEFAULT NULL,
		  `updateddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `SUBGROUP_COMBO_ID` (`subgroupid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}playlistcomboitem` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `playlistcomboid` int(11) DEFAULT NULL,
		  `playlistcomboitemlabel` tinytext,
		  `playlistcomboitemsortorder` int(11) DEFAULT '10',
		  `playlistcomboitemdownloadlink` tinytext,
		  `playlistcomboitemdownloadlabel` tinytext,
		  `playlistcomboitemdownloadcss` varchar(45) DEFAULT NULL,
		  `playlistcomboitemdescription` text,
		  `playlistcomboitemdefault` tinyint(1) DEFAULT '0',
		  `createddate` datetime NOT NULL,
		  `updateddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `GROUP_GRPCOMBO_ID` (`playlistcomboid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}playlistsection` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `playlisttableid` int(11) DEFAULT NULL,
		  `playlistsectioncomboitemid` int(11) DEFAULT NULL,
		  `playlistsectionlabel` longtext,
		  `playlistsectioncss` varchar(45) DEFAULT NULL,
		  `playlistsectionsortorder` int(11) DEFAULT '10',
		  `playlistsectiondownloadlink` tinytext,
		  `playlistsectiondownloadlabel` tinytext,
		  `playlistsectiondownloadcss` varchar(45) DEFAULT NULL,
		  `playlistsectiondirection` varchar(3) DEFAULT 'ltr',
		  `playlistsectionhide` tinyint(1) DEFAULT '0',
		  `createddate` datetime DEFAULT NULL,
		  `updateddate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `tablesectionid` (`playlisttableid`),
		  KEY `comboitemsectionid` (`playlistsectioncomboitemid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}playlistsectioncolumn` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `playlistsectionid` int(11) DEFAULT NULL,
		  `playlisttablecolumnid` int(11) DEFAULT NULL,
		  `playlistsectiontablecolumntext` longtext,
		  `playlistsectiontablecolumncss` varchar(45) DEFAULT NULL,
		  `playlistsectiontablecolumndirection` varchar(3) DEFAULT 'ltr',
		  `createddate` datetime DEFAULT NULL,
		  `updateddate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `playlisttablesectioncolumnid` (`playlisttablecolumnid`),
		  KEY `playlistsectionid` (`playlistsectionid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}playlisttable` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `subgroupid` int(11) NOT NULL,
		  `playlisttablecss` tinytext,
		  `updateddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `playlisttablesubgroup` (`subgroupid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}playlisttablecolumn` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `playlisttableid` int(11) NOT NULL,
		  `playlisttablecolumnlabel` tinytext NOT NULL,
		  `playlisttablecolumncss` tinytext,
		  `playlisttablecolumndirection` varchar(3) DEFAULT 'ltr',
		  `playlisttablecolumnsortorder` int(11) DEFAULT '10',
		  `playlisttablecolumntype` varchar(45) DEFAULT 'text',
		  `createddate` datetime DEFAULT NULL,
		  `updateddate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `playlisttablecolumnid` (`playlisttableid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "CREATE TABLE `{$table_prefix}subgroup` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `groupid` int(11) DEFAULT NULL,
		  `subgrouplabel` tinytext NOT NULL,
		  `subgrouprightlabel` tinytext,
		  `subgroupleftlabel` tinytext,
		  `subgroupcss` varchar(45) DEFAULT NULL,
		  `subgroupdescription` text,
		  `subgroupdirection` varchar(3) DEFAULT 'ltr',
		  `subgroupsortorder` int(11) DEFAULT '10',
		  `createddate` datetime NOT NULL,
		  `updateddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  `subgroupdownloadlink` tinytext,
		  `subgroupdownloadlabel` tinytext,
		  `subgroupdownloadcss` varchar(45) DEFAULT NULL,
		  `subgroupshowfilter` tinyint(1) DEFAULT '1',
		  `subgroupshowcombo` tinyint(1) DEFAULT '1',
		  PRIMARY KEY (`id`),
		  KEY `GROUP_SUBGROUP_ID` (`groupid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlistcolumn`
		  ADD CONSTRAINT `{$table_prefix}playlistcolumn_ibfk_1` FOREIGN KEY (`playlistcolumnsectionid`) REFERENCES `{$table_prefix}playlistsection` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  ADD CONSTRAINT `{$table_prefix}playlistcolumn_ibfk_2` FOREIGN KEY (`playlisttablecolumnid`) REFERENCES `{$table_prefix}playlisttablecolumn` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlistcombo`
		  ADD CONSTRAINT `SUBGROUP_COMBO_ID` FOREIGN KEY (`subgroupid`) REFERENCES `{$table_prefix}subgroup` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlistcomboitem`
		  ADD CONSTRAINT `COMBO_GRPCOMBO_ID` FOREIGN KEY (`playlistcomboid`) REFERENCES `{$table_prefix}playlistcombo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlistsection`
		  ADD CONSTRAINT `comboitemsectionid` FOREIGN KEY (`playlistsectioncomboitemid`) REFERENCES `{$table_prefix}playlistcomboitem` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  ADD CONSTRAINT `tablesectionid` FOREIGN KEY (`playlisttableid`) REFERENCES `{$table_prefix}playlisttable` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlistsectioncolumn`
		  ADD CONSTRAINT `playlistsectionid` FOREIGN KEY (`playlistsectionid`) REFERENCES `{$table_prefix}playlistsection` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  ADD CONSTRAINT `playlisttablesectioncolumnid` FOREIGN KEY (`playlisttablecolumnid`) REFERENCES `{$table_prefix}playlisttablecolumn` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlisttable`
		  ADD CONSTRAINT `playlisttablesubgroup` FOREIGN KEY (`subgroupid`) REFERENCES `{$table_prefix}subgroup` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}playlisttablecolumn`
		  ADD CONSTRAINT `playlisttablecolumnid` FOREIGN KEY (`playlisttableid`) REFERENCES `{$table_prefix}playlisttable` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );


		$sql = "ALTER TABLE `{$table_prefix}subgroup`
		  ADD CONSTRAINT `GROUP_SUBGROUP_ID` FOREIGN KEY (`groupid`) REFERENCES `{$table_prefix}group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

		   dbDelta( $sql );

                global $wpdb;
                $sql = "SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'";
                $wpdb->query($sql);

                $sql = "INSERT INTO `{$table_prefix}group` (`id`, `createddate`) VALUES (0, NOW())";
                dbDelta($sql);
			
                update_site_option( "giml_db_version", '1.0' );
            }
}

	function giml_update_db_check() {
		
		if (floatval(get_site_option( 'giml_db_version' )) != floatval(GIML_DB_VERSION)) {
			$this->giml_install();
		}
	}

}
/*
function detect_shortcode () {
	global $post;
	$pattern = get_shortcode_regex();

	if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
        && array_key_exists( 2, $matches )
        && in_array( 'gi-medialibrary', $matches[2] ) )
    {
        print "shortcode used";
    }else{
		print "no shortcode";
	}
    
}
function check_content($content) {
	global $post;
	
	return $content;
	
}
*/

$giml = new GI_Media_Library();

?>