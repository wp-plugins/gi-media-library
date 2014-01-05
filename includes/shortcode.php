<?php

add_shortcode( 'gi_medialibrary', 'giml_shortcode' );
add_action('wp_ajax_giml_change_search', 'giml_change_search');
add_action('wp_ajax_nopriv_giml_change_search', 'giml_change_search');


function giml_set_default_options() {
	return array(
		'default'			=> 0,
		'type'				=> 'group',						// Values: group, resource
		'id'				=> '',							// Values: Integer
	);
}

function giml_shortcode( $attr ) {
	if (!is_admin())
	{
		if (!wp_style_is('gi-style','queue')) {
			if (!wp_style_is('gi-style','registered')) {
				wp_register_style( 'gi-style', plugins_url( 'css/gi.css', dirname(__FILE__) ) );
			}
			wp_enqueue_style( 'gi-style');
		}
		if (!wp_style_is('giml-style','queue')) {
			if (!wp_style_is('giml-style','registered')) {
				wp_register_style( 'giml-style', plugins_url( 'css/giml.css', dirname(__FILE__) ) );
			}
			wp_enqueue_style( 'giml-style');
		}
                
		$settings = shortcode_atts( giml_set_default_options(), $attr );
	
		$display = giml_get_media( $settings );
		print $display;
                return;
		//return $display;	//returns formatted output with autop and linebreak tags
	}
}

