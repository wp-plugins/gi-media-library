<?php

defined('ABSPATH') OR exit;

/**
 * Description of GIML_Combo
 *
 * @author Zishan J.
 */
class GIML_Combo {

    function __construct() {
        global $wpdb;

        $wpdb->hide_errors();
    }

    function get($subgroupid) {
        global $wpdb;

        $combo = $wpdb->get_row("
                    SELECT *
                    FROM " . GIML_TABLE_PREFIX . "playlistcombo
                    WHERE subgroupid = {$subgroupid}");

        $data = null;
        if ($combo) {
            $data[] = [
                'id' => intval($combo->id),
                'playlistcombolabel' => stripslashes($combo->playlistcombolabel),
                'playlistcombodirection' => $combo->playlistcombodirection,
                'playlistcombocss' => $combo->playlistcombocss
            ];
        }
        return $data;
    }

    function get_items($subgroupid, $sortbyname = true) {
        global $wpdb;
        $sortby = (!$sortbyname) ? "playlistcomboitemsortorder ASC" : "playlistcomboitemlabel ASC";
        $result = $wpdb->get_results("
                    SELECT playlistcomboitem.*
                    FROM " . GIML_TABLE_PREFIX . "playlistcombo AS playlistcombo, " . GIML_TABLE_PREFIX . "playlistcomboitem AS playlistcomboitem
                    WHERE playlistcombo.subgroupid = {$subgroupid} AND playlistcomboitem.playlistcomboid = playlistcombo.id
                    ORDER BY " . $sortby);
                    
        $data[] = ['id'=>0, 'playlistcomboitemlabel'=>'None'];
        
        foreach ($result as $item) {
            $data[] = [
                'id'=>intval($item->id), 
                'playlistcomboitemlabel'=>stripslashes($item->playlistcomboitemlabel),
                'playlistcomboitemdescription'=>stripslashes($item->playlistcomboitemdescription),
                'playlistcomboitemsortorder'=>intval($item->playlistcomboitemsortorder),
                'playlistcomboitemdownloadlink'=>stripslashes($item->playlistcomboitemdownloadlink),
                'playlistcomboitemdownloadlabel'=>stripslashes($item->playlistcomboitemdownloadlabel),
                'playlistcomboitemdownloadcss'=>$item->playlistcomboitemdownloadcss,
                'playlistcomboitemdefault'=>intval($item->playlistcomboitemdefault)];            
        }
        return $data;
    }
    
    function update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $data = [
                'playlistcombolabel' => sanitize_text_field(trim(stripslashes($row['playlistcombolabel']))),
                'playlistcombodirection' => sanitize_text_field(trim(stripslashes($row['playlistcombodirection']))),
                'playlistcombocss' => sanitize_text_field(trim(stripslashes($row['playlistcombocss'])))
            ];
            $comboid = intval($row['id']);
            $result = $wpdb->update(GIML_TABLE_PREFIX . 'playlistcombo', $data, array('id' => $comboid), null, array('%d'));
            if (!$result && !empty($wpdb->last_error))
                $error[] = $wpdb->last_error;
        }
        
        if ($error)
            return new WP_Error('comboupdate-error', __('An error occurred while updating combo: ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function items_update($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            $data = [
                'playlistcomboitemlabel'=>sanitize_text_field(trim(stripslashes($row['playlistcomboitemlabel']))),
                'playlistcomboitemdescription'=>wp_kses_post(trim(stripslashes($row['playlistcomboitemdescription']))),
                'playlistcomboitemsortorder'=>intval($row['playlistcomboitemsortorder']),
                'playlistcomboitemdownloadlink'=>esc_url_raw(trim(stripslashes($row['playlistcomboitemdownloadlink']))),
                'playlistcomboitemdownloadlabel'=>sanitize_text_field(trim(stripslashes($row['playlistcomboitemdownloadlabel']))),
                'playlistcomboitemdownloadcss'=>sanitize_text_field(trim(stripslashes($row['playlistcomboitemdownloadcss']))),
                'playlistcomboitemdefault'=>intval($row['playlistcomboitemdefault'])
            ];
            $comboid = intval($row['id']);
            $result = $wpdb->update(GIML_TABLE_PREFIX . 'playlistcomboitem', $data, array('id' => $comboid), null, array('%d'));
            if (!$result && !empty($wpdb->last_error))
                $error[] = $wpdb->last_error;
        }
        
        if ($error)
            return new WP_Error('comboitemsupdate-error', __('An error occurred while updating combo item(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }
    
    function items_add($rows) {
        global $wpdb;
        
        $error = null;
        
        foreach ($rows as $row) {
            if (trim($row['playlistcomboitemlabel']) !== "") {
                $data = [
                    'playlistcomboid'=>intval($row['playlistcomboid']),
                    'playlistcomboitemlabel'=>sanitize_text_field(trim(stripslashes($row['playlistcomboitemlabel']))),
                    'playlistcomboitemdescription'=>wp_kses_post(trim(stripslashes($row['playlistcomboitemdescription']))),
                    'playlistcomboitemsortorder'=>intval($row['playlistcomboitemsortorder']),
                    'playlistcomboitemdownloadlink'=>esc_url_raw(trim(stripslashes($row['playlistcomboitemdownloadlink']))),
                    'playlistcomboitemdownloadlabel'=>sanitize_text_field(trim(stripslashes($row['playlistcomboitemdownloadlabel']))),
                    'playlistcomboitemdownloadcss'=>sanitize_text_field(trim(stripslashes($row['playlistcomboitemdownloadcss']))),
                    'playlistcomboitemdefault'=>intval($row['playlistcomboitemdefault']),
                    'createddate' => date('Y-m-d H:i:s')
                ];
                
                $result = $wpdb->insert(GIML_TABLE_PREFIX . 'playlistcomboitem', $data);
                if (!$result && !empty($wpdb->last_error))
                    $error[] = $wpdb->last_error;
            }
        }
        
        if ($error)
            return new WP_Error('comboitemsadd-error', __('An error occurred while adding combo item(s): ' . join(", ", $error) . '<br/>', 'giml'));
        
        return true;
    }

    function items_delete($ids) {
        global $wpdb;
        
        $error = null;
        
        $result = $wpdb->query("DELETE FROM " . GIML_TABLE_PREFIX . "playlistcomboitem WHERE id IN ({$ids})");
    
        if (!$result && !empty($wpdb->last_error))
            return new WP_Error('comboitemdelete-error', __('An error occurred while deleting combo item(s): ' . $wpdb->last_error . '<br/>', 'giml'));
        
        return true;
    }
}
