<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_Section
 *
 * @author Zishan J.
 */
class GIML_Section {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }

    function get($tableid, $comboitemid=null, $sortbyname = true, $sectionId=null) {
        if (empty($tableid))// || is_null($comboitemid))
            return null;
        
        global $wpdb;
        
        $where = '';
        if (!is_null($comboitemid))
            $where = 'AND playlistsectioncomboitemid='.$comboitemid;
        
        if (!is_null($sectionId)) {
            $where = (!is_array($sectionId)?("id=".intval($sectionId)):("id IN (" . join(",", $sectionId) . ")"));
            $where = 'AND ' . $where;
        }
        
        $sortby = (!$sortbyname) ? "playlistsectionsortorder ASC" : "playlistsectionlabel ASC";
        $result = $wpdb->get_results("SELECT * " . 
                "FROM " . GIML_TABLE_PREFIX . "playlistsection WHERE playlisttableid=".$tableid." " . $where .
                " ORDER BY ".$sortby);
        
        $data = null;
        foreach ($result as $section) {
            $data[] = [
                'id' => intval($section->id),
                'playlistsectionlabel' => stripslashes($section->playlistsectionlabel),
                'playlistsectioncss' => $section->playlistsectioncss,
                'playlistsectionsortorder' => intval($section->playlistsectionsortorder),
                'playlistsectiondownloadlink' => stripslashes($section->playlistsectiondownloadlink),
                'playlistsectiondownloadlabel' => stripslashes($section->playlistsectiondownloadlabel),
                'playlistsectiondownloadcss' => $section->playlistsectiondownloadcss,
                'playlistsectiondirection' => $section->playlistsectiondirection,
                'playlistsectionhide' => intval($section->playlistsectionhide)
            ];
        }
        
        return $data;
    }
    
    function get_section($sectionId, $sortbyname = true) {
        global $wpdb;
        
        $where = (!is_array($sectionId)?("id=".intval($sectionId)):("id IN (" . join(",", $sectionId) . ")"));
        $where = 'WHERE ' . $where;
            
        $sortby = (!$sortbyname) ? "playlistsectionsortorder ASC" : "playlistsectionlabel ASC";
        
        $result = $wpdb->get_results("SELECT * FROM " . GIML_TABLE_PREFIX . "playlistsection " . $where . ' ORDER BY ' . $sortby);
        $data = null;
        foreach ($result as $section) {
            $data[] = [
                'id' => intval($section->id),
                'playlisttableid' => intval($section->playlisttableid),
                'playlistsectionlabel' => stripslashes($section->playlistsectionlabel),
                'playlistsectioncss' => $section->playlistsectioncss,
                'playlistsectionsortorder' => intval($section->playlistsectionsortorder),
                'playlistsectiondownloadlink' => stripslashes($section->playlistsectiondownloadlink),
                'playlistsectiondownloadlabel' => stripslashes($section->playlistsectiondownloadlabel),
                'playlistsectiondownloadcss' => $section->playlistsectiondownloadcss,
                'playlistsectiondirection' => $section->playlistsectiondirection,
                'playlistsectionhide' => intval($section->playlistsectionhide)
            ];
        }
        
        return $data;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $data = [
                'playlistsectioncomboitemid'=>intval($row['playlistsectioncomboitem']),
                'playlistsectionlabel' => wp_kses_post(trim(stripslashes($row['playlistsectionlabel']))),
                'playlistsectioncss' => sanitize_text_field(trim(stripslashes($row['playlistsectioncss']))),
                'playlistsectionsortorder' => intval($row['playlistsectionsortorder']),
                'playlistsectiondownloadlink' => esc_url_raw(trim(stripslashes($row['playlistsectiondownloadlink']))),
                'playlistsectiondownloadlabel' => sanitize_text_field(trim(stripslashes($row['playlistsectiondownloadlabel']))),
                'playlistsectiondownloadcss' => sanitize_text_field(trim(stripslashes($row['playlistsectiondownloadcss']))),
                'playlistsectiondirection' => sanitize_text_field(trim($row['playlistsectiondirection'])),
                'playlistsectionhide' => intval($row['playlistsectionhide'])
            ];
            $sectionid = intval($row['id']);
            $result = $wpdb->update(GIML_TABLE_PREFIX . 'playlistsection', $data, array('id' => $sectionid), null, array('%d'));
            if (!$result && !empty($wpdb->last_error))
                $error[] = $wpdb->last_error;
        }
        
        if ($error)
            return new WP_Error('sectionupdate-error', __('An error occurred while updating section: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function add($rows, $returnID = false) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (trim($row['playlistsectionlabel']) !== "") {
                $data = [
                    'playlisttableid'=>intval($row['playlisttableid']),
                    'playlistsectioncomboitemid'=>intval($row['playlistsectioncomboitem']),
                    'playlistsectionlabel' => wp_kses_post(trim(stripslashes($row['playlistsectionlabel']))),
                    'playlistsectioncss' => sanitize_text_field(trim(stripslashes($row['playlistsectioncss']))),
                    'playlistsectionsortorder' => intval($row['playlistsectionsortorder']),
                    'playlistsectiondownloadlink' => esc_url_raw(trim(stripslashes($row['playlistsectiondownloadlink']))),
                    'playlistsectiondownloadlabel' => sanitize_text_field(trim(stripslashes($row['playlistsectiondownloadlabel']))),
                    'playlistsectiondownloadcss' => sanitize_text_field(trim(stripslashes($row['playlistsectiondownloadcss']))),
                    'playlistsectiondirection' => sanitize_text_field(trim(stripslashes($row['playlistsectiondirection']))),
                    'playlistsectionhide' => intval($row['playlistsectionhide']),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistsection', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('sectionadd-error', __('An error occurred while adding section(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return ($returnID)?$wpdb->insert_id:true;
    }
    
    function delete($ids) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistsection WHERE id IN ({$ids})");
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('sectiondelete-error', __('An error occurred while deleting section(s): ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
    
    function delete_combo_item($comboItemID) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistsection WHERE playlistsectioncomboitemid = $comboItemID");
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('sectiondelete-error', __('An error occurred while deleting section: ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
}