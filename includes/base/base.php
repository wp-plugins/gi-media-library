<?php

defined('ABSPATH') OR exit;

/**
 * 
 *
 * @author Zishan J.
 */
class GIML_Base {

    private static $error;
    private static $success;

    protected static function check_error($data, $custom_msg = "") {

        if (is_wp_error($data)) {
            self::$error .= "<b>ERROR:</b> " . ((!empty($custom_msg)) ? $custom_msg : $data->get_error_message()) . ((is_wp_error($data)) ? "" : "<br />");
        }
        return self::$error;
    }

    protected static function print_error() {

        if (!empty(self::$error))
            $msg = '<div class="error"><p>' . self::$error . '</p></div>';

        self::$error = "";
        return $msg;
    }

    protected static function print_success($msg) {
        //if (!empty($this->success))
        $msg = '<div class="updated"><p>' . $msg . '</p></div>';
        //$this->success = "";
        return $msg;
    }

    protected static function giml_table_playlist($subgroup_id, $sections, $page_link, $rowId=null) {
        
        $playlistcolumn = new GIML_Column();
        $sectioncolumn = new GIML_SectionColumn();
        $table = new GIML_Table();
        $tablecolumns = $table->get_columns($subgroup_id, FALSE);

        $playlist = null;

        if ($sections) {
            foreach ($sections as &$section) {
                if (intval($section['playlistsectionhide']) == 0) {
                    if (!empty($section['playlistsectiondownloadlink'])) {
                        $section['downloadsize'] = self::get_filesize($section['playlistsectiondownloadlink']);
                        $section['downloadimage'] = unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($section['playlistsectiondownloadlink'], '.'), 1)];
                        $section['playlistsectiondownloadlabel'] = (empty($section['playlistsectiondownloadlabel']) ? self::get_title($section['playlistsectiondownloadlink']) : $section['playlistsectiondownloadlabel']);
                        $section['playlistsectiondownloadlink'] = add_query_arg(['download-file' => $section['playlistsectiondownloadlink'], GIML_NONCE_NAME => wp_create_nonce('download_file_' . $section['playlistsectiondownloadlink'])], $page_link);
                    }
                }

                $sectionrow = $sectioncolumn->get($section['id'], $subgroup_id);
                if ($sectionrow) {
                    foreach ($tablecolumns as $col) {
                        foreach ($sectionrow as &$secrow) {
                            $tmpVal = null;
                            if ($col['playlisttablecolumntype'] === 'audio' && isset($secrow[$col['id']]) && !empty($secrow[$col['id']])) {
                                $tmpVal = self::get_audio_link($secrow[$col['id']], $playlist, $page_link);
                            } elseif ($col['playlisttablecolumntype'] === 'video' && isset($secrow[$col['id']]) && !empty($secrow[$col['id']])) {
                                $tmpVal = self::get_video_link($secrow[$col['id']], $page_link);
                            } elseif ($col['playlisttablecolumntype'] === 'download' && isset($secrow[$col['id']]) && !empty($secrow[$col['id']])) {
                                $tmpVal = self::get_download_link($secrow[$col['id']], $page_link);
                            } elseif ($col['playlisttablecolumntype'] === 'link' && isset($secrow[$col['id']]) && !empty($secrow[$col['id']])) {
                                $tmpVal = self::get_link($secrow[$col['id']]);
                            } elseif ($col['playlisttablecolumntype'] === 'iconiclink' && isset($secrow[$col['id']]) && !empty($secrow[$col['id']])) {
                                $tmpVal = self::get_iconic_link($secrow[$col['id']]);
                            }
                            if (is_array($tmpVal))
                                $secrow[$col['id']] = implode(' | ', $tmpVal);
                        }
                    }
                    $section['sectionrow'] = $sectionrow[0];
                }

                $tmprows = $playlistcolumn->get($section['id'], $subgroup_id, $rowId);
                if ($tmprows) {
                    foreach ($tablecolumns as $col) {
                        foreach ($tmprows as &$row) {
                            $tmpVal = null;
                            if ($col['playlisttablecolumntype'] === 'audio' && isset($row['data'][$col['id']]) && !empty($row['data'][$col['id']])) {
                                $tmpVal = self::get_audio_link($row['data'][$col['id']], $playlist, $page_link);
                            } elseif ($col['playlisttablecolumntype'] === 'video' && isset($row['data'][$col['id']]) && !empty($row['data'][$col['id']])) {
                                $tmpVal = self::get_video_link($row['data'][$col['id']], $page_link);
                            } elseif ($col['playlisttablecolumntype'] === 'download' && isset($row['data'][$col['id']]) && !empty($row['data'][$col['id']])) {
                                $tmpVal = self::get_download_link($row['data'][$col['id']], $page_link);
                            } elseif ($col['playlisttablecolumntype'] === 'link' && isset($row['data'][$col['id']]) && !empty($row['data'][$col['id']])) {
                                $tmpVal = self::get_link($row['data'][$col['id']]);
                            } elseif ($col['playlisttablecolumntype'] === 'iconiclink' && isset($row['data'][$col['id']]) && !empty($row['data'][$col['id']])) {
                                $tmpVal = self::get_iconic_link($row['data'][$col['id']]);
                            }
                            if (is_array($tmpVal))
                                $row['data'][$col['id']] = implode(' | ', $tmpVal);
                        }
                    }
                    $section['playlistrows'] = $tmprows;
                }
            }
        }
        $sections = ['audioplaylist' => $playlist, 'sections' => $sections];
        return $sections;
    }

    protected static function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    protected static function get_filesize($file) {
        $header = @get_headers($file, 1);
        $size = self::human_filesize($header['Content-Length']);
        //$size = $header['Content-Length']/2048 . " MB";
        /* if ($size <= 1024 )
          $size .= " bytes";
          elseif ($size > 1024 && $size < */

        return $size;
    }

    protected static function get_download_link($val, $post_link) {
        $downloads = explode('::', $val);
        $tmpVal = null;
        foreach ($downloads as $download) {
            list($url, $title) = explode("||", "$download||");
            $title = ((empty($title)) ? self::get_title($url) : $title);
            $tmp = '<a href="' . add_query_arg(['download-file' => rawurlencode($url), GIML_NONCE_NAME => wp_create_nonce('download_file_' . $url)], $post_link) . '" download>' . $title . '</a> ';
            if (isset(unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($url, '.'), 1)]))
                $tmp .= '<img title="Click to download" src="' . GIML_URI . 'images/' . unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($url, '.'), 1)] . '"> (' . self::get_filesize($url) . ')';
            else
                $tmp .= 'Click to download (' . self::get_filesize($url) . ')';
            
            $tmpVal [] = $tmp . '</a>';
        }
        return $tmpVal;
    }

    protected static function get_audio_link($val, &$playlist, $post_link) {
        $audios = explode('::', $val);
        $tmpVal = null;
        foreach ($audios as $audio) {
            list($url, $title) = explode("||", "$audio||");
            $title = ((empty($title)) ? self::get_title($url) : $title);
            $playlist[] = $url . '||' . $title . '||' . add_query_arg(['download-file' => rawurlencode($url), GIML_NONCE_NAME => wp_create_nonce('download_file_' . $url)], $post_link);
            $tmp = '<a href="' . $url . '" ng-click="play($event, \'' . addslashes(str_replace(" ", "%20", $url)) . '\')">';
            if (isset(unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($url, '.'), 1)]))
                $tmp .= '<img title="Click to play ' . $title . '" src="' . GIML_URI . 'images/' . unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($url, '.'), 1)] . '">';
            else
                $tmp .= 'Click to play ' . $title;
            
            $tmpVal[] = $tmp . '</a>';
        }
        return $tmpVal;
    }

    protected static function get_video_link($val, $post_link) {
        $videos = explode('::', $val);
        $tmpVal = null;
        $videonum = 1;
        foreach ($videos as $video) {
            list($type, $id) = explode("||", "$video||");
            switch ($type) {
                case "vimeo":
                    $url = 'player.vimeo.com/video/' . $id;
                    $title = '';
                    break;
                case "youtube":
                    $url = 'www.youtube.com/embed/' . $id . '?enablejsapi=1&origin=' . GIML_URL;
                    $title = "";
                default:
                    break;
            }
            $query = add_query_arg(['video-file' => rawurlencode($url), GIML_NONCE_NAME => wp_create_nonce('video_file_' . $url)], $post_link);
            $tmpVal[] = '<a href="#" onclick="window.open(\'' . $query . '\',\'GIML-Player\',\'width=640,height=390,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0\');return false;"><img title="Click to watch Video ' . $videonum . '" src="' . GIML_URI . 'images/' . unserialize(GIML_MEDIA_FORMATS)[$type] . '"></a>';
            $videonum++;
        }
        return $tmpVal;
    }

    protected static function get_link($val) {
        if (preg_match_all('/<a .*?href=[\'""](.+?)[\'""].*?>(.*?)<\/a>/si', $val, $matches)) {
            foreach ($matches[1] as $key => $link) {
                $links[] = $link . '||' . $matches[2][$key];
            }
        } else {
            $links = explode('::', $val);
        }
        $tmpVal = null;
        foreach ($links as $link) {
            list($url, $title) = explode("||", "$link||");
            $title = ((empty($title)) ? self::get_title($url) : $title);
            $tmpVal[] = "<a href=\"$url\" onclick=\"window.open('{$url}','_blank');return false;\">" . $title . "</a>";
        }
        return $tmpVal;
    }

    protected static function get_iconic_link($val) {
        $links = explode('::', $val);
        $tmpVal = null;
        $filenum = 1;
        foreach ($links as $link) {
            $tmp = '<a title="Click to open File ' . self::get_title($link) . '" href="' . $link . '" onclick="window.open(\'' . $link . '\',\'GIML-View\',\'width=650,height=550,location=0,menubar=0,status=0,titlebar=0,toolbar=0\');return false;">';
            if (isset(unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($link, '.'), 1)]))
                $tmp .= '<img src="' . GIML_URI . 'images/' . unserialize(GIML_MEDIA_FORMATS)[substr(strrchr($link, '.'), 1)] . '">';
            else
                $tmp .= 'Click here';
            
            $tmpVal[] = $tmp . '</a>';
            $filenum++;
        }
        return $tmpVal;
    }

    protected static function get_title($url) {
        $title = explode('/', $url);
        if (!empty($title))
            $title = urldecode(substr($title[count($title) - 1], 0, -(strlen(strrchr($url, '.')))));
        return trim($title);
    }

    protected static function search($search, $pageLink, $showPagination=false, $itemsPerPage=10, $startIndex=0) {
        if (empty($search))
            return null;
        
        require_once(GIML_INCLUDES . 'models/group.php');
        require_once(GIML_INCLUDES . 'models/subgroup.php');
        require_once(GIML_INCLUDES . 'models/combo.php');
        require_once(GIML_INCLUDES . 'models/table.php');
        require_once(GIML_INCLUDES . 'models/section.php');
        require_once(GIML_INCLUDES . 'models/sectioncolumn.php');
        require_once(GIML_INCLUDES . 'models/column.php');

        global $wpdb;
        
        $search = trim($search);
        $arrSearch[] = $search;//addslashes(addslashes($search)); //data are added in DB with slashes
        $orSearch = false;
search:
        //search in playlist column
        $tmpquery = "";
        
        //foreach($arrSearch as $val)
            $tmpquery .= "col.playlistcolumntext REGEXP '" . implode('|', $arrSearch) . "'";
        
        //$tmpquery = substr($tmpquery, 0, strlen($tmpquery)-3);
        
        /*$playlistColumns = $wpdb->get_results('SELECT rowid, playlistcolumnsectionid FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn '
                . 'WHERE ' . $tmpquery, ARRAY_A );*/
        $rowIds = $wpdb->get_col('SELECT distinct col.rowid '
                . 'FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn AS col, ' . GIML_TABLE_PREFIX . 'playlistcomboitem AS item, ' . GIML_TABLE_PREFIX . 'playlistsection AS sec '
                . 'WHERE ' . $tmpquery . ' AND col.playlistcolumnsectionid = sec.id AND sec.playlistsectioncomboitemid = item.id '
                . 'ORDER BY item.playlistcomboitemsortorder ASC, sec.playlistsectionsortorder ASC');// LIMIT ' . $startIndex . ', ' . $itemsPerPage);
        //$rowIds = array_unique(self::array_column($playlistColumns, 'rowid'));
        
        //search in section column
        $tmpquery = "";
        
        //foreach($arrSearch as $val)
            $tmpquery .= "seccol.playlistsectiontablecolumntext REGEXP '" . implode('|', $arrSearch) . "'";
        
        //$tmpquery = substr($tmpquery, 0, strlen($tmpquery)-3);
        
        /*$sectionIds = $wpdb->get_col('SELECT distinct playlistsectionid FROM ' . GIML_TABLE_PREFIX . 'playlistsectioncolumn '
                . 'WHERE ' . $tmpquery . ' ' . $where);*/
        
        $sectionIds = $wpdb->get_col('SELECT distinct seccol.playlistsectionid '
                . 'FROM ' . GIML_TABLE_PREFIX . 'playlistsectioncolumn AS seccol, ' . GIML_TABLE_PREFIX . 'playlistcomboitem AS item, ' . GIML_TABLE_PREFIX . 'playlistsection AS sec '
                . 'WHERE ' . $tmpquery . ' AND seccol.playlistsectionid = sec.id AND sec.playlistsectioncomboitemid = item.id '
                . 'ORDER BY item.playlistcomboitemsortorder ASC, sec.playlistsectionsortorder ASC');
        
        $playlistColumnSectionIds = $sectionIds;
        
               
        //search in subgroup
        $tmpquery = "";
        
        //foreach($arrSearch as $val)
            $tmpquery .= "subgrouplabel REGEXP '" . implode('|', $arrSearch) . "' OR subgrouprightlabel REGEXP '" . implode('|', $arrSearch) . "' OR subgroupleftlabel REGEXP '" . implode('|', $arrSearch) . "'";
        
        //$tmpquery = substr($tmpquery, 0, strlen($tmpquery)-3);
        
        $subgroupIds = $wpdb->get_col('SELECT id FROM ' . GIML_TABLE_PREFIX . 'subgroup '
                . 'WHERE ' . $tmpquery);
        
        if ((empty($subgroupIds) && empty($playlistColumnSectionIds) && empty($rowIds)) && !$orSearch) {
            $arrSearch = explode(" ", $search);
            $orSearch = true;
            goto search;
        }
        
        $tmpTable = new GIML_Table();
        $where = '';$sectionIds = null;
        if (!empty($playlistColumnSectionIds))
            $where = 'AND sec.id NOT IN(' . implode(', ', $playlistColumnSectionIds) . ')';
        $tmpSectionIds = [];
        foreach($subgroupIds as $id) {
            /*$sectionIds = $wpdb->get_col('SELECT distinct id FROM ' . GIML_TABLE_PREFIX . 'playlistsection '
                . 'WHERE playlisttableid = ' . $tmpTable->get($id)[0]['id'] . ' ' . $where);*/
            $sectionIds = $wpdb->get_col('SELECT distinct sec.id FROM ' . GIML_TABLE_PREFIX . 'playlistsection AS sec, ' . GIML_TABLE_PREFIX . 'playlistcomboitem AS item '
                    . 'WHERE sec.playlisttableid = ' . $tmpTable->get($id)[0]['id'] . ' ' . $where . ' AND sec.playlistsectioncomboitemid = item.id '
                    . 'ORDER BY item.playlistcomboitemsortorder ASC, sec.playlistsectionsortorder ASC');
            
            $tmpSectionIds = array_merge($tmpSectionIds, $sectionIds);
            $playlistColumnSectionIds = (!empty($sectionIds))?array_merge($playlistColumnSectionIds, $sectionIds):$playlistColumnSectionIds;
        }
        
        //get remaining rowids not present before
        $where = '';$where1 = ''; $where2 = '';
        if (!empty($rowIds))
            $where1 = 'col.rowid NOT IN(' . implode(', ', $rowIds) . ')';
        if (!empty($sectionIds))
            $where2 = 'col.playlistcolumnsectionid IN(' . implode(', ', $tmpSectionIds) . ')';
        if (!empty($where1) && !empty($where2))
            $where = $where1 . ' AND ' . $where2;
        else
            $where = (!empty($where1))?$where1:$where2;
        
        if (!empty($where) && !empty($sectionIds)) {
            /*$tmp = $wpdb->get_col('SELECT distinct rowid FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn '
                    . 'WHERE ' . $where);*/
            $tmp = $wpdb->get_col('SELECT distinct col.rowid FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn AS col, ' . GIML_TABLE_PREFIX . 'playlistcomboitem AS item, ' . GIML_TABLE_PREFIX . 'playlistsection AS sec '
                    . 'WHERE ' . $where . ' AND col.playlistcolumnsectionid = sec.id AND sec.playlistsectioncomboitemid = item.id '
                    . 'ORDER BY item.playlistcomboitemsortorder ASC, sec.playlistsectionsortorder ASC');
            
            $rowIds = array_unique(array_merge($rowIds, $tmp));
        }
        
        $totalItems = count($rowIds);
        if (empty($rowIds)) 
            return null;
        
        if ($showPagination) {
            $rowIds = array_slice($rowIds, $startIndex, $itemsPerPage);
            /*$playlistColumnSectionIds = $wpdb->get_col('SELECT distinct playlistcolumnsectionid FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn '
                    . 'WHERE rowid IN(' . implode(', ', $rowIds) . ')');*/
            $playlistColumnSectionIds = $wpdb->get_col('SELECT distinct col.playlistcolumnsectionid FROM ' . GIML_TABLE_PREFIX . 'playlistcolumn AS col, ' . GIML_TABLE_PREFIX . 'playlistcomboitem AS item, ' . GIML_TABLE_PREFIX . 'playlistsection AS sec '
                    . 'WHERE col.rowid IN(' . implode(', ', $rowIds) . ') AND col.playlistcolumnsectionid = sec.id AND sec.playlistsectioncomboitemid = item.id '
                    . 'ORDER BY item.playlistcomboitemsortorder ASC, sec.playlistsectionsortorder ASC');
            
            $sections = new GIML_Section();
            $sections = $sections->get_section($playlistColumnSectionIds, false);

            $tableIds = self::array_column($sections, 'playlisttableid');

            $subgroupIds = $wpdb->get_col('SELECT subgroupid FROM ' . GIML_TABLE_PREFIX . 'playlisttable '
                    . 'WHERE id IN(' . implode(', ', $tableIds) . ')');
        }
        
        //get groups
        $tmp = new GIML_Group();
        $groups = $tmp->get(true, null, $subgroupIds);
        array_shift($groups); //remove first element which is NONE
        
        //get all data at once
        $tmp = new GIML_Subgroup();
        $playlist = null;
        foreach ($groups as &$group) {
            $group['subgroups'] = $tmp->get(false, $subgroupIds, $group['id']);
            foreach ($group['subgroups'] as &$subgroup) {
                $tmpTable = new GIML_Table();
                $subgroup['tablecolumns'] = $tmpTable->get_columns($subgroup['id'], false);
                $subgroup['table'] = $tmpTable->get($subgroup['id'])[0];
        
                $tmpSection = new GIML_Section();
                $sections = $tmpSection->get($subgroup['table']['id'], null, false, $playlistColumnSectionIds);
                $data = self::giml_table_playlist($subgroup['id'], $sections, $pageLink, $rowIds);
                $subgroup['sections'] = $data['sections'];
                if ($data['audioplaylist']) {
                    foreach($data['audioplaylist'] as $audio)
                        $playlist[] = $audio;
                }
            }
        }
        $groups['total_items'] = $totalItems;
        $groups['groups'] = $groups;
        $groups['audioplaylist'] = $playlist;
        return $groups;
    }

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey. (Function included in PHP 5.5)
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    public static function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}

?>