<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_SectionColumn
 *
 * @author Zishan J.
 */
class GIML_SectionColumn {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }
    
    function get($sectionId, $subgroupId) {
        if (empty($sectionId) || empty($subgroupId))
            return null;
        
        global $wpdb;
        
        $table = new GIML_Table();
        $tablecols = $table->get_columns($subgroupId, FALSE);
        $totalcols = $wpdb->num_rows;
	
        $result = $wpdb->get_results("select 
                                        seccol . *
                                    from
                                        " . GIML_TABLE_PREFIX . "playlistsectioncolumn as `seccol`,
                                        " . GIML_TABLE_PREFIX . "playlisttablecolumn as `col`
                                    where
                                        seccol.playlisttablecolumnid = col.id
                                            and seccol.playlistsectionid = {$sectionId}
                                    order by col.playlisttablecolumnsortorder asc");
        
        $data = null;
        $tmp = [];
        
	foreach ($result as $seccol) {
            $tmp[$seccol->playlisttablecolumnid] = stripslashes($seccol->playlistsectiontablecolumntext);
	}
        if(count($tmp)>0) {
            $data[] = $tmp;
	}
        
        return $data;
    }
    
    function search ($search) {
        if (empty($search))
            return null;
        
        global $wpdb;
        
        $search = trim($search);
        $tmp = explode(" ", $search);
        $tmpquery = "";
        
        foreach($tmp as $val)
            $tmpquery .= "playlistsectiontablecolumntext LIKE '%" . $val . "%' OR ";
        
        $tmpquery = substr($tmpquery, 0, strlen($tmpquery)-3);
        
        $sectionIds = $wpdb->get_col('SELECT distinct playlistsectionid FROM ' . GIML_TABLE_PREFIX . 'playlistsectioncolumn WHERE ' . $tmpquery);
        
        $result = $wpdb->get_results("select 
                                        seccol . *
                                    from
                                        " . GIML_TABLE_PREFIX . "playlistsectioncolumn as `seccol`,
                                        " . GIML_TABLE_PREFIX . "playlisttablecolumn as `col`
                                    where
                                        seccol.playlisttablecolumnid = col.id
                                            and seccol.playlistsectionid IN (" . implode(', ', $sectionIds) . ")
                                    order by seccol.playlistsectionid asc, col.playlisttablecolumnsortorder asc");
        
        $data = null;
        $tmp = [];
        $sectionId = 0;
        
	foreach ($result as $seccol) {
            if ($sectionId == intval($seccol->playlistsectionid)) {
                $tmp[$seccol->playlisttablecolumnid] = stripslashes($seccol->playlistsectiontablecolumntext);
            }else{
                if (!empty($tmp)) {
                    $data[$sectionId] = $tmp;
                    $tmp = [];
                }
                $sectionId = intval($seccol->playlistsectionid);
                
                $tmp[$seccol->playlisttablecolumnid] = stripslashes($seccol->playlistsectiontablecolumntext);
            }
	}
        if(count($tmp)>0) {
            $data[$sectionId] = $tmp;
	}
        
        return $data;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistsectioncolumn WHERE playlistsectionid =" . intval($row['section']));
            foreach ($row['columns'] as $col) {
                $data = [
                    'playlistsectionid'=>intval($row['section']),
                    'playlisttablecolumnid' => intval($col['id']),
                    'playlistsectiontablecolumntext' => wp_kses_post(trim(stripslashes($col['data']))),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistsectioncolumn', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('sectioncolumnsupdate-error', __('An error occurred while updating section columns data: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function add($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $cols_count = $wpdb->get_var('SELECT COUNT(*) FROM ' . GIML_TABLE_PREFIX . 'playlistsectioncolumn WHERE playlistsectionid=' . intval($row['section']));
            if(intval($cols_count > 0)) {
                $section = new GIML_Section();
                
                return new WP_Error('sectioncolumnsadd-error', __("Section columns data already exists for section '" . strip_tags(stripslashes($section->get_section(intval($row['section']))[0]['playlistsectionlabel'])) . "'", 'giml'));
            }
            foreach ($row['columns'] as $col) {
                $data = [
                    'playlistsectionid'=>intval($row['section']),
                    'playlisttablecolumnid' => intval($col['id']),
                    'playlistsectiontablecolumntext' => wp_kses_post(trim(stripslashes($col['data']))),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistsectioncolumn', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('sectioncolumnsadd-error', __('An error occurred while adding section columns data: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function delete($sectionId) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistsectioncolumn WHERE playlistsectionid =" . intval($sectionId));
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('sectioncolumnsdelete-error', __('An error occurred while deleting section columns data: ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
}