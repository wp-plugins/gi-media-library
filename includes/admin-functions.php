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
			'zip' => 'zip-icon.png'
				);
global $nonce;
global $nonce_name;
$nonce = "";
$nonce_name = 'gi-medialibrary-action';
if (function_exists('wp_create_nonce')) {
	$nonce = wp_create_nonce($nonce_name);
}
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
	global $nonce;
	$css = trim($css);
	$downloadlabel = "";
	if (!is_null($link)&& $link!=="") {
		$link = html_entity_decode(trim($link));
		if (@array_key_exists(substr(strrchr($link,'.'),1), $mediaformats)) {
			//$query = add_query_arg('download-fileid', base64_encode(trim($link)), get_permalink($post->ID));
			if (function_exists('plugins_url')) {
				$query = plugins_url('download.php?fileid=' . base64_encode(trim($link)) . '&nonce=' . $nonce, dirname(__FILE__));
				$downloadlabel = "<span class=\"{$css}\"><a href=\"{$query}\">".trim($label)."&nbsp;<img title=\"Click to download\" src=\"" . 
						plugins_url( 'images/' . $mediaformats[substr(strrchr($link,'.'),1)], dirname(__FILE__)) . "\">" .
						"&nbsp(" . get_filesize($link) . ")</a></span>";
			}else{
				$query = html_entity_decode($pluginurl) . 'download.php?fileid=' . base64_encode(trim($link)) . '&nonce=' . $nonce;
				$downloadlabel = "<span class=\"{$css}\"><a href=\"{$query}\">".trim($label)."&nbsp;<img title=\"Click to download\" src=\"" . 
						html_entity_decode($pluginurl) . 'images/' . $mediaformats[substr(strrchr($link,'.'),1)] . "\">" .
						"&nbsp(" . get_filesize($link) . ")</a></span>";
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

function giml_get_groups() {
	global $mydb;
	$group_option = "";
	$groups = $mydb->get_groups();
	foreach ($groups as $group)
	{
		if(!empty($group->grouplabel))
			$group_option .= '<option value="' . $group->id . '">' . $group->grouplabel . '</option>';
	}
	die($group_option);
}

function giml_group_delete() {
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		global $mydb;
		$mydb->group_delete($_POST['groupid']);
		$group_option = giml_get_groups();
			
		die($group_option);
	}
}

function giml_group_edit() {
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		global $mydb;
		die(json_encode($mydb->get_group($_POST['groupid'])));
	}
}

function giml_group_update() {
	global $mydb;
	global $textareafields;
	
	$group_option = "";
	
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
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
				
				if (strpos($field, "sortorder") !== false)
					$data[$field] = intval($_POST[$field.$i.'_'.$id]);
				elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
					$data[$field] = trim(wp_kses_post($_POST[$field.$i.'_'.$id]));
				else
					$data[$field] = trim(sanitize_text_field($_POST[$field.$i.'_'.$id]));
			}
			if (count($data)>0)
				$mydb->group_update($data, $id);
		}
		$group_option = giml_get_groups();
	}
	die($group_option);
}

function giml_group_add() {
	global $mydb;
	global $textareafields;
	
	$group_option = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		for ($i=1; $i<=$_POST['rows']; $i++)
		{
			$data = array();
			foreach ($_POST['fields'] as $field)
			{
				if (sanitize_text_field($_POST['grouplabel'.$i]) === "")
					continue 2;
				
				if (strpos($field, "sortorder") !== false)
					$data[$field] = intval($_POST[$field.$i]);
				elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
					$data[$field] = trim(wp_kses_post($_POST[$field.$i]));
				else
					$data[$field] = trim(sanitize_text_field($_POST[$field.$i]));
			}
			$data["createddate"] = date('Y-m-d H:i:s');
			if (count($data)>0)
				$mydb->group_add($data);
		}
		$group_option = giml_get_groups();
	}
	die($group_option);
}

// SUBGROUPS

function giml_subgroup_delete() {
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		global $mydb;
		$mydb->subgroup_delete($_POST['subgroupid']);
		
		die(giml_get_subgroups());
	}
}

function giml_subgroup_edit() {
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		global $mydb;
		die(json_encode($mydb->get_subgroup($_POST['subgroupid'])));
	}
}

