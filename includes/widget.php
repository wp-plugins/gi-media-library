<?php
add_action('widgets_init', 'GIMediaLibraryWidget::register_me');

class GIMediaLibraryWidget extends WP_Widget {
	public $myid;
	
	public function __construct() {
		parent::__construct(
	 		__CLASS__, // Base ID
			'GI-Media Library', // Name
			array( 'description' => 'Display the widget with media library selection on sidebar.', 
				'classname' => __CLASS__ )  
			// Args
		);
	}
	
	static function register_me() {
		if (!is_admin()) {
			if (!wp_script_is('jquery', 'queue')) wp_enqueue_script( 'jquery' );
			if (!wp_script_is('jquery-ui-accordion', 'queue')) wp_enqueue_script( 'jquery-ui-accordion' );
			if (!wp_style_is('jquery-ui-css','queue')) {
				if (!wp_style_is('jquery-ui-css','registered')) {
					wp_register_style( 'jquery-ui-css',  plugins_url( 'css/jquery-ui.css', dirname(__FILE__) ) );//'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
				}
				wp_enqueue_style( 'jquery-ui-css' );
			}
                        if (!wp_style_is('giml-widget','queue')) {
                            if (!wp_style_is('giml-widget','registered')) {
                                    wp_register_style( 'giml-widget', plugins_url( 'css/widget.css', dirname(__FILE__) ) );
                            }
                            wp_enqueue_style( 'giml-widget');
                        }
		}
		register_widget(__CLASS__);
	}
	
