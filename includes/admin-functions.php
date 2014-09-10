<?php

global $textareafields;
global $mediaformats;

$mediaformats = array(
			'mp3' => 'mp3-icon.gif',
			'pdf' => 'pdf-icon.gif',
			'ppt' => 'ppt-icon.jpg',
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
				);
$textareafields = array('grouplabel', 'subgrouplabel', 'subgroupdescription', 'playlistsectionlabel', 'playlistcomboitemdescription');

function get_filesize($file) {
	$header = @get_headers($file, 1);
	$size = human_filesize($header['Content-Length']);
	//$size = $header['Content-Length']/2048 . " MB";
	/*if ($size <= 1024 )
		$size .= " bytes";
	elseif ($size > 1024 && $size < */
	
	return $size;
}
function get_fileicon($link) {
	global $mediaformats;
	$link = trim($link);
	return "<img src=\"" . plugins_url( 'images/' . $mediaformats[substr(strrchr($link,'.'),1)], dirname(__FILE__)) . "\">";
}
function get_downloadhtml($link, $label="", $css="", $pluginurl="") {
	global $mediaformats;
	
	$css = trim($css);
	$downloadlabel = "";
	if (!is_null($link)&& $link!=="") {
		$link = html_entity_decode(trim($link));
		if (@array_key_exists(substr(strrchr($link,'.'),1), $mediaformats)) {
			//$query = add_query_arg('download-fileid', base64_encode(trim($link)), get_permalink($post->ID));
			if (function_exists('plugins_url')) {
				$query = plugins_url('download.php?fileid=' . base64_encode(trim($link)) . '&nonce=' . GIML_NONCE, dirname(__FILE__));
				$downloadlabel = "<span class=\"{$css}\"><a href=\"{$query}\">".trim($label)."&nbsp;<img title=\"Click to download\" src=\"" . 
						plugins_url( 'images/' . $mediaformats[substr(strrchr($link,'.'),1)], dirname(__FILE__)) . "\">" .
						"&nbsp;(" . get_filesize($link) . ")</a></span>";
			}else{
				$query = html_entity_decode($pluginurl) . 'download.php?fileid=' . base64_encode(trim($link)) . '&nonce=' . GIML_NONCE;
				$downloadlabel = "<span class=\"{$css}\"><a href=\"{$query}\">".trim($label)."&nbsp;<img title=\"Click to download\" src=\"" . 
						html_entity_decode($pluginurl) . 'images/' . $mediaformats[substr(strrchr($link,'.'),1)] . "\">" .
						"&nbsp;(" . get_filesize($link) . ")</a></span>";
			}
		}else{
			$downloadlabel = "";
		}
	}
	return $downloadlabel;
}
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}


function giml_sortplaylist($playlist, $sectionid) {
	global $giml_db;
	$sorteddata = $giml_db->get_distinctplaylistcolumnsbysection($sectionid);
	$tmpsortorder = "";
	$tmprowid = "";
	foreach($sorteddata as $key=>$row) {
		$tmpsortorder[$key] = $row->playlistsortorder;
	}
	if (!empty($tmpsortorder))
		array_multisort($tmpsortorder, SORT_ASC, SORT_NUMERIC, $sorteddata);
	
	//print "<pre>" . print_r($playlist, true) . "</pre>";
	$tmp = array();
	foreach($sorteddata as $data) {
		foreach($playlist as $col) {
			if ($data->rowid == $col->rowid) {
				$tmp[] = $col;
			}
		}
	}
	//print "<pre>" . print_r($tmp, true) . "</pre>";
	return $tmp;
}

function giml_get_audiolink($link, $title, $audionum="") {
	global $mediaformats;
	
	if(!is_null($link) && !empty($link)){
		list($url, $title1) = explode("||", $link);
		$url = html_entity_decode(trim($url));
		$query = plugins_url('audioplayer.php?fileid=' . base64_encode(str_replace(" ", "%20", $url)."||".$title1."||".str_replace(" ", "%20", GIML_URI)) . '&nonce=' . GIML_NONCE, dirname(__FILE__));
		$link = '<span class=""><a href="'.$url.'" onclick="window.open(\''.$query.'\',\'GIPlayer\',\'width=440,height=160,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0\');return false;"><img title="Click to listen Audio '.$audionum.'" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a></span>&nbsp;';
	}
	return $link;
}

function giml_get_videolink($video, $col_id, $videonum="") {
	global $mediaformats;
	
	if(!empty($video)){
		list($type, $id) = explode("||", $video);
                $src = '<iframe id="giml-video" src="//';
                switch ($type) {
                    case "vimeo":
                        $src .= 'player.vimeo.com/video/'.$id;
                        $title = '';
                        break;
                    case "youtube":
                        $src .= 'www.youtube.com/embed/'.$id.'?enablejsapi=1&origin='.GIML_URL;
                        $title = "";
                    default:
                        break;
                }
                $src .= '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		$query = plugins_url('videoplayer.php?fileid=' . base64_encode($src."||".$title) . '&nonce=' . GIML_NONCE, dirname(__FILE__));
		$link = '<span class=""><a href="#" onclick="window.open(\''.$query.'\',\'GIPlayer\',\'width=640,height=390,location=0,menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0\');return false;"><img title="Click to watch Video '.$videonum.'" src="' . plugins_url( 'images/' . $mediaformats[$type], dirname(__FILE__)) . '"></a></span>&nbsp;';
	}
	return $link;
}

