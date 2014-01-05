<?php
global $giml_db;

if (isset($_POST)) {
	$listid = 0;
	$filterid = 0;
	$js = "";
	$id = intval($_POST['subgroupid']);
	//$subgroup = json_decode(stripslashes($_POST['subgroup']));
	$subgroup = $giml_db->get_subgroup($id);
	$subgroup = $subgroup[0];
	$data = $giml_db->get_playlistdata($id);
	
	if (isset($_POST['searchid']))
		$listid = intval($_POST['searchid']);
		
	if (isset($_POST['filterby']))
		$filterid = intval($_POST['filterby']);
	
	$js['playlistcombolabel'] = stripslashes($data->playlistcombolabel);
	$js['playlistcombodirection'] = stripslashes($data->playlistcombodirection);
	$js['playlistcombocss'] = stripslashes($data->playlistcombocss);
	$js['subgrouplabel'] = stripslashes($subgroup->subgrouplabel);
	$js['subgroupdescriptionvisible'] = (is_null($subgroup->subgroupdescription)||$subgroup->subgroupdescription==="")?"display:none":"";
	$js['subgroupdescription'] = $subgroup->subgroupdescription;
	if(empty($subgroup->grouprightlabel) && empty($subgroup->groupleftlabel)) {
		$js['grouprightlabel'] = stripslashes($subgroup->subgrouprightlabel);
		$js['groupleftlabel'] = stripslashes($subgroup->subgroupleftlabel);
		$js['subgrouprightlabel'] = "";
		$js['subgroupleftlabel'] = "";
	}else{
		$js['grouprightlabel'] = stripslashes($subgroup->grouprightlabel);
		$js['groupleftlabel'] = stripslashes($subgroup->groupleftlabel);
		$js['subgrouprightlabel'] = stripslashes($subgroup->subgrouprightlabel);
		$js['subgroupleftlabel'] = stripslashes($subgroup->subgroupleftlabel);
	}
	$js['subgroupshowcombo'] = (intval($subgroup->subgroupshowcombo)==0)?"display:none":"";
	$js['subgroupdownloadvisible'] = (is_null($subgroup->subgroupdownloadlink)||$subgroup->subgroupdownloadlink==="")?"display:none":"";
	$js['subgroupdownload'] = get_downloadhtml($subgroup->subgroupdownloadlink, stripslashes($subgroup->subgroupdownloadlabel), stripslashes($subgroup->subgroupdownloadcss));
	$js['subgroupshowfilter'] = (intval($subgroup->subgroupshowfilter)==0)?"display:none":"";
	
	
	$js['playlistcomboitemssubgroup'] = "";
	$js['playlistcomboitemdescriptionvisible'] = "";
	$js['playlistcomboitemdescription'] = "";
	$js['playlistcomboitemdownloadvisible'] = "";
	$js['playlistcomboitemdownload'] = "";
	$js['subgroupfilteroptions'] = "";
	$js['playlisttablecss'] = "";
	$js['tableheader'] = "";
	
	if (intval($subgroup->subgroupshowcombo)==1) {
		if($listid>0)
			$data = get_playlistcomboitemssubgroup($id, $listid, false, true);
		else {
			$data = get_playlistcomboitemssubgroup($id, null, true, true);
			$listid = get_playlistcomboitemssubgroupfirstid($id);
		}
		$js['playlistcomboitemssubgroup'] = stripslashes($data);
		//$tpl = str_replace( '[+playlistcomboitemssubgroup+]', stripslashes($data), $tpl );
		
		if ($listid>0) {
			$data = $giml_db->select("playlistcomboitem", $listid, 1);
			$data = $data[0];
		}
		 
		if (is_object($data)) {
			$js['playlistcomboitemdescriptionvisible'] = (is_null($data->playlistcomboitemdescription)||$data->playlistcomboitemdescription==="")?"display:none":"";
			$js['playlistcomboitemdescription'] = $data->playlistcomboitemdescription;
			
			$js['playlistcomboitemdownloadvisible'] = (is_null($data->playlistcomboitemdownloadlink)||$data->playlistcomboitemdownloadlink==="")?"display:none":"";
			$js['playlistcomboitemdownload'] = get_downloadhtml($data->playlistcomboitemdownloadlink, stripslashes($data->playlistcomboitemdownloadlabel), stripslashes($data->playlistcomboitemdownloadcss));
			
			//$tpl = str_replace( '[+playlistcomboitemdownloadvisible+]', (is_null($data->playlistcomboitemdownloadlink)||$data->playlistcomboitemdownloadlink==="")?"display:none":"", $tpl );
			//$tpl = str_replace( '[+playlistcomboitemdownload+]', get_downloadhtml($data->playlistcomboitemdownloadlink, stripslashes($data->playlistcomboitemdownloadlabel), stripslashes($data->playlistcomboitemdownloadcss)), $tpl );
		}
	}		
			
	if(intval($subgroup->subgroupshowfilter)==1) {
			
		if (intval($subgroup->subgroupshowcombo)==0) {
			$data = get_playlistsectionssubgroup($id, $filterid, true);
			$sections = $giml_db->get_playlistsectionssubgroup($id, true, $filterid);
		}else{
			if ($listid > 0) {
				$data = get_playlistcombosections($listid, $filterid, true);
				$sections = $giml_db->get_playlistcombosections($listid, true, $filterid);
			}else{
				$data = get_playlistcombosectionssubgroup($id, $filterid, true);
				$sections = $giml_db->get_playlistcombosectionssubgroup($id, true, $filterid);
			}
		}
		$js['subgroupfilteroptions'] = '<option value="0">None</option>' . stripslashes($data);
		//$tpl = str_replace( '[+subgroupfilteroptions+]', '<option value="">None</option>' . stripslashes($data), $tpl );
	}else{
		if (intval($subgroup->subgroupshowcombo)==0)
			$sections = $giml_db->get_playlistsectionssubgroup($id, true);
		elseif (!empty($listid))
			$sections = $giml_db->get_playlistcombosections($listid, true);
		else
			$sections = $giml_db->get_playlistcombosectionssubgroup($id, true);
	}
	
	
			$result = $giml_db->get_playlisttablecolumnsbycolumn ($id);
			$totalcols = $giml_db->get_numrows();
			$tablecss = "";
			$tableheader = "";
			$html = "";
				
			if ($totalcols > 0) {
				$tmpcols = "";
				foreach ($result as $row) {
					$html .= "<th class=\"".stripslashes($row->playlisttablecolumncss)."\" style=\"direction:{$row->playlisttablecolumndirection}\">".stripslashes($row->playlisttablecolumnlabel)."</th>";
					$tmpcols[] = $row->playlisttablecolumnlabel . "::" . $row->playlisttablecolumndirection;
				}
                                if (has_filter('gilms_student_personal')) {
                                    $html .= "<th></th>";
                                }
				$row = $result[0];
				
				$js['playlisttablecss'] = stripslashes($row->playlisttablecss);
				$js['tableheader'] = $html;
				
				$html = "";
				//print "<pre>" . print_r($sections, true) . "</pre>";return;
				foreach ($sections as $section) {
					if ($section->playlistsectionhide == 0) {
                                            $colspan = $totalcols;
                                            if (has_filter('gilms_student_personal'))
                                                $colspan += 1;
                                                    
                                            $html .= "<tr><th class=\"center " . stripslashes($section->playlistsectioncss) . "\" style=\"direction:{$section->playlistsectiondirection}\" colspan=\"{$colspan}\">" . stripslashes($section->playlistsectionlabel) . "&nbsp;" . get_downloadhtml($section->playlistsectiondownloadlink, stripslashes($section->playlistsectiondownloadlabel), stripslashes($section->playlistsectiondownloadcss)) . "</th></tr>";
                                        }
					$data = $giml_db->get_playlistcolumnsbysection($section->id);
					
					$data = giml_sortplaylist($data, $section->id);
					
					// ccompare with total table columns to be displayed
					$i=1;
					//store playlist columns data in html format
					$tmpvalues = "";
					//store section columns data in html format
					$tmpsectioncolvalues = array();
					$totalrows = 1;
					/*if (!empty($data)) {
						$firstrow = (array)$data[0];
						$totalrows = $firstrow['rowid'];
					}*/
					//print "<pre>" . print_r((array)$data[0], true) . "</pre>";return;
					// loop through playlist columns data and its column name
                                        $dataid = "";
					foreach ($data as $col) {
						if($i > $totalcols) {// || $totalrows != $col->rowid) {
							/*if($totalrows != $col->rowid){
								if(count($tmpvalues) < $totalcols) {
									for ($k=0;$k<$totalcols;$k++) {
										if(!array_key_exists($k, $tmpvalues)) {
											$tmpvalues[$k] = "<td></td>";
										}
									}
								}
							}*/
							ksort($tmpvalues);
							
							//print "<pre>" . print_r($tmpvalues, true) . "</pre>";
							
							$html .= "<tr>" . join("", $tmpvalues);
                                                        if (has_filter('gilms_student_personal')) {
                                                            $html = apply_filters('gilms_student_personal', $html, $dataid);
                                                        }
                                                        $dataid = "";
                                                        $html .= "</tr>";
							$totalrows++;
							$i = 1;
							$tmpvalues="";
						}
						// represent current table column id 
						$j = 0;
                                                $dataid .= $col->id . '::';
                                                            
						//loop through table column names
						foreach($tmpcols  as $tmpcol) {
							list($tmpcol, $coldir) = split("::", $tmpcol);

							//if table column name matches playlist column name
							if($tmpcol === $col->playlisttablecolumnlabel) {
								//get section columns data
								$result = $giml_db->get_playlistsectioncolumnsbysection($section->id, "'".addslashes($tmpcol)."'");
								//print "<pre>" . print_r($result, true) . "</pre>";
								// if section columns is less than table columns to be displayed then skip
								/*if(empty($result)) {print $section->id.",";
									$tmpvalues[$j] = "<td></td>";
									$i++;
									continue 2;
								}*/
								// if section columns are empty
								if(empty($result)) {
									$sectioncolumn = (object)array();
								}else{
									$sectioncolumn = $result[0]; //print "<pre>" . print_r($sectioncolumn, true) . "</pre>";
								}
								// if section column data is empty use playlist column data
								if(empty($sectioncolumn->playlistsectiontablecolumntext)) {
									if(strtolower($col->playlisttablecolumntype) === "audio" && !empty($col->playlistcolumntext)) {
										$audios = split("::", stripslashes($col->playlistcolumntext));
										$audionum = 1;
										$tmpvalues[$j] = "";
										foreach ($audios as $audio) {
											$tmpvalues[$j] .= giml_get_audiolink($audio, $col->id, $audionum);
											//$tmpvalues[$j] .= '<a href="' . get_audiolink(trim($audio)) . '"><img title="Click to listen Audio '.$audionum.'" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
											$audionum++;
										}
										$tmpvalues[$j] = "<td class=\"{$coldir}\">" . $tmpvalues[$j] . "</td>";
                                                                        }elseif(strtolower($col->playlisttablecolumntype) === "video" && !empty($col->playlistcolumntext)) {
										$videos = split("::", stripslashes($col->playlistcolumntext));
										$videonum = 1;
										$tmpvalues[$j] = "";
										foreach ($videos as $video) {
											$tmpvalues[$j] .= giml_get_videolink($video, $col->id, $videonum);
											//$tmpvalues[$j] .= '<a href="' . get_audiolink(trim($audio)) . '"><img title="Click to listen Audio '.$audionum.'" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
											$videonum++;
										}
										$tmpvalues[$j] = "<td class=\"{$coldir}\">" . $tmpvalues[$j] . "</td>";
									}elseif(strtolower($col->playlisttablecolumntype) === "download" && !empty($col->playlistcolumntext)) {
										$downloads = split("::", stripslashes($col->playlistcolumntext));
										$tmpvalues[$j] = "";
										foreach ($downloads as $download) {
											$tmpvalues[$j] .= get_downloadhtml(str_replace(" ", "%20", $download)) . "<br/>";
											//'<a href="http://download.php?id=' . base64_encode(trim($download)) . '&nonce={GIML_NONCE}"><img title="Click to download" src="' . plugins_url( 'images/' . $mediaformats["download"], dirname(__FILE__)) . '"></a>';
										}
										$tmpvalues[$j] = "<td class=\"{$coldir}\">" . $tmpvalues[$j] . "</td>";
									}elseif(strtolower($col->playlisttablecolumntype) === "link" && !empty($col->playlistcolumntext)) {
										$links = split("::", stripslashes($col->playlistcolumntext));
										$val = array();
										foreach ($links as $link) {
											list($url, $label) = explode("||", $link);
											$label = trim($label);
											$url = trim($url);
											$val[] = "<a href=\"$url\" onclick=\"window.open('{$url}','_blank');return false;\">" . ((empty($label))?$url:$label) . "</a>";
										}
										$tmpvalues[$j] = "<td class=\"{$coldir}\">" . implode(" | ", $val) . "</td>";
									}elseif(strtolower($col->playlisttablecolumntype) === "iconiclink" && !empty($col->playlistcolumntext)) {
										$links = split("::", stripslashes($col->playlistcolumntext));
										$val = array();
										$filenum = 1;
										foreach ($links as $link) {
											//list($url, $label) = explode("||", $link);
											//$label = trim($label);
											$url = trim($link);
											$val[] = '<a title="Click to view File '.$filenum.'" href="'.$url.'" onclick="window.open(\''.$url.'\',\'GIView\',\'width=650,height=550,location=0,menubar=0,status=0,titlebar=0,toolbar=0\');return false;">' . get_fileicon($url) . '</a>';
											$filenum++;
										}
										$tmpvalues[$j] = "<td class=\"{$coldir}\">" . implode(" | ", $val) . "</td>";
									}else
										$tmpvalues[$j] = "<td class=\"{$coldir}\">" . stripslashes($col->playlistcolumntext) . "</td>";
								}else{
								// section columns data is available for displaying
									// avoid repetition of displaying section column data in next row
									if(isset($tmpsectioncolvalues[$j])) {
										if ($tmpsectioncolvalues[$j] !== $sectioncolumn->playlistsectiontablecolumntext) {
											$tmpsectioncolvalues[$j] = $sectioncolumn->playlistsectiontablecolumntext;
										}else{
											$tmpvalues[$j] = "";
											$i++;
											continue 2;
										}
									}else{
										$tmpsectioncolvalues[$j] = $sectioncolumn->playlistsectiontablecolumntext;
									}
									//if ($tmpsectioncolvalues[$j] !== $sectioncolumn->playlistsectiontablecolumntext) {
									//	$tmpsectioncolvalues[$j] = $sectioncolumn->playlistsectiontablecolumntext;
											
										if(strtolower($col->playlisttablecolumntype) === "audio" && !empty($sectioncolumn->playlistsectiontablecolumntext)) {
											$audios = split("::", stripslashes($sectioncolumn->playlistsectiontablecolumntext));
											$audionum = 1;
											$tmpvalues[$j] = "";
											foreach ($audios as $audio) {
												$tmpvalues[$j] .= giml_get_audiolink($audio, $col->id, $audionum);
												//$tmpvalues[$j] .= '<a href="' . get_audiolink(trim($audio)) . '"><img title="Click to listen Audio ' . $audionum . '" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
												$audionum++;
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . $tmpvalues[$j] . "</td>";
                                                                                }elseif(strtolower($col->playlisttablecolumntype) === "video" && !empty($sectioncolumn->playlistsectiontablecolumntext)) {
											$videos = split("::", stripslashes($sectioncolumn->playlistsectiontablecolumntext));
											$videonum = 1;
											$tmpvalues[$j] = "";
											foreach ($videos as $video) {
												$tmpvalues[$j] .= giml_get_videolink($video, $col->id, $videonum);
												//$tmpvalues[$j] .= '<a href="' . get_audiolink(trim($audio)) . '"><img title="Click to listen Audio ' . $audionum . '" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
												$videonum++;
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . $tmpvalues[$j] . "</td>";
										}elseif(strtolower($col->playlisttablecolumntype) === "download" && !empty($sectioncolumn->playlistsectiontablecolumntext)) {
											$downloads = split("::", stripslashes($sectioncolumn->playlistsectiontablecolumntext));
											$tmpvalues[$j] = "";
											foreach ($downloads as $download) {
												$tmpvalues[$j] .= get_downloadhtml(str_replace(" ", "%20", $download)) . "<br/>";
												//$tmpvalues[$j] .= '<a href="http://download.php?id=' . base64_encode(trim($download)) . '&nonce={GIML_NONCE}"><img title="Click to download" src="' . plugins_url( 'images/' . $mediaformats["download"], dirname(__FILE__)) . '"></a>';
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . $tmpvalues[$j] . "</td>";
										}elseif(strtolower($col->playlisttablecolumntype) === "link" && !empty($col->playlistsectiontablecolumntext)) {
											$links = split("::", stripslashes($col->playlistsectiontablecolumntext));
											$val = array();
											foreach ($links as $link) {
												list($url, $label) = explode("||", $link);
												$label = trim($label);
												$url = trim($url);
												$val[] = "<a href=\"$url\" onclick=\"window.open('{$url}','_blank');return false;\">" . ((empty($label))?$url:$label) . "</a>";
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . implode(" | ", $val) . "</td>";
										}elseif(strtolower($col->playlisttablecolumntype) === "iconiclink" && !empty($col->playlistsectiontablecolumntext)) {
											$links = split("::", stripslashes($col->playlistsectiontablecolumntext));
											$val = array();
											$filenum = 1;
											foreach ($links as $link) {
												//list($url, $label) = explode("||", $link);
												//$label = trim($label);
												$url = trim($link);
												$val[] = '<a title="Click to view File '.$filenum.'" href="'.$url.'" onclick="window.open(\''.$url.'\',\'GIView\',\'width=650,height=550,location=0,menubar=0,status=0,titlebar=0,toolbar=0\');return false;">' . get_fileicon($url) . '</a>';
												$filenum++;
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . implode(" | ", $val) . "</td>";
										}else{
											$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . stripslashes($sectioncolumn->playlistsectiontablecolumntext) . "</td>";
										}
									//}else
									//	$tmpvalues[$j] = "";
								}
								$i++;
								//one playlist column has been printed in table column, now skip the table column and get next playlist column
								continue 2;
								//print "<pre>" . print_r($result, true) . "</pre>";
							}
							$j++;
						}
						$i++;
					}
				
					if (is_array($tmpvalues)) {
						// if section columns is less than table columns then fill up them
						if(count($tmpvalues) < $totalcols) {
							for ($j=0; $j<$totalcols; $j++) {
								if(!isset($tmpvalues[$j])) {
									$tmpvalues[$j] = "<td></td>";
								}
							}
						}
						ksort($tmpvalues);//print "<pre>" . print_r($tmpvalues, true) . "</pre>";
						$html .= "<tr>" . join("", $tmpvalues);
                                                if (has_filter('gilms_student_personal')) {
                                                    $html = apply_filters('gilms_student_personal', $html, $dataid);
                                                }
                                                $dataid = "";
                                                $html .= "</tr>";
						$html = str_replace("[+rowspan+]", $totalrows, $html);
					}
				}
			}
			$js['tablerows'] = $html;
			die(json_encode((object)$js));
	
}

?>