function giml_subgroup_update() {
	global $mydb;
	global $textareafields;
	
	$subgroup_option = "";
	
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
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
					if ($field === "subgroupdownloadlink")
						$data[$field] = trim(esc_url_raw($_POST[$field.$i]));
					elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
						$data[$field] = trim(wp_kses_post($_POST[$field.$i]));
					else
						$data[$field] = trim(sanitize_text_field($_POST[$field.$i]));
				}else{
					if (sanitize_text_field($_POST['subgrouplabel'.$i.'_'.$id]) === "")
						continue 2;
					
					$field1 = ($field === "subgroupgroup")?"groupid":$field;
					if (strpos($field, "sortorder") !== false)
						$data[$field1] = intval($_POST[$field.$i.'_'.$id]);
					elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
						$data[$field1] = trim(wp_kses_post($_POST[$field.$i.'_'.$id]));
					else
						$data[$field1] = trim(sanitize_text_field($_POST[$field.$i.'_'.$id]));
				}
			}
			if (count($data)>0)
				$mydb->subgroup_update($data, $id);
		}
		$subgroup_option = giml_get_subgroups();
	}
	die($subgroup_option);
}

function giml_subgroup_add() {
	global $mydb;
	global $textareafields;
	
	$subgroup_option = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		for ($i=1; $i<=$_POST['rows']; $i++)
		{
			$data = array();
			foreach ($_POST['fields'] as $field)
			{
				if (sanitize_text_field($_POST['subgrouplabel'.$i]) === "")
					continue 2;
					
				$field1 = ($field === "subgroupgroup")?"groupid":$field;
				
				if (strpos($field, "sortorder") !== false)
					$data[$field1] = intval($_POST[$field.$i]);
				elseif (array_search($field, $textareafields)!==false && array_search($field, $textareafields)!==null)
					$data[$field1] = trim(wp_kses_post($_POST[$field.$i]));
				else
					$data[$field1] = trim(sanitize_text_field($_POST[$field.$i]));
			}
			$data["createddate"] = date('Y-m-d H:i:s');
			if (count($data)>0)
				$mydb->subgroup_add($data);
		}
		$subgroup_option = giml_get_subgroups();
	}
	die($subgroup_option);
}

function get_independentsubgroups($sortbysortorder=false) {
	global $mydb;
	$subgroup_option = "";
	
	$subgroups = $mydb->get_independentsubgroups($sortbysortorder);
	
	foreach ($subgroups as $subgroup) {
		$subgroup_option .= '<option value="' . $subgroup->subgroupid . '">' . $subgroup->subgrouplabel . '</option>';
	}
	return $subgroup_option;
}

function get_groupsubgroups($groupid, $sortbysortorder=false) {
	global $mydb;
	$subgroup_option = "";
	
	$subgroups = $mydb->get_groupsubgroups($groupid, $sortbysortorder);
	foreach ($subgroups as $subgroup)
	{
		$subgroup_option .= '<option value="' . $subgroup->subgroupid . '">' . $subgroup->subgrouplabel . '</option>';
	}
	return $subgroup_option;
}

function giml_get_shortcodedata() {
	global $mydb;
	
	$subgroup_option = "";
	$group_option = "";
	
	$mydata = "";
	switch($_POST['datatype']) {
		case 'tablecolumns':
			$rows = $mydb->get_playlisttablecolumnssubgroup($_POST['subgroupid'], true);
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
			$subgroups = $mydb->get_independentsubgroups(true);
			
			foreach ($subgroups as $subgroup) {
				$cols = "";
				$rows = $mydb->get_playlisttablecolumnssubgroup($subgroup->subgroupid, true);
				foreach ($rows as $row)
				{
					if(!empty($row->playlisttablecolumnlabel))
						$cols .= $row->playlisttablecolumnlabel . ',';
				}
				$cols = substr($cols, 0, strlen($cols)-1);
				$subgroup_option .= '<option value="' . $subgroup->subgroupid . ':::' . sanitize_text_field($subgroup->subgrouplabel) . ':::' . $cols . '">' . $subgroup->subgrouplabel . '</option>';
			}
			$mydata['subgroups'] = $subgroup_option;
			
			$groups = $mydb->get_groups(true);
			foreach ($groups as $group)
			{
				if(!empty($group->grouplabel))
					$group_option .= '<option value="' . $group->id . '">' . $group->grouplabel . '</option>';
			}
			$mydata['groups'] = $group_option;
			break;
		case 'admininit':
			if (check_ajax_referer('gi-medialibrary-action') ) {
				
				$mydata['subgroups'] = get_independentsubgroups();
				$mydata['subgroupsbysortorder'] = get_independentsubgroups(true);
				
				$groups = $mydb->get_groups(true);
				foreach ($groups as $group)
				{
					if(!empty($group->grouplabel))
						$group_option .= '<option value="' . $group->id . '">' . $group->grouplabel . '</option>';
				}
			}
			$mydata['groups'] = $group_option;
			break;
		case 'groupsubgroups':
			$subgroups = $mydb->get_groupsubgroups($_POST['groupid']);
			foreach ($subgroups as $subgroup)
			{
				$cols = "";
				$rows = $mydb->get_playlisttablecolumnssubgroup($subgroup->subgroupid, true);
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
			if (check_ajax_referer('gi-medialibrary-action') ) {
				$mydata['subgroups'] = get_groupsubgroups($_POST['groupid']);
				$mydata['subgroupsbysortorder'] = get_groupsubgroups($_POST['groupid'], true);
			}
			 
			break;
		default:
	}
	
	die(json_encode($mydata));
}

function giml_get_subgroups() {
	global $mydb;
	$subgroup_option = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		$subgroups = $mydb->get_subgroups();
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
	global $mydb;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		$mydata['playlistcolumns'] = mb_convert_encoding(get_playlistcolumnssubgroup($_POST['subgroupid'], $_POST['sectionid']), "UTF-8");
	}
	
	die(json_encode($mydata));
}