function giml_get_groups() {
	global $giml_db;
	$group_option = "";
	$groups = $giml_db->get_groups();
	foreach ($groups as $group)
	{
		if(!empty($group->grouplabel))
			$group_option .= '<option value="' . $group->id . '">' . $group->grouplabel . '</option>';
	}
	die($group_option);
}

function giml_group_delete() {
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		global $giml_db;
                
                if (has_filter('giml_admin_group_delete')) {
                    $results = $giml_db->get_group($_POST['groupid']);
                    apply_filters('giml_admin_group_delete', '', (string)$results[0]->grouplabel);
                }
                
                $giml_db->group_delete($_POST['groupid']);
		$group_option = giml_get_groups();
			
		die($group_option);
	}
}

function giml_group_edit() {
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		global $giml_db;
		die(json_encode($giml_db->get_group($_POST['groupid'])));
	}
}

function giml_group_update() {
	global $giml_db;
	global $textareafields;
	
	$group_option = "";
	
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		for ($i=1; $i<=$_POST['rows']; $i++)
		{
			$data = array();
			$id = "";
			foreach ($_POST['fields'] as $field)
			{
				if ($id == "")
				{
					foreach ($_POST as $key => $value)
					{
						if (strstr($key, $field.$i.'_'))
						{
							$id = substr(strrchr($key, '_'),1);
							break;
						}
					}
				}
				if (sanitize_text_field($_POST['grouplabel'.$i.'_'.$id]) === "")
					continue 2;
				
				if (strpos($field, "sortorder") !== false || strpos($field, "number") !== false)
					$data[$field] = intval($_POST[$field.$i.'_'.$id]);
                                elseif (strpos($field, "link") !== false)
                                        $data[$field] = trim(esc_url_raw($_POST[$field.$i.'_'.$id]));
				elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
					$data[$field] = trim(wp_kses_post($_POST[$field.$i.'_'.$id]));
                                elseif (strpos($field, "price") !== false)
                                        $data[$field] = floatval($_POST[$field.$i.'_'.$id]);
				else
					$data[$field] = trim(sanitize_text_field($_POST[$field.$i.'_'.$id]));
			}
			if (count($data)>0) {
                            if (has_filter('giml_admin_group_update')) {
                                $g = $giml_db->get_group($id);
                                apply_filters('giml_admin_group_update', '', (string)$g[0]->grouplabel, $_POST['grouplabel'.$i.'_'.$id]);
                            }
                            
                            $giml_db->group_update($data, $id);
                        }
		}
		$group_option = giml_get_groups();
	}
	die($group_option);
}

function giml_group_add() {
	global $giml_db;
	global $textareafields;
	
	$group_option = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		for ($i=1; $i<=$_POST['rows']; $i++)
		{
			$data = array();
			foreach ($_POST['fields'] as $field)
			{
				if (sanitize_text_field($_POST['grouplabel'.$i]) === "")
					continue 2;
				
				if (strpos($field, "sortorder") !== false || strpos($field, "number") !== false)
					$data[$field] = intval($_POST[$field.$i]);
                                elseif (strpos($field, "link") !== false)
                                        $data[$field] = trim(esc_url_raw($_POST[$field.$i]));
				elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
					$data[$field] = trim(wp_kses_post($_POST[$field.$i]));
                                elseif (strpos($field, "price") !== false)
                                        $data[$field] = floatval($_POST[$field.$i]);
				else
					$data[$field] = trim(sanitize_text_field($_POST[$field.$i]));
			}
			$data["createddate"] = date('Y-m-d H:i:s');
			if (count($data)>0){
                            $gid = $giml_db->group_add($data);
                            
                            if (has_filter('giml_admin_group_add')) {
                                $g = $giml_db->get_group($gid);
                                apply_filters('giml_admin_group_add', '', (string)$g[0]->grouplabel);
                            }
                        }
		}
		$group_option = giml_get_groups();
	}
	die($group_option);
}

// SUBGROUPS

function giml_subgroup_delete() {
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		global $giml_db;
		
                if (has_filter('giml_admin_subgroup_delete')) {
                    $results = $giml_db->get_subgroup($_POST['subgroupid']);
                    apply_filters('giml_admin_subgroup_delete', '', (string)$results[0]->subgrouplabel, $_POST['subgroupid']);
                }
                
                $giml_db->subgroup_delete($_POST['subgroupid']);
                
		die(giml_get_subgroups());
	}
}

function giml_subgroup_edit() {
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		global $giml_db;
		die(json_encode($giml_db->get_subgroup($_POST['subgroupid'])));
	}
}

