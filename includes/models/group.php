<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_Group
 *
 * @author Zishan J.
 */
class GIML_Group {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }

    function get($sortbyname=true, $id=NULL, $subgroupId = NULL) {
        global $wpdb;
        
        $sortby = (!$sortbyname) ? "createddate DESC" : "grouplabel ASC";
        $where = "WHERE id>0";
        $select = 'SELECT grp.*';
        if (!is_null($id)) {
            $where = (!is_array($id)?("id=".$id):("id IN (" . join(",", $id) . ")"));
            $where = 'WHERE ' . $where;
        }elseif (!is_null($subgroupId)) {
            $select = 'SELECT DISTINCT grp.id, grp.*';
            $where = (!is_array($subgroupId)?("subgroupid=".$id):("subgroupid IN (" . join(",", $subgroupId) . ")"));
            $from = ', ' . GIML_TABLE_PREFIX . 'groupsubgroup ';
            $where = $from . 'WHERE grp.id=groupid AND ' . $where;
        }
        
        $result = $wpdb->get_results($select . " FROM " . GIML_TABLE_PREFIX . "group AS grp " . $where . " ORDER BY " . $sortby);
        
        $data[] = ['id'=>0, 'grouplabel'=>'None'];
        
        foreach ($result as $group) {
            $data[] = [
                'id'=>intval($group->id), 
                'grouplabel'=>stripslashes($group->grouplabel),
                'grouprightlabel'=>stripslashes($group->grouprightlabel),
                'groupleftlabel'=>stripslashes($group->groupleftlabel),
                'groupcss'=>$group->groupcss,
                'groupdirection'=>$group->groupdirection];            
        }
        
        return $data;
    }

    function add($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (trim($row['grouplabel']) !== "") {
                $data = [
                    'grouplabel' => wp_kses_post(trim(stripslashes($row['grouplabel']))),
                    'grouprightlabel' => sanitize_text_field(trim(stripslashes($row['grouprightlabel']))),
                    'groupleftlabel' => sanitize_text_field(trim(stripslashes($row['groupleftlabel']))),
                    'groupcss' => sanitize_text_field(trim(stripslashes($row['groupcss']))),
                    'groupdirection' => sanitize_text_field(trim(stripslashes($row['groupdirection']))),
                    'createddate' => date('Y-m-d H:i:s')
                ];

                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'group', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('groupinsert-error', __('An error occurred while creating new group(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (trim($row['grouplabel']) !== "") {
                $data = [
                    'grouplabel' => sanitize_text_field(trim(stripslashes($row['grouplabel']))),
                    'grouprightlabel' => sanitize_text_field(trim(stripslashes($row['grouprightlabel']))),
                    'groupleftlabel' => sanitize_text_field(trim(stripslashes($row['groupleftlabel']))),
                    'groupcss' => sanitize_text_field(trim(stripslashes($row['groupcss']))),
                    'groupdirection' => sanitize_text_field(trim(stripslashes($row['groupdirection'])))
                ];
                $groupid = intval($row['id']);
                $result = $wpdb->update(GIML_TABLE_PREFIX . 'group', $data, array('id' => $groupid), null, array('%d'));
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('groupupdate-error', __('An error occurred while updating group(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
        
    }
    
    function delete($ids) {
        global $wpdb;
        
        $error = null;
        
        $subgrpids = array_map("intval", $wpdb->get_col("SELECT subgroupid FROM " . GIML_TABLE_PREFIX . "groupsubgroup WHERE groupid IN ({$ids})"));
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "group WHERE id IN ({$ids})");
        
        $tmpids = null;
        foreach ($subgrpids as $subgrpid) {
            if ($wpdb->get_var( "SELECT COUNT(*) FROM " . GIML_TABLE_PREFIX . "groupsubgroup WHERE groupid NOT IN ({$ids}) AND subgroupid = {$subgrpid}" ) == 0)
                $tmpids[] = $subgrpid;
        }
        if ($tmpids) {
            $tmpids = implode(",", $tmpids);
            $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "subgroup WHERE id IN (" . $tmpids . ")");
        }
        
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('groupupdate-error', __('An error occurred while deleting group(s): ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
}
?>