function giml_get_playlistcombosections() {
	global $mydb;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		$mydata['playlistsection'] = get_playlistcombosections($_POST['comboitemid'], null, ($_POST['sortbysortorder']==="true")?true:false, $_POST['subgroupid']);
	}
	die(json_encode($mydata));
}

function giml_get_playlistcombosectioncolumns() {
	global $mydb;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		$mydata['playlistsectioncolumn'] = get_playlistsectioncolumnssubgroup($_POST['subgroupid'], $_POST['comboitemid']);
	}
	die(json_encode($mydata));
}

function giml_get_playlistdata() {
	global $mydb;
	
	$mydata = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		$subgroupid = $_POST['subgroupid'];
		$tmp = "";
		
		$results = $mydb->get_subgroup($subgroupid);
		$mydata['subgroupdownloadlabel'] = (string)$results[0]->subgroupdownloadlabel;
		$mydata['subgroupdownloadlink'] = (string)$results[0]->subgroupdownloadlink;
		$mydata['subgroupdownloadcss'] = (string)$results[0]->subgroupdownloadcss;
		$mydata['subgroupshowfilter'] = (string)$results[0]->subgroupshowfilter;
		$mydata['subgroupshowcombo'] = (string)$results[0]->subgroupshowcombo;
		
		$results = $mydb->get_playlisttablecolumnssubgroup($subgroupid);
		$mydata['playlisttablecss'] = (string)$results[0]->playlisttablecss;
		$mydata['playlisttablecolumn'] = (string)get_playlisttablecolumnssubgroup($subgroupid, true);
		$results = $mydb->get_playlistcomboitemssubgroup($subgroupid);
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
	global $mydb;
	global $textareafields;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
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
							
						if ($mydb->checksectioncolumnscreated($_POST["playlistsectionid".$i]))
							continue;
					}
					
					$data = array();
					
					if($table === 'playlistcolumn'){
						$rowid = $mydb->get_playlistnextrowid();
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
							$mydb->insert($table, $data);
						
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
						$mydb->insert($table, $data, $_POST['subgroupid']);
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
	global $mydb;
	global $textareafields;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		$table = $_POST['table'];
		switch($table) {
			case 'playlisttable': case 'playlistcombo':
				foreach ($_POST['fields'] as $field) {
					$data[$field] = trim(sanitize_text_field($_POST[$field]));
				}
				if (count($data)>0)
					$mydb->update($table, $data, array('subgroupid'=>$_POST['subgroupid']));
				break;
			case 'playlistsectioncolumn': case 'playlistcolumn':
				for ($i=1; $i<=$_POST['rows']; $i++) {
					$data = array();
					if($table === 'playlistcolumn'){
						if(empty($_POST["playlistcolumnsectionid".$i]))
							continue;

						$data['playlistcolumnsectionid'] = $_POST["playlistcolumnsectionid".$i];
						$sectionid1 = $_POST["playlistcolumnsectionid".$i];
						$mydb->delete($table, $_POST['ids']);
						
						$rowid = $mydb->get_playlistnextrowid();
					}else{
						$oldsectionids = explode(",", $_POST['ids']);

						if(empty($_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]]))
							continue;
						
						if ($_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]] != $oldsectionids[$i-1]) {
							if ($mydb->checksectioncolumnscreated($_POST["playlistsectionid".$i.'_'.$oldsectionids[$i-1]]))
								continue;
						}
						$ids = explode(",", $_POST['ids']);
						$mydb->delete($table, $ids[$i-1]);
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
								//$mydb->update($table, $data, array('playlistcolumnsectionid'=>$sectionid, 'playlisttablecolumnid'=>$tablecolumnid));
							//else
								//$mydb->update($table, $data, array('playlistsectionid'=>$sectionid, 'playlisttablecolumnid'=>$tablecolumnid));
								
							
							$data['playlisttablecolumnid'] = $id;
							$data["createddate"] = date('Y-m-d H:i:s');
							$mydb->insert($table, $data);
							
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
						$mydb->update($table, $data, array('id'=>$id));
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
	global $mydb;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') )
	{
		$table = $_POST['table'];
		switch($table)
		{
			default:
				$result = $mydb->select($table, $_POST['ids'], 1);
			
		}
	}
	die(json_encode($result));
}
function giml_delete() {
	global $mydb;
	
	$result = "";
	if ( !empty($_POST) && check_ajax_referer('gi-medialibrary-action') ) {
		$table = $_POST['table'];
		switch($table) {
			default:
				$mydb->delete($table, $_POST['ids']);
				
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
	global $mydb;
	
	$results = $mydb->get_playlisttablecolumnssubgroup($subgroupid, $sortbysortorder);
	$tmp = "";
	foreach ($results as $data)
	{
		if(!empty($data->playlisttablecolumnlabel))
			$tmp .= '<option value="' . $data->id . '">' . $data->playlisttablecolumnlabel . '</option>';
	}
	return $tmp;
}
function get_playlistcomboitemssubgroup($subgroupid, $selectedid=null, $defaultselected=false, $sortbysortorder=false) {
	global $mydb;
	
	$results = $mydb->get_playlistcomboitemssubgroup($subgroupid, $sortbysortorder);
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
	global $mydb;
	
	$id = $mydb->get_playlistcomboitemssubgroupfirstid($subgroupid);
	
	return $id;
}
function get_playlistcombosectionssubgroup($subgroupid, $selectedid=null, $sortbysortorder=false) {
	global $mydb;
	
	$results = $mydb->get_playlistcombosectionssubgroup($subgroupid, $sortbysortorder);
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
	global $mydb;
	
	$results = $mydb->get_playlistcombosections($comboitemid, $sortbysortorder, 0, $subgroupid);
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
	global $mydb;
	
	$results = $mydb->get_independentplaylistsectionssubgroup($subgroupid, $sortbysortorder);
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
	global $mydb;
	
	$results = $mydb->get_playlistsectionssubgroup($subgroupid, $sortbysortorder);
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
	global $mydb;
	
	$results = $mydb->get_playlistcolumnssubgroup($subgroupid, $sectionid);
	$tmp = $mydb->get_playlisttablecolumnssubgroup($subgroupid);
	$totalcols = $mydb->get_numrows();
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
		$columntext = (strlen($data->playlistcolumntext)>30)?substr($data->playlistcolumntext,0,29).'...':$data->playlistcolumntext;
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
	global $mydb;
	
	$results = $mydb->get_playlistsectioncolumnssubgroup($subgroupid, $comboitemid);
	$tmp = $mydb->get_playlisttablecolumnssubgroup($subgroupid);
	$totalcols = $mydb->get_numrows();
	$col = 1;
	$option = "";
	$tmp = "";
	$section = "";
	$comboitemlabel = "";
	foreach ($results as $data) {
		if ($section === $data->playlistsectionlabel) {
			if($col > $totalcols) {
				$col = 1;
				$option = substr($option, 0, strlen($option)-4);
				$option .= '</option>';
				$option .= '<option value="' . $data->comboitemid . '_' . $data->playlistsectionid . '">' . $comboitemlabel . $section . ' > ';
			}
			$option .= $data->playlistsectiontablecolumntext . ' :: ';
		}else{
			if ($option !== "") {
				$option = substr($option, 0, strlen($option)-4);
				$option .= '</option>';
				$col = 1;
			}
			$section = $data->playlistsectionlabel;
			
			//if ($comboitemid > 0)
			//	$comboitemlabel = (!empty($data->playlistcomboitemlabel))?$data->playlistcomboitemlabel . " > ":"";
				
			$option .= '<option value="' . $data->comboitemid . '_' . $data->playlistsectionid . '">' . $comboitemlabel . $section . ' > ' . $data->playlistsectiontablecolumntext . ' :: ';
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
	global $mydb;
	
	$results = $mydb->get_playlistplaylistsectionssubgroup($subgroupid);
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
	global $mydb;
	
	$results = $mydb->get_playlistcolumnsectionssubgroup($subgroupid);
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