function giml_subgroup_update() {
	global $giml_db;
	global $textareafields;
	
	$subgroup_option = "";
	
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		for ($i=1; $i<=$_POST['rows']; $i++)
		{
			$data = array();
			if (isset($_POST['subgroupid']))
				$id = $_POST['subgroupid'];
			else
				$id = "";
			foreach ($_POST['fields'] as $field)
			{
				if ($id == "")
				{
					foreach ($_POST as $key => $value)
					{
						if (strstr($key, $field.$i.'_'))
						{
							$id = substr(strrchr($key, '_'),1);
							break;
						}
					}
				}
				if (isset($_POST['subgroupid']))
				{
                                        if (strpos($field, "sortorder") !== false || strpos($field, "number") !== false)
						$data[$field] = intval($_POST[$field.$i]);
					elseif (strpos($field, "link") !== false)
						$data[$field] = trim(esc_url_raw($_POST[$field.$i]));
					elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
						$data[$field] = trim(wp_kses_post($_POST[$field.$i]));
                                        elseif (strpos($field, "price") !== false)
						$data[$field] = floatval($_POST[$field.$i]);
					else
						$data[$field] = trim(sanitize_text_field($_POST[$field.$i]));
				}else{
					if (sanitize_text_field($_POST['subgrouplabel'.$i.'_'.$id]) === "")
						continue 2;
					
					$field1 = ($field === "subgroupgroup")?"groupid":$field;
					if (strpos($field, "sortorder") !== false || strpos($field, "number") !== false)
						$data[$field1] = intval($_POST[$field.$i.'_'.$id]);
                                        elseif (strpos($field, "link") !== false)
                                            $data[$field1] = trim(esc_url_raw($_POST[$field.$i.'_'.$id]));
					elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
						$data[$field1] = trim(wp_kses_post($_POST[$field.$i.'_'.$id]));
                                        elseif (strpos($field, "price") !== false)
						$data[$field1] = floatval($_POST[$field.$i.'_'.$id]);
					else
						$data[$field1] = trim(sanitize_text_field($_POST[$field.$i.'_'.$id]));
				}
			}
			if (count($data)>0) {
                            if (has_filter('giml_admin_subgroup_update')) {
                                $sg = $giml_db->get_subgroup($id);
                                apply_filters('giml_admin_subgroup_update', '', (string)$sg[0]->subgrouplabel, $_POST['subgrouplabel'.$i.'_'.$id], $id);
                            }
                            $giml_db->subgroup_update($data, $id);
                        }
		}
		$subgroup_option = giml_get_subgroups();
	}
	die($subgroup_option);
}

function giml_subgroup_add() {
	global $giml_db;
	global $textareafields;
	
	$subgroup_option = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		for ($i=1; $i<=$_POST['rows']; $i++)
		{
			$data = array();
			foreach ($_POST['fields'] as $field)
			{
				if (sanitize_text_field($_POST['subgrouplabel'.$i]) === "")
					continue 2;
					
				$field1 = ($field === "subgroupgroup")?"groupid":$field;
				
				if (strpos($field, "sortorder") !== false || strpos($field, "number") !== false)
					$data[$field1] = intval($_POST[$field.$i]);
                                elseif (strpos($field, "link") !== false)
                                        $data[$field1] = trim(esc_url_raw($_POST[$field.$i]));
				elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
					$data[$field1] = trim(wp_kses_post($_POST[$field.$i]));
                                elseif (strpos($field, "price") !== false)
					$data[$field1] = floatval($_POST[$field.$i]);
				else
					$data[$field1] = trim(sanitize_text_field($_POST[$field.$i]));
			}
			$data["createddate"] = date('Y-m-d H:i:s');
			if (count($data)>0){
                            $sgid = $giml_db->subgroup_add($data);
                            
                            if (has_filter('giml_admin_subgroup_add')) {
                                $sg = $giml_db->get_subgroup($sgid);
                                apply_filters('giml_admin_subgroup_add', '', (string)$sg[0]->subgrouplabel, $sgid);
                            }
                        }
		}
		$subgroup_option = giml_get_subgroups();
	}
	die($subgroup_option);
}

function get_independentsubgroups($sortbysortorder=false) {
	global $giml_db;
	$subgroup_option = "";
	
	$subgroups = $giml_db->get_independentsubgroups($sortbysortorder);
	
	foreach ($subgroups as $subgroup) {
		$subgroup_option .= '<option value="' . $subgroup->subgroupid . '">' . $subgroup->subgrouplabel . '</option>';
	}
	return $subgroup_option;
}

function get_groupsubgroups($groupid, $sortbysortorder=false) {
	global $giml_db;
	$subgroup_option = "";
	
	$subgroups = $giml_db->get_groupsubgroups($groupid, $sortbysortorder);
	foreach ($subgroups as $subgroup)
	{
		$subgroup_option .= '<option value="' . $subgroup->subgroupid . '">' . $subgroup->subgrouplabel . '</option>';
	}
	return $subgroup_option;
}

