<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_Column
 *
 * @author Zishan J.
 */
class GIML_Column {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }
    
    function get($sectionId, $subgroupId=null, $rowId=null) {
        if (empty($sectionId))
            return null;
        
        global $wpdb;
        
        $where = '';
        if (!is_null($rowId)) {
            $where = (!is_array($rowId)?("col.rowid=".$rowId):("col.rowid IN (" . join(",", $rowId) . ")"));
            $where = 'AND ' . $where;
        }
        //$result = $wpdb->get_results("SELECT * FROM " . GIML_TABLE_PREFIX . "playlistcolumn WHERE playlistcolumnsectionid=" . $sectionId . " ORDER BY playlistsortorder ASC");
        $result = $wpdb->get_results("select 
                                        col . *
                                    from
                                        " . GIML_TABLE_PREFIX . "playlistcolumn as `col`,
                                        " . GIML_TABLE_PREFIX . "playlisttablecolumn as `tblcol`
                                    where
                                        col.playlisttablecolumnid = tblcol.id
                                            and col.playlistcolumnsectionid = {$sectionId} {$where}
                                    order by col.rowid asc, tblcol.playlisttablecolumnsortorder asc");
                                            
        $data = null;
        
        $tmp = [];
	$rowid = 0;
        $sortorder = 0;
	
        foreach ($result as $key=>$col) {
            if ($rowid == intval($col->rowid)) {
                $tmp[$col->playlisttablecolumnid] = stripslashes($col->playlistcolumntext);
                $sortorder = intval($col->playlistsortorder);
            }else{
                if(!empty($tmp)){
                    $data[] = ['rowid'=>$rowid, 'playlistsortorder'=>$sortorder, 'data'=>$tmp];
                    $tmp = [];
                }
                $rowid = intval($col->rowid);
                
                $tmp[$col->playlisttablecolumnid] = stripslashes($col->playlistcolumntext);
                $sortorder = intval($col->playlistsortorder);
            }
        }
	if(count($tmp)>0) {
            $data[] = ['rowid'=>$rowid, 'playlistsortorder'=>$sortorder, 'data'=>$tmp];
	}
        if ($data) {
            foreach ($data as $key => $row) {
                $playlistsortorder[$key]  = $row['playlistsortorder'];
            }
            array_multisort($playlistsortorder, SORT_ASC, $data);
        }
        return $data;
    }
    
    function search($search) {
        if (empty($search))
            return null;
        
        global $wpdb;
        
        $search = trim($search);
        $tmp = explode(" ", $search);
        $tmpquery = "";
        
        foreach($tmp as $val)
            $tmpquery .= "playlistcolumntext LIKE '%" . $val . "%' OR ";
        
        $tmpquery = substr($tmpquery, 0, strlen($tmpquery)-3);
        
        $rowIds = $wpdb->get_col('SELECT distinct rowid FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn WHERE ' . $tmpquery);
        
        $result = $wpdb->get_results("SELECT col.* "
                . "FROM " . GIML_TABLE_PREFIX . "playlistcolumn as col, "
                . GIML_TABLE_PREFIX . "playlisttablecolumn as `tblcol` "
                . "WHERE col.rowid IN (" . implode(', ', $rowIds) . ") AND "
                . "col.playlisttablecolumnid = tblcol.id "
                . "order by col.rowid asc, tblcol.playlisttablecolumnsortorder asc");
        
        $data = null;
        
        $tmp = [];
	$rowid = 0;
        $sortorder = 0;
	
        foreach ($result as $key=>$col) {
            if ($rowid == intval($col->rowid)) {
                $tmp[$col->playlisttablecolumnid] = stripslashes($col->playlistcolumntext);
                $sortorder = intval($col->playlistsortorder);
            }else{
                if(!empty($tmp)){
                    $data[] = ['rowid'=>$rowid, 'playlistsortorder'=>$sortorder, 'data'=>$tmp];
                    $tmp = [];
                }
                $rowid = intval($col->rowid);
                
                $tmp[$col->playlisttablecolumnid] = stripslashes($col->playlistcolumntext);
                $sortorder = intval($col->playlistsortorder);
            }
        }
	if(count($tmp)>0) {
            $data[] = ['rowid'=>$rowid, 'playlistsortorder'=>$sortorder, 'data'=>$tmp];
	}
        if ($data) {
            foreach ($data as $key => $row) {
                $playlistsortorder[$key]  = $row['playlistsortorder'];
            }
            array_multisort($playlistsortorder, SORT_ASC, $data);
        }
        
        return $data;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistcolumn "
                    . "WHERE rowid=" . intval($row['id']));
            
            $rowid = $wpdb->get_var("select max(rowid) from " . GIML_TABLE_PREFIX . "playlistcolumn");
            $rowid++;
        
            foreach ($row['columns'] as $col) {
                $data = [
                    'rowid' => intval($rowid),
                    'playlistsortorder' => intval($row['playlistsortorder']),
                    'playlistcolumnsectionid'=>intval($row['section']),
                    'playlisttablecolumnid' => intval($col['id']),
                    'playlistcolumntext' => wp_kses_post(trim(stripslashes($col['data']))),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistcolumn', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('columnsupdate-error', __('An error occurred while updating columns data: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function add($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $rowid = $wpdb->get_var("select max(rowid) from " . GIML_TABLE_PREFIX . "playlistcolumn");
            $rowid++;
        
            foreach ($row['columns'] as $col) {
                $data = [
                    'rowid' => intval($rowid),
                    'playlistsortorder' => intval($row['playlistsortorder']),
                    'playlistcolumnsectionid'=>intval($row['section']),
                    'playlisttablecolumnid' => intval($col['id']),
                    'playlistcolumntext' => wp_kses_post(trim(stripslashes($col['data']))),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistcolumn', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('columnsadd-error', __('An error occurred while adding columns data: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function delete($rowIds) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistcolumn WHERE rowid IN ({$rowIds})");
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('columnsdelete-error', __('An error occurred while deleting columns data: ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
}