	function widget($args, $instance) {
		global $post;
		global $giml_db;
		
		$js = "";
		
		$currentsubgroup = "";
		
		if(isset($_GET['giml-id']))
			$currentsubgroup = $_GET['giml-id'];
			
		$pattern = "\[(\[?)(gi\_medialibrary)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)";
		//$pattern = get_shortcode_regex();
		if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
        && array_key_exists( 2, $matches )
        && in_array( 'gi_medialibrary', $matches[2] ) )
		{
			//$tpl = file_get_contents( plugin_dir_path(__FILE__) . "../tpl/widget.tpl");
			$html = "<script>
						jQuery(function($) {
							$( \"div#giml-widget-accordion\" ).accordion({icons: false, collapsible: true, active: false});
							$( \"div#giml-widget-accordion\" ).find('div.ui-accordion-content').css('height','');
							$(\"div#giml-widget-accordion\").attr('class','');
							$(\"div#giml-widget-accordion h6\").attr('class','');
							$(\"div#giml-menu\").attr('class','ui-accordion-content1');

							$.browseSubgroup = function(id) {
								$('div#giml-menu').find('ul li.list-selected').attr('class','unselected')
										.find('span.selected').removeClass('selected');
								$('span#giml-subgroup'+id).addClass('selected');
								$('span#giml-subgroup'+id).parent().attr('class','list-selected');
								
								$('select#searchtype, select#filterby').unbind(\"change\");
								$('select#searchtype, select#filterby').change(function(){
									$.changeSelection(id);
								});
								$('select#searchtype, select#filterby').attr('disabled','disabled');
								$('div#giml_loader').html('<p align=\"center\"><img src=\"".plugins_url('js/ajax-loader.gif', dirname(__FILE__))."\">&nbsp;Loading . . .</p>');
								var data = {action: 'giml_change_search',
									_ajax_nonce: '".GIML_NONCE."',
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
									$('select#searchtype option').remove();
									$('select#searchtype').append(response['playlistcomboitemssubgroup']);
									$('table#playList').removeClass();
									$('table#playList').addClass(response['playlisttablecss']);
									$('tr#playlistHeader').html(response['tableheader']);
									$('tbody#playlistBody').html(response['tablerows']);
									$('div#giml_loader').html('');
									$('select#searchtype, select#filterby').removeAttr('disabled');
									
									$('label[for=\"searchtype\"]').html(response['playlistcombolabel']);
									$('select#searchtype').attr('dir', response['playlistcombodirection']);
									$('select#searchtype').attr('class', 'arabicName ' + response['playlistcombocss']);
									$('span#groupleftlabel').html(response['groupleftlabel'] + '<span id=\"grouprightlabel\"></span>');
									$('span#grouprightlabel').html(response['grouprightlabel']);
									$('span#subgroupleftlabel').html(response['subgroupleftlabel'] + '<span id=\"subgrouprightlabel\"></span>');
									$('span#subgrouprightlabel').html(response['subgrouprightlabel']);
									$('span#spansubgroupsearch').attr('style',response['subgroupshowcombo']);
									$('div#subgroupdownload').attr('style',response['subgroupdownloadvisible']);
									$('div#subgroupdownload').html(response['subgroupdownload']);
									$('span#spansubgroupfilter').attr('style',response['subgroupshowfilter']);
								},'json');
								return false;
							};
						});
					</script>
					<div id=\"giml-widget-accordion\">";
			$total = count($matches[0]);
			$i=1;
			
			$accordion_index = 0;
			$j = 0;
			
			foreach ($matches[0] as $shortcode) {
				preg_match('/[ ]+id *= *["\']?([0-9]+)["\']+/i', $shortcode, $match);
				$id = $match[1];
				preg_match('/default *= *["\']?([0-1]+)["\']+/i', $shortcode, $match);
				$default = 0;
				if(!empty($match)) {
					if($match[1]==1)
						$default = $id;
				}
				
				if(!empty($id)) {
					$subgroup = $giml_db->get_subgroup($id);
					
					foreach ($subgroup as $data) {
							
						if (intval($data->groupid) > 0) {
                                                    $subgroups = $giml_db->get_group_subgroups($data->groupid);
                                                    if (count($subgroups)>1 || $total > 1) {
                                                        $html .= "<h6><span class=\"{$data->groupcss}\" style=\"direction:{$data->groupdirection};\">{$data->grouplabel}</span></h6><div id=\"giml-menu\"><ul>";
							foreach ($subgroups as $data1) {
								$style1 = ""; $style2 = "";
								if($currentsubgroup == $data1->id) {
									$accordion_index = $j;
									$style1 = "list-selected";
									$style2 = "selected";
								}elseif(empty($currentsubgroup) && $default == $data1->id){
									$accordion_index = $j;
									$style1 = "list-selected";
									$style2 = "selected";
								}else{
									$style1 = "unselected";
								}
								$query = add_query_arg('giml-id', $data1->id, get_permalink($post->ID));
								$html .= "<li class=\"{$style1}\"><span id=\"giml-subgroup{$data1->id}\" class=\"{$style2} {$data1->subgroupcss}\" style=\"direction:{$data1->subgroupdirection}\"><a href=\"{$query}\" onclick=\"return jQuery.browseSubgroup({$data1->id});\">{$data1->subgrouplabel}</a></span></li>";
								
							}
							if($i==$total)
								$html .= '<li class="bottom"></li>';
							
							$html .= "</ul></div>";
							
							$j++;
                                                    }
							//print "$id group avail";
						}else{
							if(count($matches[0])==1) {
/*								$js .= "<script>
											jQuery(function($) {
												$('div[id^=\"sidebar\"]').hide();
												$('div#entries,div.entry-full').width('958');
												$('div#entries').css('float', 'none');
												$('.entry .border').css('background','url(\"". get_bloginfo('template_directory') . "/images/border-bg-entryfullwidth.png\") no-repeat scroll 0 0 transparent');
												$('div.entry-content').css('background-image','url(\"". get_bloginfo('template_directory') . "/images/entry-content-bg-entryfullwidth.png\")');
												$('div#mediaList2,div#titles,div.titles').css('width',$('div.row').width());
												$('div.Head,div.Alhuda').css('width',$('div.row').width()+3);
												$('div#mediaList2 .Head div').css('background','url(\"". plugins_url( 'css/style-images/mdlist_left3-full.gif', dirname(__FILE__) ) . "\") no-repeat scroll left bottom transparent');
											});
										</script>";*/
							}
							//print "no group";
						}
							
					}
				}
				$i++;
			}
			$js .= "
			<script>
				jQuery(function($) {
							$( \"div#giml-widget-accordion\" ).accordion(\"option\", \"active\", {$accordion_index});
							$( \"div#giml-widget-accordion\" ).accordion(\"option\", \"collapsible\", false);
				});
			</script>";
			//$tpl = str_replace('[+contents+]', $html, $tpl);
			$html .= "</div>";
			print $html . $js;
			//print($post->ID);
			//print($pattern);
			//print_r($matches);
			//foreach ($matches[3] as $attr) {
			//	print ($attr . "<br>");
			//}
			//print "shortcode used";
		}else{
			//print "no shortcode";
		}
	}
}


?>