function giml_get_shortcodedata() {
	global $giml_db;
	
	$subgroup_option = "";
	$group_option = "";
	
	$mydata = "";
	switch($_POST['datatype']) {
		case 'tablecolumns':
			$rows = $giml_db->get_playlisttablecolumnssubgroup($_POST['subgroupid'], true);
			$tmp = "";
			foreach ($rows as $row)
			{
				if(!empty($row->playlisttablecolumnlabel))
					$tmp .= $row->playlisttablecolumnlabel . ',';
			}
			$tmp = substr($tmp, 0, strlen($tmp)-1);
			$mydata['tablecolumns'] = $tmp;
			break;
		case 'init':
			$subgroups = $giml_db->get_independentsubgroups(true);
			
			foreach ($subgroups as $subgroup) {
				$cols = "";
				$rows = $giml_db->get_playlisttablecolumnssubgroup($subgroup->subgroupid, true);
				foreach ($rows as $row)
				{
					if(!empty($row->playlisttablecolumnlabel))
						$cols .= $row->playlisttablecolumnlabel . ',';
				}
				$cols = substr($cols, 0, strlen($cols)-1);
				$subgroup_option .= '<option value="' . $subgroup->subgroupid . ':::' . sanitize_text_field($subgroup->subgrouplabel) . ':::' . $cols . '">' . $subgroup->subgrouplabel . '</option>';
			}
			$mydata['subgroups'] = $subgroup_option;
			
			$groups = $giml_db->get_groups(true);
			foreach ($groups as $group)
			{
				if(!empty($group->grouplabel))
					$group_option .= '<option value="' . $group->id . '">' . $group->grouplabel . '</option>';
			}
			$mydata['groups'] = $group_option;
			break;
		case 'admininit':
			if (check_ajax_referer(GIML_NONCE_NAME) ) {
				
				$mydata['subgroups'] = get_independentsubgroups();
				$mydata['subgroupsbysortorder'] = get_independentsubgroups(true);
				
				$groups = $giml_db->get_groups(true);
				foreach ($groups as $group)
				{
					if(!empty($group->grouplabel))
						$group_option .= '<option value="' . $group->id . '">' . $group->grouplabel . '</option>';
				}
			}
			$mydata['groups'] = $group_option;
			break;
		case 'groupsubgroups':
			$subgroups = $giml_db->get_groupsubgroups($_POST['groupid']);
			foreach ($subgroups as $subgroup)
			{
				$cols = "";
				$rows = $giml_db->get_playlisttablecolumnssubgroup($subgroup->subgroupid, true);
				foreach ($rows as $row)
				{
					if(!empty($row->playlisttablecolumnlabel))
						$cols .= $row->playlisttablecolumnlabel . ',';
				}
				$cols = substr($cols, 0, strlen($cols)-1);
				//if (is_null($subgroup->grouplabel))
					$subgroup_option .= '<option value="' . $subgroup->subgroupid . ':::' . sanitize_text_field($subgroup->subgrouplabel) . ':::' . $cols . '">' . $subgroup->subgrouplabel . '</option>';
				//else
				//	$subgroup_option .= '<option value="' . $subgroup->subgroupid . ':::' . sanitize_text_field($subgroup->grouplabel) . ':::' . $cols . '">' . $subgroup->grouplabel . ' > ' . $subgroup->subgrouplabel . '</option>';
			}
			
			$mydata['subgroups'] = $subgroup_option;
			break;
		case 'admingroupsubgroups':
			if (check_ajax_referer(GIML_NONCE_NAME) ) {
				$mydata['subgroups'] = get_groupsubgroups($_POST['groupid']);
				$mydata['subgroupsbysortorder'] = get_groupsubgroups($_POST['groupid'], true);
			}
			 
			break;
		default:
	}
	
	die(json_encode($mydata));
}

function giml_get_subgroups() {
	global $giml_db;
	$subgroup_option = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		$subgroups = $giml_db->get_subgroups();
		foreach ($subgroups as $subgroup)
		{
			if (is_null($subgroup->grouplabel))
				$subgroup_option .= '<option value="' . $subgroup->subgroupid . '">' . $subgroup->subgrouplabel . '</option>';
			else
				$subgroup_option .= '<option value="' . $subgroup->subgroupid . '">' . $subgroup->grouplabel . ' > ' . $subgroup->subgrouplabel . '</option>';
		}
	}
	die($subgroup_option);
}
function giml_get_playlistcolumns() {
	global $giml_db;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		$mydata['playlistcolumns'] = get_playlistcolumnssubgroup($_POST['subgroupid'], $_POST['sectionid']);
	}
	
	die(json_encode($mydata));
}

function giml_get_playlistcombosections() {
	global $giml_db;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		$mydata['playlistsection'] = get_playlistcombosections($_POST['comboitemid'], null, ($_POST['sortbysortorder']==="true")?true:false, $_POST['subgroupid']);
	}
	die(json_encode($mydata));
}

function giml_get_playlistcombosectioncolumns() {
	global $giml_db;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		$mydata['playlistsectioncolumn'] = get_playlistsectioncolumnssubgroup($_POST['subgroupid'], $_POST['comboitemid']);
	}
	die(json_encode($mydata));
}

