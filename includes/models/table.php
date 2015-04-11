<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_Table
 *
 * @author Zishan J.
 */
class GIML_Table {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }

    function get($subgroupid=null, $id=NULL) {
        if (empty($subgroupid) && empty($id))
            return null;
        
        global $wpdb;

        if (!is_null($id)) {
            $where = (!is_array($id)?("id=".$id):("id IN (" . join(",", $id) . ")"));
        }else{
            $where = 'subgroupid = ' . $subgroupid;
        }
        $where = 'WHERE ' . $where;
        $table = $wpdb->get_row("
                    SELECT *
                    FROM " . GIML_TABLE_PREFIX . "playlisttable " . $where);

        $data = null;
        if ($table) {
            $data[] = [
                'id' => intval($table->id),
                'subgroupid' => intval($table->subgroupid),
                'playlisttablecss' => $table->playlisttablecss
            ];
        }
        return $data;
    }
    
    function get_columns($subgroupid, $sortbyname = true) {
        global $wpdb;
        $sortby = (!$sortbyname) ? "playlisttablecolumnsortorder ASC" : "playlisttablecolumnlabel ASC";
        $result = $wpdb->get_results("
                    SELECT tablecolumn.*
                    FROM " . GIML_TABLE_PREFIX . "playlisttable AS `table`, " . GIML_TABLE_PREFIX . "playlisttablecolumn AS tablecolumn
                    WHERE table.subgroupid = {$subgroupid} AND tablecolumn.playlisttableid = table.id
                    ORDER BY " . $sortby);
                    
        $data = null;
        
        foreach ($result as $item) {
            $data[] = [
                'id'=>intval($item->id), 
                'playlisttablecolumnlabel'=>stripslashes($item->playlisttablecolumnlabel),
                'playlisttablecolumncss'=>$item->playlisttablecolumncss,
                'playlisttablecolumndirection'=>$item->playlisttablecolumndirection,
                'playlisttablecolumnsortorder'=>intval($item->playlisttablecolumnsortorder),
                'playlisttablecolumntype'=>$item->playlisttablecolumntype];            
        }
        return $data;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $data = [
                'playlisttablecss' => sanitize_text_field(trim($row['playlisttablecss']))
            ];
            $tableid = intval($row['id']);
            $result = $wpdb->update(GIML_TABLE_PREFIX . 'playlisttable', $data, array('id' => $tableid), null, array('%d'));
            if (!$result && !empty($wpdb->last_error))
                $error[] = $wpdb->last_error;
        }
        
        if ($error)
            return new WP_Error('comboupdate-error', __('An error occurred while updating table: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function columns_update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $data = [
                'playlisttablecolumnlabel'=>sanitize_text_field(trim(stripslashes($row['playlisttablecolumnlabel']))),
                'playlisttablecolumncss'=>sanitize_text_field(trim($row['playlisttablecolumncss'])),
                'playlisttablecolumndirection'=>sanitize_text_field(trim($row['playlisttablecolumndirection'])),
                'playlisttablecolumnsortorder'=>intval($row['playlisttablecolumnsortorder']),
                'playlisttablecolumntype'=>sanitize_text_field(trim($row['playlisttablecolumntype']))
            ];
            $tableid = intval($row['id']);
            $result = $wpdb->update(GIML_TABLE_PREFIX . 'playlisttablecolumn', $data, array('id' => $tableid), null, array('%d'));
            if (!$result && !empty($wpdb->last_error))
                $error[] = $wpdb->last_error;
        }
        
        if ($error)
            return new WP_Error('tablecolumnsupdate-error', __('An error occurred while updating table column(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function columns_add($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (trim($row['playlisttablecolumnlabel']) !== "") {
                $data = [
                    'playlisttableid'=>intval($row['playlisttableid']),
                    'playlisttablecolumnlabel'=>sanitize_text_field(trim(stripslashes($row['playlisttablecolumnlabel']))),
                    'playlisttablecolumncss'=>sanitize_text_field(trim($row['playlisttablecolumncss'])),
                    'playlisttablecolumndirection'=>sanitize_text_field(trim($row['playlisttablecolumndirection'])),
                    'playlisttablecolumnsortorder'=>intval($row['playlisttablecolumnsortorder']),
                    'playlisttablecolumntype'=>sanitize_text_field(trim($row['playlisttablecolumntype'])),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlisttablecolumn', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('tablecolumnsadd-error', __('An error occurred while adding table column(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }

    function columns_delete($ids) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlisttablecolumn WHERE id IN ({$ids})");
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('tablecolumnsdelete-error', __('An error occurred while deleting table column(s): ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
}