function giml_mypage_title( $old_title, $sep, $sep_location ) {
	return "TEST";
}
function giml_get_media( $settings ) {
	global $giml_db;
	global $mediaformats;
	global $post;
	
	if (intval($settings['default']) != 1)
		return;
		
	if (isset($_GET['giml-id']))
		$id = intval($_GET['giml-id']);
	else
		$id = intval($settings['id']);
		
	$listid = 0;
	$filterid = 0;
	if (isset($_GET['giml-listid']))
		$listid = intval($_GET['giml-listid']);
		
	if (isset($_GET['giml-filterid']))
		$filterid = intval($_GET['giml-filterid']);
	
	$sections = "";
	
	//$fields = explode( ",", $settings['columns'] );
	

				$subgroup = $giml_db->get_subgroup($id);
				$subgroup = $subgroup[0];
				$data = $giml_db->get_playlistdata($id);
				
				$tpl = file_get_contents( plugin_dir_path(__FILE__) . "../tpl/playlist.tpl");
				//wp_title("<b>{$subgroup->subgrouplabel}</b>");
				$tpl = str_replace( '[+playlistcombolabel+]', stripslashes($data->playlistcombolabel), $tpl );
				$tpl = str_replace( '[+playlistcombodirection+]', stripslashes($data->playlistcombodirection), $tpl );
				$tpl = str_replace( '[+playlistcombocss+]', stripslashes($data->playlistcombocss), $tpl );
				$tpl = str_replace( '[+subgrouplabel+]', stripslashes($subgroup->subgrouplabel), $tpl );
				$tpl = str_replace( '[+subgroupdescriptionvisible+]', (is_null($subgroup->subgroupdescription)||$subgroup->subgroupdescription==="")?"display:none":"", $tpl );
				$tpl = str_replace( '[+subgroupdescription+]', $subgroup->subgroupdescription, $tpl );
						
				if(empty($subgroup->grouprightlabel) && empty($subgroup->groupleftlabel)) {
					$tpl = str_replace( '[+grouprightlabel+]', stripslashes($subgroup->subgrouprightlabel), $tpl );
					$tpl = str_replace( '[+groupleftlabel+]', stripslashes($subgroup->subgroupleftlabel), $tpl );
				}else{
					$tpl = str_replace( '[+grouprightlabel+]', stripslashes($subgroup->grouprightlabel), $tpl );
					$tpl = str_replace( '[+groupleftlabel+]', stripslashes($subgroup->groupleftlabel), $tpl );
					$tpl = str_replace( '[+subgrouprightlabel+]', stripslashes($subgroup->subgrouprightlabel), $tpl );
					$tpl = str_replace( '[+subgroupleftlabel+]', stripslashes($subgroup->subgroupleftlabel), $tpl );
				}
				$tpl = str_replace( '[+subgroupshowcombo+]', (intval($subgroup->subgroupshowcombo)==0)?"display:none":"", $tpl );
				$tpl = str_replace( '[+subgroupdownloadvisible+]', (is_null($subgroup->subgroupdownloadlink)||$subgroup->subgroupdownloadlink==="")?"display:none":"", $tpl );
				$tpl = str_replace( '[+subgroupdownload+]', get_downloadhtml($subgroup->subgroupdownloadlink, stripslashes($subgroup->subgroupdownloadlabel), stripslashes($subgroup->subgroupdownloadcss)), $tpl );
				$tpl = str_replace( '[+subgroupshowfilter+]', (intval($subgroup->subgroupshowfilter)==0)?"display:none":"", $tpl );
				
				
				if (intval($subgroup->subgroupshowcombo)==1) {
					if($listid>0)
						$data = get_playlistcomboitemssubgroup($id, $listid, false, true);
					else {
						$data = get_playlistcomboitemssubgroup($id, null, true, true);
						$listid = get_playlistcomboitemssubgroupfirstid($id);
					}
					$tpl = str_replace( '[+playlistcomboitemssubgroup+]', stripslashes($data), $tpl );
					
					if ($listid>0) {
						$data = $giml_db->select("playlistcomboitem", $listid, 1);
						$data = $data[0];
					}
					 
					if (is_object($data)) {
						$tpl = str_replace( '[+playlistcomboitemdescriptionvisible+]', (is_null($data->playlistcomboitemdescription)||$data->playlistcomboitemdescription==="")?"display:none":"", $tpl );
						$tpl = str_replace( '[+playlistcomboitemdescription+]', $data->playlistcomboitemdescription, $tpl );
						
						$tpl = str_replace( '[+playlistcomboitemdownloadvisible+]', (is_null($data->playlistcomboitemdownloadlink)||$data->playlistcomboitemdownloadlink==="")?"display:none":"", $tpl );
						$tpl = str_replace( '[+playlistcomboitemdownload+]', get_downloadhtml($data->playlistcomboitemdownloadlink, stripslashes($data->playlistcomboitemdownloadlabel), stripslashes($data->playlistcomboitemdownloadcss)), $tpl );
					}
					
				}
				
				if(intval($subgroup->subgroupshowfilter)==1) {
					$query = add_query_arg('giml-id', $id, get_permalink($post->ID));
					if(!empty($listid))
						$tpl = str_replace( '[+playlistfilteritemredirect+]', $query . "&giml-listid=" . $listid, $tpl );
					else
						$tpl = str_replace( '[+playlistfilteritemredirect+]', $query, $tpl );
						
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
					$tpl = str_replace( '[+subgroupfilteroptions+]', '<option value="0">None</option>' . stripslashes($data), $tpl );
				}else{
					if (intval($subgroup->subgroupshowcombo)==0)
						$sections = $giml_db->get_playlistsectionssubgroup($id, true);
					elseif (!empty($listid))
						$sections = $giml_db->get_playlistcombosections($listid, true);
					else
						$sections = $giml_db->get_playlistcombosectionssubgroup($id, true);
				}
				
				$js = "
				<script>
					jQuery(function($) {
						$.changeSelection = function(id) {
							$('select#searchtype, select#filterby').attr('disabled','disabled');
							$('div#giml_loader').html('<p align=\"center\"><img src=\"".plugins_url('js/ajax-loader.gif', dirname(__FILE__))."\">&nbsp;Loading . . .</p>');
							var data = {action: 'giml_change_search',
								_ajax_nonce: '".GIML_NONCE."',
								searchid: $('select#searchtype').val(),
								filterby: $('select#filterby').val(),
								subgroupid: id};
							
							$.post('" . admin_url('admin-ajax.php') . "', data, function(response){
								$('div#subgroupdescription').css('display', response['subgroupdescriptionvisible']);
								$('div#subgroupdescription').html(response['subgroupdescription']);
								$('div#giml_playlistcomboitemdescription').css('display', response['playlistcomboitemdescriptionvisible']);
								$('div#giml_playlistcomboitemdescription').html(response['playlistcomboitemdescription']);
								$('div#giml_playlistcomboitemdownload').css('display', response['playlistcomboitemdownloadvisible']);
								$('div#giml_playlistcomboitemdownload').html(response['playlistcomboitemdownload']);
								$('select#filterby option').remove();
								$('select#filterby').append(response['subgroupfilteroptions']);
								$('table#playList').removeClass();
								$('table#playList').addClass(response['playlisttablecss']);
								$('tr#playlistHeader').html(response['tableheader']);
								$('tbody#playlistBody').html(response['tablerows']);
								$('div#giml_loader').html('');
								$('select#searchtype, select#filterby').removeAttr('disabled');
							},'json');
						};
						$('select#searchtype, select#filterby').change(function(){
							$.changeSelection({$id});
						});
					});
				</script>";
				$tpl = str_replace( '[+script+]' , $js, $tpl);
				
				
				$html = "";
				
				$result = $giml_db->get_playlisttablecolumnsbycolumn ($id);
				$totalcols = $giml_db->get_numrows();
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
					$tpl = str_replace( '[+playlisttablecss+]', stripslashes($row->playlisttablecss), $tpl );
					$tpl = str_replace( '[+tableheader+]', $html, $tpl );
					
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
								
								//print "<pre>" . print_r($tmpvalues, true) . "</pre>";//exit;
								
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
												//$tmpvalues[$j] .= '<a href="' . giml_get_audiolink(trim($audio)) . '"><img title="Click to listen Audio '.$audionum.'" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
												$audionum++;
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\">" . $tmpvalues[$j] . "</td>";
                                                                                }elseif(strtolower($col->playlisttablecolumntype) === "video" && !empty($col->playlistcolumntext)) {
                                                                                        $videos = split("::", stripslashes($col->playlistcolumntext));
											$videonum = 1;
											$tmpvalues[$j] = "";
											foreach ($videos as $video) {
												$tmpvalues[$j] .= giml_get_videolink($video, $col->id, $videonum);
												$videonum++;
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\">" . $tmpvalues[$j] . "</td>";
										}elseif(strtolower($col->playlisttablecolumntype) === "download" && !empty($col->playlistcolumntext)) {
											$downloads = split("::", stripslashes($col->playlistcolumntext));
											$tmpvalues[$j] = "";
											foreach ($downloads as $download) {
												$tmpvalues[$j] .= get_downloadhtml(str_replace(" ", "%20", $download)) . "<br/>";
												//'<a href="http://download.php?id=' . base64_encode(trim($download)) . '&nonce='.GIML_NONCE.'"><img title="Click to download" src="' . plugins_url( 'images/' . $mediaformats["download"], dirname(__FILE__)) . '"></a>';
											}
											$tmpvalues[$j] = "<td class=\"{$coldir}\">" . $tmpvalues[$j] . "</td>";
										}elseif(strtolower($col->playlisttablecolumntype) === "link" && !empty($col->playlistcolumntext)) {
											$links = split("::", stripslashes($col->playlistcolumntext));
											$val = array();
											foreach ($links as $link) {
												list($url, $label) = explode("||", $link);
												$label = trim($label);
												$url = trim($url);
												$val[] = "<a href=\"{$url}\" onclick=\"window.open('{$url}','_blank');return false;\">" . ((empty($label))?$url:$label) . "</a>";
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
													//$tmpvalues[$j] .= '<a href="' . giml_get_audiolink(trim($audio)) . '"><img title="Click to listen Audio ' . $audionum . '" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
													$audionum++;
												}
												$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . $tmpvalues[$j] . "</td>";
                                                                                        }elseif(strtolower($col->playlisttablecolumntype) === "video" && !empty($sectioncolumn->playlistsectiontablecolumntext)) {
												$videos = split("::", stripslashes($sectioncolumn->playlistsectiontablecolumntext));
												$videonum = 1;
												$tmpvalues[$j] = "";
												foreach ($videos as $video) {
													$tmpvalues[$j] .= giml_get_videolink($video, $col->id, $videonum);
													//$tmpvalues[$j] .= '<a href="' . giml_get_audiolink(trim($audio)) . '"><img title="Click to listen Audio ' . $audionum . '" src="' . plugins_url( 'images/' . $mediaformats["audio"], dirname(__FILE__)) . '"></a>&nbsp;';
													$videonum++;
												}
												$tmpvalues[$j] = "<td class=\"{$coldir}\" rowspan=\"[+rowspan+]\">" . $tmpvalues[$j] . "</td>";
											}elseif(strtolower($col->playlisttablecolumntype) === "download" && !empty($sectioncolumn->playlistsectiontablecolumntext)) {
												$downloads = split("::", stripslashes($sectioncolumn->playlistsectiontablecolumntext));
												$tmpvalues[$j] = "";
												foreach ($downloads as $download) {
													$tmpvalues[$j] .= get_downloadhtml(str_replace(" ", "%20", $download)) . "<br/>";
													//$tmpvalues[$j] .= '<a href="http://download.php?id=' . base64_encode(trim($download)) . '&nonce='.GIML_NONCE.'"><img title="Click to download" src="' . plugins_url( 'images/' . $mediaformats["download"], dirname(__FILE__)) . '"></a>';
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
				$tpl = str_replace( '[+tablerows+]', $html, $tpl );
				//replace remaining separators with null
				preg_match_all('/\[\+[a-zA-Z]+\+\]/', $tpl, $matches);
				foreach($matches as $separator)
					$tpl = str_replace($separator, "", $tpl);
/*				
				}
				
				$result = $wpdb->get_results( $wpdb->prepare( $sql, $settings['id'] ), ARRAY_A );
				if ($result)
				{
					$combooptions = "";
					$tablerows = "";
					foreach( $result as $row )
					{
						$combooptions .= "<option value=\"{$row['grpcombo_value']}\">{$row['grpcombo_value']}</option>";
						$tablerows .= "<tr class=\"{$row['css']}\">\n";
						
						foreach( $fields as $value )
						{
							if (array_key_exists($value, $row))
							{
								if (strtolower($value) == "mp3")
								{
									$links = unserialize($row[$value]);
									$tablerows .= "<td>";
									if ($links)
									{
										foreach( $links as $link )
											$tablerows .= "<a class=\"mediaPlayer\" href=\"$link\">&nbsp;</a>\n";
									}
									$tablerows .= "</td>";
								}else
									$tablerows .= "<td>$row[$value]</td>\n";
							}
						}
						
						$tablerows .= "</tr>";
					}
				}
				
				$tpl = str_replace( '[+combo_options+]', $combooptions, $tpl );
				$tpl = str_replace( '[+tablerows+]', $tablerows, $tpl );*/
				return $tpl;
/*			}
			return false;
		break;
		case 'resource':
			return false;
		break;
	}
*/
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

function giml_change_search() {
	
	if (isset($_POST) && wp_verify_nonce($_POST['_ajax_nonce'], GIML_NONCE_NAME)) {
		include 'shortcode-ajax.php';	
	}

}

?>