function giml_get_playlistdata() {
	global $giml_db;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		$subgroupid = $_POST['subgroupid'];
		$tmp = "";
		
		$results = $giml_db->get_subgroup($subgroupid);
		$mydata['subgroupdownloadlabel'] = (string)$results[0]->subgroupdownloadlabel;
		$mydata['subgroupdownloadlink'] = (string)$results[0]->subgroupdownloadlink;
		$mydata['subgroupdownloadcss'] = (string)$results[0]->subgroupdownloadcss;
		$mydata['subgroupshowfilter'] = (string)$results[0]->subgroupshowfilter;
		$mydata['subgroupshowcombo'] = (string)$results[0]->subgroupshowcombo;
		
		$results = $giml_db->get_playlisttablecolumnssubgroup($subgroupid);
		$mydata['playlisttablecss'] = (string)$results[0]->playlisttablecss;
		$mydata['playlisttablecolumn'] = (string)get_playlisttablecolumnssubgroup($subgroupid, true);
		$results = $giml_db->get_playlistcomboitemssubgroup($subgroupid);
		$mydata['playlistcombolabel'] = (string)$results[0]->playlistcombolabel;
		$mydata['playlistcombocss'] = (string)$results[0]->playlistcombocss;
		$mydata['playlistcombodirection'] = (string)$results[0]->playlistcombodirection;
		$mydata['playlistcomboitem'] = (string)get_playlistcomboitemssubgroup($subgroupid);
		$mydata['playlistcomboitembysortorder'] = (string)get_playlistcomboitemssubgroup($subgroupid, null, false, true);
		//$mydata['playlistsection'] = (string)get_playlistsectionssubgroup($subgroupid, null, true);
		$mydata['playlistsection'] = (string)get_independentplaylistsectionssubgroup($subgroupid, null, true);
		$mydata['playlistcolumnsections'] = (string)get_playlistcolumnsectionssubgroup($subgroupid);
		$mydata['playlistsectioncolumn'] = (string)get_playlistsectioncolumnssubgroup($subgroupid , 0);
		//$mydata['playlistcolumns'] = (string)get_playlistcolumnssubgroup($subgroupid);
		$mydata['playlistcolumns'] = "";
		$mydata['playlistplaylistsections'] = (string)get_playlistplaylistsectionssubgroup($subgroupid);
	}
	die(json_encode($mydata));
}
function giml_insert() {
	global $giml_db;
	global $textareafields;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		$table = $_POST['table'];
		switch($table)
		{
			case 'playlistsectioncolumn': case 'playlistcolumn':
				for ($i=1; $i<=$_POST['rows']; $i++) {
					if($table === 'playlistcolumn') {
						if(empty($_POST["playlistcolumnsectionid".$i]))
							continue;
					}else{
						if(empty($_POST["playlistsectionid".$i]))
							continue;
							
						if ($giml_db->checksectioncolumnscreated($_POST["playlistsectionid".$i]))
							continue;
					}
					
					$data = array();
					
					if($table === 'playlistcolumn'){
						$rowid = $giml_db->get_playlistnextrowid();
						$data['playlistcolumnsectionid'] = $_POST["playlistcolumnsectionid".$i];
					}else{
						$data['playlistsectionid'] = $_POST["playlistsectionid".$i];
					}
					
					foreach ($_POST['fields'] as $field) {
						$id = "";
						foreach ($_POST as $key => $value) {
							if (strstr($key, str_replace('_', $i.'_', $field)) && strlen($key)==strlen(str_replace('_', $i.'_', $field))) {
								$id = substr(strrchr($key, '_'),1);
								break;
							}
						}
						//if id is null due to first field playlistsectionid
						if($id=="")
							continue;
						
						if($table === 'playlistcolumn') {
							$data['playlistcolumntext'] = trim(wp_kses_post($_POST[str_replace('_', $i.'_', $field)]));
							$data['rowid'] = $rowid;
							$data['playlistsortorder'] = intval($_POST['playlistsortorder'.$i]);
						}else{
							$data['playlistsectiontablecolumntext'] = trim(wp_kses_post($_POST[str_replace('_', $i.'_', $field)]));
						}
						
						$data['playlisttablecolumnid'] = $id;
						$data["createddate"] = date('Y-m-d H:i:s');
						if (count($data)>0)
							$giml_db->insert($table, $data);
						
					}
				}
				if($table === 'playlistcolumn') {
					$result['playlistcolumns'] = "";//(string)get_playlistcolumnssubgroup($_POST['subgroupid']);
					$result['playlistplaylistsections'] = "";//(string)get_playlistplaylistsectionssubgroup($_POST['subgroupid']);
				}else{
					$result['playlistsectioncolumn'] = (string)get_playlistsectioncolumnssubgroup($_POST['subgroupid'], 0);
					$result['playlistcolumnsections'] = (string)get_playlistcolumnsectionssubgroup($_POST['subgroupid']);
				}

				break;
			case 'playlisttablecolumn': case 'playlistcomboitem': case 'playlistsection':
				for ($i=1; $i<=$_POST['rows']; $i++) {
					$data = array();
					foreach ($_POST['fields'] as $field) {
						if (sanitize_text_field($_POST[$table.'label'.$i]) === "")
							continue 2;
						
						if (strpos($field, "sortorder") !== false)
							$data[$field] = intval($_POST[$field.$i]);
						elseif (strpos($field, "link") !== false)
							$data[$field] = trim(esc_url_raw($_POST[$field.$i]));
						elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
							$data[$field] = trim(wp_kses_post($_POST[$field.$i]));
						else
							$data[$field] = trim(sanitize_text_field($_POST[$field.$i]));
					}
					$data["createddate"] = date('Y-m-d H:i:s');
					if (count($data)>0)
						$giml_db->insert($table, $data, $_POST['subgroupid']);
				}
				giml_get_playlistdata();
						
				/*
				switch($table) {
					case 'playlisttablecolumn':
						$result['playlisttablecolumn'] = get_playlisttablecolumnssubgroup($_POST['subgroupid']);
						break;
					case 'playlistcomboitem':
						$result['playlistcomboitem'] = get_playlistcomboitemssubgroup($_POST['subgroupid']);
						break;
					case 'playlistsection':
						$result['playlistsection'] = get_playlistsectionssubgroup($_POST['subgroupid']);
						$result['playlistcolumnsections'] = get_playlistcolumnsectionssubgroup($_POST['subgroupid']);
						break;
				}*/
				break;
			default:
			
		}
	}
	die(json_encode($result));
}
function giml_update() {
	global $giml_db;
	global $textareafields;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		$table = $_POST['table'];
		switch($table) {
			case 'playlisttable': case 'playlistcombo':
				foreach ($_POST['fields'] as $field) {
					$data[$field] = trim(sanitize_text_field($_POST[$field]));
				}
				if (count($data)>0)
					$giml_db->update($table, $data, array('subgroupid'=>$_POST['subgroupid']));
				break;
			case 'playlistsectioncolumn': case 'playlistcolumn':
				for ($i=1; $i<=$_POST['rows']; $i++) {
					$data = array();
					if($table === 'playlistcolumn'){
						if(empty($_POST["playlistcolumnsectionid".$i]))
							continue;

						$data['playlistcolumnsectionid'] = $_POST["playlistcolumnsectionid".$i];
						$sectionid1 = $_POST["playlistcolumnsectionid".$i];
						$giml_db->delete($table, $_POST['ids']);
						
						$rowid = $giml_db->get_playlistnextrowid();
					}else{
						$oldsectionids = explode(",", $_POST['ids']);

						if(empty($_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]]))
							continue;
						
						if ($_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]] != $oldsectionids[$i-1]) {
							if ($giml_db->checksectioncolumnscreated($_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]]))
								continue;
						}
						$ids = explode(",", $_POST['ids']);
						$giml_db->delete($table, $ids[$i-1]);
					}
												
					foreach ($_POST['fields'] as $field) {
						
						if($table === 'playlistcolumn') {
							//if($field === 'playlistcolumnsectionid')
							if(strpos($field, 'playlistcolumntext') === false)
								continue;
						}else{
							//if($field === 'playlistsectionid')
							if(strpos($field, 'playlistsectiontablecolumntext') === false)
								continue;
							
							$data['playlistsectionid'] = $_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]];
						}
						
						$sectionid = "";
						foreach ($_POST as $key => $value) {
							//get the column id
							if (strstr($key, str_replace('_', $i.'_', $field)) && strlen($key)==strlen(str_replace('_', $i.'_', $field))) {
								$id = substr(strrchr($key, '_'),1);
								//list($tablecolumnid, $sectionid) = split('_', substr(strstr($key, '_'),1));
								break;
							}
						}
						//skip the field if its sectionid
						if($id=="")
							continue;
						
						if($table === 'playlistcolumn') {
							$data['rowid'] = $rowid;
							$data['playlistcolumntext'] = trim(wp_kses_post($_POST[str_replace('_', $i.'_', $field)]));
							$data['playlistsortorder'] = intval($_POST['playlistsortorder'.$i]);
						}else{
							$data['playlistsectiontablecolumntext'] = trim(wp_kses_post($_POST[str_replace('_', $i.'_', $field)]));
						}
						
						if (count($data)>0) {
							//if($table === 'playlistcolumn') 
								//$giml_db->update($table, $data, array('playlistcolumnsectionid'=>$sectionid, 'playlisttablecolumnid'=>$tablecolumnid));
							//else
								//$giml_db->update($table, $data, array('playlistsectionid'=>$sectionid, 'playlisttablecolumnid'=>$tablecolumnid));
								
							
							$data['playlisttablecolumnid'] = $id;
							$data["createddate"] = date('Y-m-d H:i:s');
							$giml_db->insert($table, $data);
							
						}
						
					}
				}
    
				if($table === 'playlistcolumn') {
					$result['playlistcolumns'] = "";//(string)get_playlistcolumnssubgroup($_POST['subgroupid']);
					$result['playlistplaylistsections'] = "";//(string)get_playlistplaylistsectionssubgroup($_POST['subgroupid']);
				}else{
					$result['playlistsectioncolumn'] = (string)get_playlistsectioncolumnssubgroup($_POST['subgroupid'], 0);
					$result['playlistcolumnsections'] = (string)get_playlistcolumnsectionssubgroup($_POST['subgroupid']);
				}
				break;
			default:
				for ($i=1; $i<=$_POST['rows']; $i++) {
					$data = array();
					$id = "";
					foreach ($_POST['fields'] as $field) {
						if ($id == "") {
							foreach ($_POST as $key => $value) {
								if (strstr($key, $field.$i.'_')) {
									$id = substr(strrchr($key, '_'),1);
									break;
								}
							}
						}
						
						if (sanitize_text_field($_POST[$table.'label'.$i.'_'.$id]) === "")
							continue 2;
						
						if (strpos($field, "sortorder") !== false)
							$data[$field] = intval($_POST[$field.$i.'_'.$id]);
						elseif (strpos($field, "link") !== false)
							$data[$field] = trim(esc_url_raw($_POST[$field.$i.'_'.$id]));
						elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
							$data[$field] = trim(wp_kses_post($_POST[$field.$i.'_'.$id]));
						else
							$data[$field] = trim(sanitize_text_field($_POST[$field.$i.'_'.$id]));
					}
					if (count($data)>0)
						$giml_db->update($table, $data, array('id'=>$id));
				}
				giml_get_playlistdata();
						
				/*
				switch ($table) {
					case 'playlisttablecolumn':
						$result['playlisttablecolumn'] = get_playlisttablecolumnssubgroup($_POST['subgroupid']);
						break;
					case 'playlistcomboitem':
						$result['playlistcomboitem'] = get_playlistcomboitemssubgroup($_POST['subgroupid']);
						break;
					case 'playlistsection':
						$result['playlistsection'] = get_playlistsectionssubgroup($_POST['subgroupid']);
						$result['playlistcolumnsections'] = get_playlistcolumnsectionssubgroup($_POST['subgroupid']);
						break;
				}*/
		}
	}
	die(json_encode($result));
}
function giml_edit() {
	global $giml_db;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) )
	{
		$table = $_POST['table'];
		switch($table)
		{
			default:
				$result = $giml_db->select($table, $_POST['ids'], 1);
			
		}
	}
	die(json_encode($result));
}
function giml_delete() {
	global $giml_db;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer(GIML_NONCE_NAME) ) {
		$table = $_POST['table'];
		switch($table) {
			default:
				$giml_db->delete($table, $_POST['ids']);
				
				giml_get_playlistdata();
				/*
				switch($table) {
					case 'playlisttablecolumn':
						$result['playlisttablecolumn'] = get_playlisttablecolumnssubgroup($_POST['subgroupid']);
						break;
					case 'playlistcomboitem':
						$result['playlistcomboitem'] = get_playlistcomboitemssubgroup($_POST['subgroupid']);
						break;
					case 'playlistsection':
						$result['playlistsection'] = get_playlistsectionssubgroup($_POST['subgroupid']);
						$result['playlistcolumnsections'] = get_playlistcolumnsectionssubgroup($_POST['subgroupid']);
						break;
				}*/
		}
	}
	die(json_encode($result));
}

