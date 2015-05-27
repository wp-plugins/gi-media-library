<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_Subgroup
 *
 * @author Zishan J.
 */
class GIML_Subgroup {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }

    function get($sortbyname=true, $id=NULL, $groupid=NULL) {
        global $wpdb;
        
        $sortby = (!$sortbyname) ? "subgroupsortorder ASC" : "subgrouplabel ASC";
        if (!is_null($id)) {
            $id = (is_null($id))?0:$id;
            $where = (!is_array($id)?("subgroup.id".(($id==0)?">0":"=".$id)):("subgroup.id IN (" . join(",", $id) . ")"));
        }
        $where1 = '';
        if (!is_null($groupid)) {
            if (!is_null($id))
                $where1 = $where;
            //$where = (!is_array($groupid)?("grpsubgrp.groupid" . (($groupid==0)?">0":"=".$groupid)):("grpsubgrp.groupid IN (" . join(",", $groupid) . ")"));
            $where = (!is_array($groupid)?("grpsubgrp.groupid=" . $groupid):("grpsubgrp.groupid IN (" . join(",", $groupid) . ")"));
            $where = (!empty($where1))? $where1 . ' AND ' . $where:$where;
        }
        
        
        $result = $wpdb->get_results(""
                . "SELECT 
                    subgroup . *, grpsubgrp.groupid
                FROM
                    " . GIML_TABLE_PREFIX . "subgroup AS `subgroup`,
                    " . GIML_TABLE_PREFIX . "groupsubgroup AS `grpsubgrp`
                WHERE
                    subgroup.id = grpsubgrp.subgroupid
                    AND {$where} 
                ORDER BY {$sortby}");
                
        $data = null;
        
        foreach ($result as $subgroup) {
            $data[] = [
                'id'=>intval($subgroup->id), 
                'groupid'=>intval($subgroup->groupid),
                'subgrouplabel'=>stripslashes(trim($subgroup->subgrouplabel)),
                'subgrouprightlabel'=>stripslashes(trim($subgroup->subgrouprightlabel)),
                'subgroupleftlabel'=>stripslashes(trim($subgroup->subgroupleftlabel)),
                'subgroupcss'=>trim($subgroup->subgroupcss),
                'subgroupdescription'=>stripslashes(trim($subgroup->subgroupdescription)),
                'subgroupsortorder'=>intval($subgroup->subgroupsortorder),
                'subgroupdirection'=>$subgroup->subgroupdirection,
                'subgroupdownloadlink'=>trim($subgroup->subgroupdownloadlink),
                'subgroupdownloadlabel'=>stripslashes(trim($subgroup->subgroupdownloadlabel)),
                'subgroupdownloadcss'=>trim($subgroup->subgroupdownloadcss),
                'subgroupshowfilter'=>intval($subgroup->subgroupshowfilter),
                'subgroupshowcombo'=>intval($subgroup->subgroupshowcombo)];
        }
        return $data;
    }
    
    function get_independentsubgroups($sortbyname=true) {
        global $wpdb;
        
        $sortby = (!$sortbyname) ? "subgroupsortorder ASC" : "subgrouplabel ASC";
                
        $result = $wpdb->get_results(""
                . "SELECT 
                    subgroup . *, grpsubgrp.groupid
                FROM
                    " . GIML_TABLE_PREFIX . "subgroup AS `subgroup`,
                    " . GIML_TABLE_PREFIX . "groupsubgroup AS `grpsubgrp`
                WHERE
                    grpsubgrp.groupid = 0
                        AND subgroup.id = grpsubgrp.subgroupid
                ORDER BY {$sortby}");
        
        $data = null;
        
        foreach ($result as $subgroup) {
            $data[] = [
                'id'=>intval($subgroup->id), 
                'groupid'=>intval($subgroup->groupid),
                'subgrouplabel'=>stripslashes(trim($subgroup->subgrouplabel)),
                'subgrouprightlabel'=>stripslashes(trim($subgroup->subgrouprightlabel)),
                'subgroupleftlabel'=>stripslashes(trim($subgroup->subgroupleftlabel)),
                'subgroupcss'=>trim($subgroup->subgroupcss),
                'subgroupdescription'=>stripslashes(trim($subgroup->subgroupdescription)),
                'subgroupsortorder'=>intval($subgroup->subgroupsortorder),
                'subgroupdirection'=>$subgroup->subgroupdirection,
                'subgroupdownloadlink'=>trim($subgroup->subgroupdownloadlink),
                'subgroupdownloadlabel'=>stripslashes(trim($subgroup->subgroupdownloadlabel)),
                'subgroupdownloadcss'=>trim($subgroup->subgroupdownloadcss),
                'subgroupshowfilter'=>intval($subgroup->subgroupshowfilter),
                'subgroupshowcombo'=>intval($subgroup->subgroupshowcombo)];
        }
        return $data;
    }

    function get_groupsubgroups($groupid, $sortbysortorder = false) {
        global $wpdb;
        $sortby = ($sortbysortorder == false) ? "subgroup.subgrouplabel ASC" : "subgroup.subgroupsortorder ASC";
        $sql = "
                SELECT 
                    subgroup . *, `group`.grouplabel, grpsubgrp.groupid
                FROM
                    " . GIML_TABLE_PREFIX . "group AS `group`,
                    " . GIML_TABLE_PREFIX . "subgroup AS `subgroup`,
                    " . GIML_TABLE_PREFIX . "groupsubgroup AS `grpsubgrp`
                WHERE
                    grpsubgrp.groupid = " . intval($groupid) . "
                        AND `group`.id = grpsubgrp.groupid
                        AND subgroup.id = grpsubgrp.subgroupid
                ORDER BY {$sortby}
		";
        $result = $wpdb->get_results($sql);
        
        $data = null;
        foreach ($result as $subgroup) {
            $data[] = [
                'id'=>intval($subgroup->id), 
                'groupid'=>  json_encode(array_map('intval', $wpdb->get_col("SELECT groupid FROM " . GIML_TABLE_PREFIX . 'groupsubgroup WHERE subgroupid=' . $subgroup->id))),
                //'grouplabel'=> json_encode(array_map('stripslashes', $wpdb->get_col("SELECT group.grouplabel FROM " . GIML_TABLE_PREFIX . "group AS `group`, " . GIML_TABLE_PREFIX . "groupsubgroup AS `grpsubgrp` WHERE group.id=grpsubgrp.groupid AND grpsubgrp.subgroupid=" . $subgroup->id))),
                'subgrouplabel'=>stripslashes(trim($subgroup->subgrouplabel)),
                'subgrouprightlabel'=>stripslashes(trim($subgroup->subgrouprightlabel)),
                'subgroupleftlabel'=>stripslashes(trim($subgroup->subgroupleftlabel)),
                'subgroupcss'=>trim($subgroup->subgroupcss),
                'subgroupdescription'=>stripslashes(trim($subgroup->subgroupdescription)),
                'subgroupsortorder'=>intval($subgroup->subgroupsortorder),
                'subgroupdirection'=>$subgroup->subgroupdirection,
                'subgroupdownloadlink'=>trim($subgroup->subgroupdownloadlink),
                'subgroupdownloadlabel'=>stripslashes(trim($subgroup->subgroupdownloadlabel)),
                'subgroupdownloadcss'=>trim($subgroup->subgroupdownloadcss),
                'subgroupshowfilter'=>intval($subgroup->subgroupshowfilter),
                'subgroupshowcombo'=>intval($subgroup->subgroupshowcombo)];
        }
        
        return $data;
    }
    
    function add($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (trim($row['subgrouplabel']) !== "") {
                $data = [
                    'subgrouplabel' => wp_kses_post(trim(stripslashes($row['subgrouplabel']))),
                    'subgrouprightlabel' => sanitize_text_field(trim(stripslashes($row['subgrouprightlabel']))),
                    'subgroupleftlabel' => sanitize_text_field(trim(stripslashes($row['subgroupleftlabel']))),
                    'subgroupcss' => sanitize_text_field(trim(stripslashes($row['subgroupcss']))),
                    'subgroupdescription' => wp_kses_post(trim(stripslashes($row['subgroupdescription']))),
                    'subgroupdirection' => sanitize_text_field(trim($row['subgroupdirection'])),
                    'subgroupsortorder' => intval($row['subgroupsortorder']),
                    'createddate' => date('Y-m-d H:i:s')
                ];

                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'subgroup', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
                else {
                    $tmpid = $wpdb->insert_id;
                    $data = null;
                    $row['groupid'] = is_array(json_decode($row['groupid']))?json_decode($row['groupid']):json_decode("[" . $row['groupid'] . "]");
                    foreach ($row['groupid'] as $grpid) {
                        $result = $wpdb->insert(GIML_TABLE_PREFIX . 'groupsubgroup', ['groupid' => $grpid, 'subgroupid' => $tmpid]);
                    }
                    if (!$result && !empty($wpdb->last_error))
                        $error[] = $wpdb->last_error;
                    
                    $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlisttable', ['subgroupid' => $tmpid]);
                    $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistcombo', ['subgroupid' => $tmpid]);
                }
            }
        }
        
        if ($error)
            return new WP_Error('subgroupinsert-error', __('An error occurred while creating new subgroup(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (isset($row['update_downloadlink'])) {
                $data = [
                    'subgroupdownloadlink' => esc_url_raw(trim(stripslashes($row['subgroupdownloadlink']))),
                    'subgroupdownloadlabel' => sanitize_text_field(trim(stripslashes($row['subgroupdownloadlabel']))),
                    'subgroupdownloadcss' => sanitize_text_field(trim(stripslashes($row['subgroupdownloadcss']))),
                    'subgroupshowfilter' => sanitize_text_field(trim($row['subgroupshowfilter'])),
                    'subgroupshowcombo' => wp_kses_post(trim($row['subgroupshowcombo']))
                ];
                $subgroupid = intval($row['id']);
                $result = $wpdb->update(GIML_TABLE_PREFIX . 'subgroup', $data, array('id' => $subgroupid), null, array('%d'));
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }elseif (trim($row['subgrouplabel']) !== "") {
                $data = [
                    'subgrouplabel' => wp_kses_post(trim(stripslashes($row['subgrouplabel']))),
                    'subgrouprightlabel' => sanitize_text_field(trim(stripslashes($row['subgrouprightlabel']))),
                    'subgroupleftlabel' => sanitize_text_field(trim(stripslashes($row['subgroupleftlabel']))),
                    'subgroupcss' => sanitize_text_field(trim(stripslashes($row['subgroupcss']))),
                    'subgroupdescription' => wp_kses_post(trim(stripslashes($row['subgroupdescription']))),
                    'subgroupdirection' => sanitize_text_field(trim($row['subgroupdirection'])),
                    'subgroupsortorder' => intval($row['subgroupsortorder'])
                ];
                $subgroupid = intval($row['id']);
                $result = $wpdb->update(GIML_TABLE_PREFIX . 'subgroup', $data, array('id' => $subgroupid), null, array('%d'));
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
                else {
                    $grpids = $wpdb->get_col("SELECT groupid FROM " . GIML_TABLE_PREFIX . 'groupsubgroup WHERE subgroupid=' . $subgroupid);
                    $row['groupid'] = is_array(json_decode($row['groupid']))?json_decode($row['groupid']):json_decode("[" . $row['groupid'] . "]");
                    $tmpgrpids = array_diff($row['groupid'], $grpids);
                                        
                    foreach ($tmpgrpids as $grpid) {
                        $result = $wpdb->insert(GIML_TABLE_PREFIX . 'groupsubgroup', ['groupid' => $grpid, 'subgroupid' => $subgroupid]);
                    }
                    if (!$result && !empty($wpdb->last_error))
                        $error[] = $wpdb->last_error;
                    else {
                        $tmpgrpids = array_diff($grpids, $row['groupid']);
                        if (count($tmpgrpids)>0) {
                            $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "groupsubgroup WHERE subgroupid=" . $subgroupid . " AND groupid IN (" . implode(",", $tmpgrpids) . ")");
                            if (!$result && !empty($wpdb->last_error))
                                $error[] = $wpdb->last_error;
                        }
                    }
                }
            }
        }
        
        if ($error)
            return new WP_Error('subgroupupdate-error', __('An error occurred while updating subgroup(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
        
    }
    
    function delete($ids) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "subgroup WHERE id IN ({$ids})");
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('subgroupupdate-error', __('An error occurred while deleting subgroup(s): ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
    
}
?>