function get_playlisttablecolumnssubgroup($subgroupid, $sortbysortorder=false) {
	global $giml_db;
	
	$results = $giml_db->get_playlisttablecolumnssubgroup($subgroupid, $sortbysortorder);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlisttablecolumnlabel))
			$tmp .= '<option value="' . $data->id . '">' . $data->playlisttablecolumnlabel . '</option>';
	}
	return $tmp;
}
function get_playlistcomboitemssubgroup($subgroupid, $selectedid=null, $defaultselected=false, $sortbysortorder=false) {
	global $giml_db;
	
	$results = $giml_db->get_playlistcomboitemssubgroup($subgroupid, $sortbysortorder);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistcomboitemlabel)) {
			if($defaultselected == true) {
					$selected = ($data->playlistcomboitemdefault==1)?'selected="selected"':"";
			}else{
				$selected = (!is_null($selectedid) && intval($data->id)==intval($selectedid))?'selected="selected"':"";
			}
			$tmp .= '<option value="' . $data->id . '" ' . $selected . '>' . $data->playlistcomboitemlabel . '</option>';
		}
	}
	return $tmp;
}
function get_playlistcomboitemssubgroupfirstid($subgroupid) {
	global $giml_db;
	
	$id = $giml_db->get_playlistcomboitemssubgroupfirstid($subgroupid);
	
	return $id;
}
function get_playlistcombosectionssubgroup($subgroupid, $selectedid=null, $sortbysortorder=false) {
	global $giml_db;
	
	$results = $giml_db->get_playlistcombosectionssubgroup($subgroupid, $sortbysortorder);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistsectionlabel)) {
			$selected = (!is_null($selectedid) && intval($data->id)==intval($selectedid))?'selected="selected"':"";
			$tmp .= '<option value="' . $data->id . '" ' . $selected . '>' . $data->playlistsectionlabel . '</option>';
		}
	}
	return $tmp;
}
function get_playlistcombosections($comboitemid, $selectedid=null, $sortbysortorder=false, $subgroupid=null) {
	global $giml_db;
	
	$results = $giml_db->get_playlistcombosections($comboitemid, $sortbysortorder, 0, $subgroupid);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistsectionlabel)) {
			$selected = (!is_null($selectedid) && intval($data->id)==intval($selectedid))?'selected="selected"':"";
			$tmp .= '<option value="' . $data->id . '" ' . $selected . '>' . $data->playlistsectionlabel . '</option>';
		}
	}
	return $tmp;
}
function get_independentplaylistsectionssubgroup($subgroupid, $selectedid=null, $sortbysortorder=false) {
	global $giml_db;
	
	$results = $giml_db->get_independentplaylistsectionssubgroup($subgroupid, $sortbysortorder);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistsectionlabel)) {
			$selected = (!is_null($selectedid) && intval($data->id)==intval($selectedid))?'selected="selected"':"";	
			$tmp .= '<option value="' . $data->id . '" ' . $selected . '>' . $data->playlistsectionlabel . '</option>';
		}
	}
	return $tmp;
}
function get_playlistsectionssubgroup($subgroupid, $selectedid=null, $sortbysortorder=false) {
	global $giml_db;
	
	$results = $giml_db->get_playlistsectionssubgroup($subgroupid, $sortbysortorder);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistsectionlabel)) {
			$selected = (!is_null($selectedid) && intval($data->id)==intval($selectedid))?'selected="selected"':"";	
			if (!$sortbysortorder)
				$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";
			else
				$comboitemlabel = "";
			$tmp .= '<option value="' . $data->id . '" ' . $selected . '>' . $comboitemlabel . $data->playlistsectionlabel . '</option>';
		}
	}
	return $tmp;
}
function get_playlistcolumnssubgroup($subgroupid, $sectionid=null) {
	global $giml_db;
	
	$results = $giml_db->get_playlistcolumnssubgroup($subgroupid, $sectionid);
	$tmp = $giml_db->get_playlisttablecolumnssubgroup($subgroupid);
	$totalcols = $giml_db->get_numrows();
	$option = "";
	//$colid = array();
	$section = "";
	$rowid = 0;
	$col = 1;
	$ids = "";
	foreach ($results as $data) {
		/*if (in_array($data->playlisttablecolumnid, $colid)) {
			$option = substr($option, 0, strlen($option)-4);
			$option .= '</option>';
			$option = str_replace("[+ids+]", substr($ids, 0, strlen($ids)-1), $option);
			$ids = $data->id . ",";
			$option .= '<option value="[+ids+]">' . $comboitemlabel . $section . ' > ' . $data->playlistcolumntext . ' :: ';
			$colid = array();
			$colid[] = $data->playlisttablecolumnid;
		}else{
			$ids .= $data->id . ",";
			if ($option !== "") {
				$option .= $data->playlistcolumntext . ' :: ';
			}else{
				$section = $data->playlistsectionlabel;
				$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";
				$option .= '<option value="[+ids+]">' . $comboitemlabel . $section . ' > ' . $data->playlistcolumntext . ' :: ';
			}
			$colid[] = $data->playlisttablecolumnid;
		}*/
		$columntext = htmlentities((strlen($data->playlistcolumntext)>30)?substr($data->playlistcolumntext,0,29).'...':$data->playlistcolumntext, ENT_COMPAT | ENT_HTML401, "UTF-8");
		if ($rowid == $data->rowid) {
			if($col > $totalcols) {
				$col = 1;
				$option = substr($option, 0, strlen($option)-4);
				$option .= '</option>';
				$option = str_replace("[+ids+]", substr($ids, 0, strlen($ids)-1), $option);
				$ids = $data->id . ",";
				$option .= '<option value="[+ids+]">';
				if (is_null($sectionid))
					$option .= $comboitemlabel . $data->playlistsectionlabel . ' > ';
			}else
				$ids .= $data->id . ",";
			$option .= $columntext . ' :: ';
		}else{
			if ($option !== "") {
				$option = substr($option, 0, strlen($option)-4);
				$option .= '</option>';
				$option = str_replace("[+ids+]", substr($ids, 0, strlen($ids)-1), $option);
				$col = 1;
				$ids = "";
			}
			$ids .= $data->id . ",";
			//$section = $data->playlistsectionlabel;
			$rowid = intval($data->rowid);
			$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";
			if (is_null($sectionid))
				$option .= '<option value="[+ids+]">' . $comboitemlabel . $data->playlistsectionlabel . ' > ' . $columntext . ' :: ';
			else
				$option .= '<option value="[+ids+]">' . $columntext . ' :: ';
		}
		$col++;
	}
	if($option !== "") {
		$option = substr($option, 0, strlen($option)-4);
		$option .= '</option>';
		$option = str_replace("[+ids+]", substr($ids, 0, strlen($ids)-1), $option);
	}
	return $option;
}
function get_playlistsectioncolumnssubgroup($subgroupid, $comboitemid) {
	global $giml_db;
	
	$results = $giml_db->get_playlistsectioncolumnssubgroup($subgroupid, $comboitemid);
	$tmp = $giml_db->get_playlisttablecolumnssubgroup($subgroupid);
	$totalcols = $giml_db->get_numrows();
	$col = 1;
	$option = "";
	$tmp = "";
	$section = "";
	$comboitemlabel = "";
	foreach ($results as $data) {
            $columntext = htmlentities((strlen($data->playlistsectiontablecolumntext)>30)?substr($data->playlistsectiontablecolumntext,0,29).'...':$data->playlistsectiontablecolumntext, ENT_COMPAT | ENT_HTML401, "UTF-8");
            if ($section === $data->playlistsectionlabel) {
                    if($col > $totalcols) {
                            $col = 1;
                            $option = substr($option, 0, strlen($option)-4);
                            $option .= '</option>';
                            $option .= '<option value="' . $data->comboitemid . '_' . $data->playlistsectionid . '">' . $comboitemlabel . $section . ' > ';
                    }
                    $option .= $columntext . ' :: ';
            }else{
                    if ($option !== "") {
                            $option = substr($option, 0, strlen($option)-4);
                            $option .= '</option>';
                            $col = 1;
                    }
                    $section = $data->playlistsectionlabel;

                    //if ($comboitemid > 0)
                    //	$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";

                    $option .= '<option value="' . $data->comboitemid . '_' . $data->playlistsectionid . '">' . $comboitemlabel . $section . ' > ' . $columntext . ' :: ';
            }
            $col++;
	}
	if($option !== "") {
		$option = substr($option, 0, strlen($option)-4);
		$option .= '</option>';
	}
	return $option;
}
function get_playlistplaylistsectionssubgroup($subgroupid) {
	global $giml_db;
	
	$results = $giml_db->get_playlistplaylistsectionssubgroup($subgroupid);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistsectionlabel)) {
			$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";
			$tmp .= '<option value="' . $data->id . '">' . $comboitemlabel . $data->playlistsectionlabel . '</option>';
		}
	}
	return $tmp;
}
function get_playlistcolumnsectionssubgroup($subgroupid) {
	global $giml_db;
	
	$results = $giml_db->get_playlistcolumnsectionssubgroup($subgroupid);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlistsectionlabel)) {
			$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";
			$tmp .= '<option value="' . $data->id . '">' . $comboitemlabel . $data->playlistsectionlabel . '</option>';
		}
	}
	return $tmp;
}
?>