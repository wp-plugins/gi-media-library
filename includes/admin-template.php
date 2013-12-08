<?php 

global $wp_version;
global $nonce;
global $nonce_name;

wp_nonce_field($nonce_name);

?>
<script>
/*
*			Author: Zishan Javaid
*			Glare of Islam
*			http://www.glareofislam.com
*			Email: info@glareofislam.com
*
*/
	var nonce = '<?php echo $nonce ?>';
	jQuery(function($){$("#gi-medialibrary-admintabs").tabs();$.stripslashes=function(a){if(a){a=a.replace(/\\'/g,'\'');a=a.replace(/\\"/g,'"');a=a.replace(/\\0/g,'\0');a=a.replace(/\\\\/g,'\\')}return a};$.showStatusDialog=function(a){var b={showImage:true,show:true,message:''};var c=$.extend({},b,a);if(c.showImage){$('div#statusdialog').find('img').show()}else{$('div#statusdialog').find('img').hide()}if(c.message!=''){$('div#statusdialog').find('span').html(c.message)}if(c.show){$('div#statusdialog').dialog('open');$('#gi-medialibrary-admintabs :input').attr('disabled',true)}else{setTimeout(function(){$('div#statusdialog').dialog('close');$('#gi-medialibrary-admintabs :input').removeAttr('disabled')},1000)}};$.trim=function(a){var a=a.replace(/^\s\s*/,''),ws=/\s/,i=a.length;while(ws.test(a.charAt(--i)));return a.slice(0,i+1)};$.resetAllFields=function(){$('form input#btngroupcancel').click();$('form input#btnsubgroupcancel').click();$('form input#btnplaylisttablecolumncancel').click();$('form input#btnplaylistcomboitemcancel').click();$('form input#btnplaylistsectioncancel').click();$('form input#btnplaylistcancel').click()};var q=10;var r={init:function(a){$.group.settings=$.extend({},$.group.defaults,a)},createRows:function(d,e){var f='				 <div class="col">				  <label for="grouplabel"><font color="#FF0000">*</font>Label:</label><br/>				  <textarea id="grouplabel" class="wp-editor-area" name="grouplabel"></textarea>				 </div>				 <div class="col">				  <label for="grouprightlabel">Right Label:</label><br/>				  <input id="grouprightlabel" name="grouprightlabel" type="text">				 </div>				 <div class="col">				  <label for="groupleftlabel">Left Label:</label><br/>				  <input id="groupleftlabel" name="groupleftlabel" type="text">				 </div>				 <div class="col">				  <label for="groupcss">CSS:</label><br/>				  <input class="small-text" id="groupcss" name="groupcss" type="text">				 </div>				 <div class="col">				  <label for="groupdirection">Direction:</label><br/>				  <select id="groupdirection" name="groupdirection">				   <option value="ltr" selected="selected">LTR</option>				   <option value="rtl">RTL</option>				  </select>				 </div>';var g="";for(var i=1;i<=d;i++){var h=f;$.each($.group.settings.rowElements,function(a,b){var c=new RegExp('"'+b+'"',"gi");h=h.replace(c,'"'+b+i.toString()+'"')});g+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+h+'<div class="clear"></div></div>'}$("div#grouprowentries").html(g);if(e){var i=1;$(e).each(function(){var c=this;$.each($.group.settings.rowElements,function(a,b){$('form #'+b+i.toString()).val($.stripslashes((c[b]==null)?"":c[b]));$('form #'+b+i.toString()).attr('id',b+i.toString()+'_'+c['id'])});i++})}},populateComboBox:function(a){$.group({groupItems:$.stripslashes(a)});$('form select#selectgroup option').remove();$('form select#selectgroup').append($.group.settings.groupItems);$('form select#groupselectgroup option').remove();$('form select#groupselectgroup').append('<option value="0">None</option>'+$.group.settings.groupItems);$('form select#playlistselectgroup option').remove();$('form select#playlistselectgroup').append('<option value="0">None</option>'+$.group.settings.groupItems);$('form select[id^="subgroupgroup"] option').remove();$('form select[id^="subgroupgroup"]').append('<option value="0">None</option>'+$.group.settings.groupItems)}};$.group=function(a){if(r[a]){return r[a].apply(this,Array.prototype.slice.call(arguments,1))}else if(typeof a==='object'||!a){return r.init.apply(this,arguments)}else{$.error('Method "'+a+'" does not exist in group plugin!')}};$.group.defaults={rowElements:['grouplabel','grouprightlabel','groupleftlabel','groupcss','groupdirection'],groupItems:''};$.group.settings={};$.group();var s={settings:{rowElements:['subgroupgroup','subgrouplabel','subgrouprightlabel','subgroupleftlabel','subgroupcss','subgroupdescription','subgroupsortorder','subgroupdirection'],downloadElements:['subgroupdownloadlink','subgroupdownloadlabel','subgroupdownloadcss','subgroupshowfilter','subgroupshowcombo'],subgroupItems:'',subgroupItemsbysortorder:''},init:function(a){$.subgroup.settings=$.extend({},$.subgroup.settings,a)},createRows:function(d,e){var f='					 <div class="col">					  <label for="subgroupgroup">Select Group:</label><br/>					  <select class="postform" id="subgroupgroup" name="subgroupgroup"><option value="0">None</option></select>					 </div>					 <div class="col">					  <label for="subgrouplabel"><font color="#FF0000">*</font>Label:</label><br/>					  <textarea id="subgrouplabel" class="wp-editor-area" name="subgrouplabel"></textarea>					 </div>					 <div class="col">					  <label for="subgrouprightlabel">Right Label:</label><br/>					  <input type="text" id="subgrouprightlabel" name="subgrouprightlabel">					 </div>					 <div class="col">					  <label for="subgroupleftlabel">Left Label:</label><br/>					  <input type="text" id="subgroupleftlabel" name="subgroupleftlabel">					 </div>					 <div class="col">					  <label for="subgroupcss">CSS:</label><br/>					  <input type="text" class="small-text" id="subgroupcss" name="subgroupcss">					 </div>				 	 <div class="col">				  	  <label for="subgroupdescription">Description:</label><br/>				  	  <textarea class="wp-editor-area" id="subgroupdescription" name="subgroupdescription"></textarea>				 	 </div>					 <div class="col">					  <label for="subgroupsortorder">Sort order:</label><br/>					  <input type="text" class="small-text" id="subgroupsortorder" name="subgroupsortorder" value="10">					 </div>					 <div class="col">					  <label for="subgroupdirection">Direction:</label><br/>					  <select id="subgroupdirection" name="subgroupdirection">						 <option value="ltr" selected="selected">LTR</option>						 <option value="rtl">RTL</option>						</select>					 </div>';var g="";for(var i=1;i<=d;i++){var h=f;$.each(mySubgroup.settings.rowElements,function(a,b){var c=new RegExp('"'+b+'"',"gi");h=h.replace(c,'"'+b+i.toString()+'"')});g+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+h+'<div class="clear"></div></div>'}$("div#subgrouprowentries").html(g);$('form select[id^="subgroupgroup"]').append($.group.settings.groupItems);$("div#subgrouprowentries").find('input[id^="subgroupsortorder"]').each(function(a){$(this).val(q*(a+1))});if(e){var i=1;$(e).each(function(){var c=this;$.each(mySubgroup.settings.rowElements,function(a,b){$('form #'+b+i.toString()).val($.stripslashes((c[b]==null)?"":c[b]));$('form #'+b+i.toString()).attr('id',b+i.toString()+'_'+c['id'])});i++})}},populateComboBox:function(a){mySubgroup.settings.subgroupItems=$.stripslashes(a['subgroups']);mySubgroup.settings.subgroupItemsbysortorder=$.stripslashes(a['subgroupsbysortorder']);$('form select#selectsubgroup option').remove();$('form select#selectsubgroup').append(mySubgroup.settings.subgroupItemsbysortorder);$('form select#selectplaylistsubgroup option').remove();$('form select#selectplaylistsubgroup').append('<option value="">Select</option>'+mySubgroup.settings.subgroupItems)}};function subgroup(a){this.init(a);return this}$.extend(subgroup.prototype,s);$.subgroup=function(a){return new subgroup(a)};mySubgroup=$.subgroup();var t={settings:{playlistColumnIDs:'',playlistColumnItems:'',playlistColumnSectionItems:'',playlistColumnRowElements:['playlistcolumncomboitemid','playlistcolumnsectionid','playlistsortorder'],playlistSectionItems:'',sectionColumnItems:'',sectionColumnRowElements:['playlistsectioncolumncomboitemid','playlistsectionid'],sectionRowElements:['playlistsectioncomboitemid','playlistsectionlabel','playlistsectioncss','playlistsectionsortorder','playlistsectiondirection','playlistsectiondownloadlink','playlistsectiondownloadlabel','playlistsectiondownloadcss','playlistsectionhide'],sectionItems:'',comboItemRowElements:['playlistcomboitemlabel','playlistcomboitemsortorder','playlistcomboitemdownloadlink','playlistcomboitemdownloadlabel','playlistcomboitemdownloadcss','playlistcomboitemdescription','playlistcomboitemdefault'],comboElements:['playlistcombolabel','playlistcombocss','playlistcombodirection'],comboItemsbysortorder:'',comboItems:'',tableElements:['playlisttablecss'],tableColumnRowElements:['playlisttablecolumnlabel','playlisttablecolumncss','playlisttablecolumndirection','playlisttablecolumnsortorder','playlisttablecolumntype'],tableColumnItems:'',rowElements:[],playlistItems:''},init:function(a){$.playlist.settings=$.extend({},$.playlist.settings,a)},createSectionColumnRows:function(f,g){var h='				 <div class="col">				  <label for="playlistsectioncolumncomboitemid">Combo Item:</label><br/>				  <select class="postform" id="playlistsectioncolumncomboitemid" name="playlistsectioncolumncomboitemid"></select>				 </div>				 <div class="col">				  <label for="playlistsectionid"><font color="#FF0000">*</font>Section:</label><br/>				  <select class="postform" id="playlistsectionid" name="playlistsectionid"></select>				 </div>				 <div id="playlistsectioncolumnfields"></div>';var j="";for(var i=1;i<=f;i++){var k=h;var l="";$.each(this.settings.sectionColumnRowElements,function(a,b){l=new RegExp('"'+b+'"',"gi");if(b==='playlistsectioncolumncomboitemid')k=k.replace(l,'"'+b+'_'+i.toString()+'"');else k=k.replace(l,'"'+b+i.toString()+'"')});l=new RegExp('playlistsectioncolumnfields',"gi");k=k.replace(l,'playlistsectioncolumnfields'+i.toString());j+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+k+'<div class="clear"></div></div>'}$("div#playlistsectioncolumnrowentries").html(j);this.settings.sectionColumnRowElements='';for(var i=1;i<=f;i++){var m="";var n=['playlistsectioncolumncomboitemid','playlistsectionid'];$('form select#selectplaylisttablecolumn').find('option').each(function(a){m+='						<div class="col">						 <label for="playlistsectiontablecolumntext'+i.toString()+'_'+$(this).val()+'">'+$(this).html()+':</label><br/>						 <textarea id="playlistsectiontablecolumntext'+i.toString()+'_'+$(this).val()+'" class="wp-editor-area" name="playlistsectiontablecolumntext'+i.toString()+'_'+$(this).val()+'"></textarea>						</div>						';n.push('playlistsectiontablecolumntext'+'_'+$(this).val())});if(this.settings.sectionColumnRowElements==''){this.settings.sectionColumnRowElements=n}$('div#playlistsectioncolumnfields'+i.toString()).html(m)}$('form select[id^="playlistsectionid"]').append("<option value=''>Select</option>"+this.settings.sectionItems);$('form select[id^="playlistsectioncolumncomboitemid"]').append("<option value=''>Select</option>"+this.settings.comboItems);$('select[id^="playlistsectioncolumncomboitemid"]').change(function(){var b=this.id.toString().split('_')[1];if($(this).val()==0){$('form select[id^="playlistsectionid'+b+'"] option').remove();$('form select[id^="playlistsectionid'+b+'"]').append('<option value="">Select</option>'+myPlaylist.settings.sectionItems);return}$.showStatusDialog({message:'Loading section list...'});var c={action:'giml_get_playlistcombosections',_ajax_nonce:nonce,comboitemid:$(this).val()};$.post(ajaxurl,c,function(a){$('form select[id^="playlistsectionid'+b+'"] option').remove();$('form select[id^="playlistsectionid'+b+'"]').append('<option value="">Select</option>'+$.stripslashes(a['playlistsection']));$.showStatusDialog({show:false})},'json')});if(g){var i=1;var o=this.settings;$('form select[id^="playlistsectionid"] option').remove();$('form select[id^="playlistsectionid"]').append("<option value=''>Select</option>"+o.sectionItems);$(g).each(function(){var e=this;$.each(o.sectionColumnRowElements,function(b,c){if($('form #'+c.replace('_',i.toString()+'_')).is(':checkbox')){if(parseInt(e[c.split('_')[0]])==1){$('form #'+c.replace('_',i.toString()+'_')).attr('checked','checked')}else{$('form #'+c.replace('_',i.toString()+'_')).removeAttr('checked')}}else{if(c==='playlistsectioncolumncomboitemid'){$('form #'+c+'_'+i.toString()).val(e[c]);$('form select#playlistsectionid'+i.toString()).val("");if(e[c]!=0){var d={action:'giml_get_playlistcombosections',_ajax_nonce:nonce,comboitemid:e[c]};$.ajaxSetup({async:false});$.post(ajaxurl,d,function(a){$('form select#playlistsectionid'+i.toString()+' option').remove();$('form select#playlistsectionid'+i.toString()).append($.stripslashes(a['playlistsection']));$.ajaxSetup({async:true})},'json')}}else if(c==='playlistsectionid'){$('form #'+c+i.toString()).val($.stripslashes((e[c]==null)?"":e[c]));$('form #'+c+i.toString()).attr('id',c+i.toString()+'_'+e[c])}else{$('form #'+c.replace('_',i.toString()+'_')).val($.stripslashes((e[c]==null)?"":e[c]))}}});i++})}},createSectionRows:function(d,e){var f='				 <div class="col">				  <label for="playlistsectioncomboitemid">Combo Item:</label><br/>				  <select class="postform" id="playlistsectioncomboitemid" name="playlistsectioncomboitemid"><option value="0">None</option></select>				 </div>				 <div class="col">				  <label for="playlistsectionlabel"><font color="#FF0000">*</font>Name:</label><br/>				  <textarea class="wp-editor-area" id="playlistsectionlabel" name="playlistsectionlabel"></textarea>				 </div>				 <div class="col">				  <label for="playlistsectioncss">CSS:</label><br/>				  <input type="text" class="small-text" id="playlistsectioncss" name="playlistsectioncss">				 </div>				 <div class="col">				  <label for="playlistsectionsortorder">Sort order:</label><br/>				  <input type="text" class="small-text" id="playlistsectionsortorder" name="playlistsectionsortorder" value="10">				 </div>				 <div class="col">				  <label for="playlistsectiondirection">Direction:</label><br/>				  <select id="playlistsectiondirection" name="playlistsectiondirection">					 <option value="ltr" selected="selected">LTR</option>					 <option value="rtl">RTL</option>					</select>				 </div>				 <div class="col">				  <label for="playlistsectiondownloadlink">Download link:</label><br/>				  <input type="text" class="regular-text" id="playlistsectiondownloadlink" name="playlistsectiondownloadlink">				 </div>				 <div class="col">				  <label for="playlistsectiondownloadlabel">Download label:</label><br/>				  <input type="text" id="playlistsectiondownloadlabel" name="playlistsectiondownloadlabel">				 </div>				 <div class="col">				  <label for="playlistsectiondownloadcss">Download CSS:</label><br/>				  <input type="text" class="small-text" id="playlistsectiondownloadcss" name="playlistsectiondownloadcss">				 </div>				 <div class="col">				  <input type="checkbox" value="1" id="playlistsectionhide" name="playlistsectionhide">				  <label for="playlistsectionhide">Hide</label>				 </div>';var g="";for(var i=1;i<=d;i++){var h=f;$.each(this.settings.sectionRowElements,function(a,b){var c=new RegExp('"'+b+'"',"gi");h=h.replace(c,'"'+b+i.toString()+'"')});g+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+h+'<div class="clear"></div></div>'}$("div#playlistsectionrowentries").html(g);$('form select[id^="playlistsectioncomboitemid"]').append(this.settings.comboItems);$("div#playlistsectionrowentries").find('input[id^="playlistsectionsortorder"]').each(function(a){$(this).val(q*(a+1))});if(e){var i=1;var j=this.settings;$(e).each(function(){var c=this;$.each(j.sectionRowElements,function(a,b){if($('form #'+b+i.toString()).is(':checkbox')){if(parseInt(c[b])==1){$('form #'+b+i.toString()).attr('checked','checked')}else{$('form #'+b+i.toString()).removeAttr('checked')}}else{$('form #'+b+i.toString()).val($.stripslashes((c[b]==null)?"":c[b]))}$('form #'+b+i.toString()).attr('id',b+i.toString()+'_'+c['id'])});i++})}},createComboItemRows:function(d,e){var f='				 <div class="col">				  <label for="playlistcomboitemlabel"><font color="#FF0000">*</font>Item label:</label><br/>				  <input type="text" id="playlistcomboitemlabel" name="playlistcomboitemlabel">				 </div>				 <div class="col">				  <label for="playlistcomboitemdescription">Description:</label><br/>				  <textarea class="wp-editor-area" id="playlistcomboitemdescription" name="playlistcomboitemdescription"></textarea>				 </div>				 <div class="col">				  <label for="playlistcomboitemsortorder">Sort order:</label><br/>				  <input type="text" class="small-text" id="playlistcomboitemsortorder" name="playlistcomboitemsortorder">				 </div>				 <div class="col">				  <label for="playlistcomboitemdownloadlink">Download link:</label><br/>				  <input type="text" class="regular-text" id="playlistcomboitemdownloadlink" name="playlistcomboitemdownloadlink">				 </div>				 <div class="col">				  <label for="playlistcomboitemdownloadlabel">Download label:</label><br/>				  <input type="text" id="playlistcomboitemdownloadlabel" name="playlistcomboitemdownloadlabel">				 </div>				 <div class="col">				  <label for="playlistcomboitemdownloadcss">Download css:</label><br/>				  <input type="text" class="small-text" id="playlistcomboitemdownloadcss" name="playlistcomboitemdownloadcss">				 </div>				 <div class="col">				  <input type="checkbox" value="1" id="playlistcomboitemdefault" name="playlistcomboitemdefault">				  <label for="playlistcomboitemdefault">Default</label>				 </div>';var g="";for(var i=1;i<=d;i++){var h=f;$.each(this.settings.comboItemRowElements,function(a,b){var c=new RegExp('"'+b+'"',"gi");h=h.replace(c,'"'+b+i.toString()+'"')});g+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+h+'<div class="clear"></div></div>'}$("div#playlistcomboitemrowentries").html(g);$("div#playlistcomboitemrowentries").find('input[id^="playlistcomboitemsortorder"]').each(function(a){$(this).val(q*(a+1))});if(e){var i=1;var j=this.settings;$(e).each(function(){var c=this;$.each(j.comboItemRowElements,function(a,b){if($('form #'+b+i.toString()).is(':checkbox')){if(parseInt(c[b])==1){$('form #'+b+i.toString()).attr('checked','checked')}else{$('form #'+b+i.toString()).removeAttr('checked')}}else{$('form #'+b+i.toString()).val($.stripslashes((c[b]==null)?"":c[b]))}$('form #'+b+i.toString()).attr('id',b+i.toString()+'_'+c['id'])});i++})}},createTableColumnRows:function(d,e){var f='				 <div class="col">				  <label for="playlisttablecolumnlabel"><font color="#FF0000">*</font>Column label:</label><br/>				  <input type="text" id="playlisttablecolumnlabel" name="playlisttablecolumnlabel">				 </div>				 <div class="col">				  <label for="playlisttablecolumncss">CSS:</label><br/>				  <input type="text" class="small-text" id="playlisttablecolumncss" name="playlisttablecolumncss">				 </div>				 <div class="col">				  <label for="playlisttablecolumndirection">Direction:</label><br/>				  <select id="playlisttablecolumndirection" name="playlisttablecolumndirection">					 <option value="ltr" selected="selected">LTR</option>					 <option value="rtl">RTL</option>				  </select>				 </div>				 <div class="col">				  <label for="playlisttablecolumnsortorder">Sort order:</label><br/>				  <input type="text" class="small-text" id="playlisttablecolumnsortorder" name="playlisttablecolumnsortorder" value="10">				 </div>				 <div class="col">				  <label for="playlisttablecolumntype">Column type:</label><br/>				  <select id="playlisttablecolumntype" name="playlisttablecolumntype">					 <option value="text" selected="selected">Text</option>					 <option value="link">Link</option>					 <option value="iconiclink">Iconic Link</option>					 <option value="audio">Audio</option>					 <option value="download">Download</option>				  </select>				 </div>				 ';var g="";for(var i=1;i<=d;i++){var h=f;$.each(this.settings.tableColumnRowElements,function(a,b){var c=new RegExp('"'+b+'"',"gi");h=h.replace(c,'"'+b+i.toString()+'"')});g+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+h+'<div class="clear"></div></div>'}$("div#playlisttablecolumnrowentries").html(g);$("div#playlisttablecolumnrowentries").find('input[id^="playlisttablecolumnsortorder"]').each(function(a){$(this).val(q*(a+1))});if(e){var i=1;var j=this.settings;$(e).each(function(){var c=this;$.each(j.tableColumnRowElements,function(a,b){$('form #'+b+i.toString()).val($.stripslashes((c[b]==null)?"":c[b]));$('form #'+b+i.toString()).attr('id',b+i.toString()+'_'+c['id'])});i++})}},createPlaylistColumnRows:function(g,h){var j='				 <div class="col">				  <label for="playlistcolumncomboitemid">Combo Item:</label><br/>				  <select class="postform" id="playlistcolumncomboitemid" name="playlistcolumncomboitemid"></select>				 </div>				 <div class="col">				  <label for="playlistcolumnsectionid"><font color="#FF0000">*</font>Section:</label><br/>				  <select class="postform" id="playlistcolumnsectionid" name="playlistcolumnsectionid"></select>				 </div>				 <div id="playlistcolumnfields"></div>';var k="";for(var i=1;i<=g;i++){var l=j;var m="";$.each(this.settings.playlistColumnRowElements,function(a,b){m=new RegExp('"'+b+'"',"gi");if(b==='playlistcolumncomboitemid')l=l.replace(m,'"'+b+'_'+i.toString()+'"');else l=l.replace(m,'"'+b+i.toString()+'"')});m=new RegExp('playlistcolumnfields',"gi");l=l.replace(m,'playlistcolumnfields'+i.toString());k+='<div class="form-row"><div class="col"><strong>'+i.toString()+'.</strong></div>'+l+'<div class="clear"></div></div>'}$("div#playlistrowentries").html(k);this.settings.playlistColumnRowElements='';for(var i=1;i<=g;i++){var n='							<div class="col">							 <label for="playlistsortorder'+i.toString()+'">Sort order:</label><br/>							 <input type="text" class="small-text" id="playlistsortorder'+i.toString()+'" name="playlistsortorder'+i.toString()+'" value="10">							</div>							';var o=['playlistcolumncomboitemid','playlistcolumnsectionid','playlistsortorder'];$('form select#selectplaylisttablecolumn').find('option').each(function(a){n+='						<div class="col">						 <label for="playlistcolumntext'+i.toString()+'_'+$(this).val()+'">'+$(this).html()+':</label><br/>						 <textarea id="playlistcolumntext'+i.toString()+'_'+$(this).val()+'" class="wp-editor-area" name="playlistcolumntext'+i.toString()+'_'+$(this).val()+'"></textarea>						</div>						';o.push('playlistcolumntext'+'_'+$(this).val())});if(this.settings.playlistColumnRowElements==''){this.settings.playlistColumnRowElements=o}$('div#playlistcolumnfields'+i.toString()).html(n)}$('form select[id^="playlistcolumnsectionid"]').append("<option value=''>Select</option>"+this.settings.sectionItems);$('form select[id^="playlistcolumncomboitemid"]').append("<option value=''>Select</option>"+this.settings.comboItems);$('select[id^="playlistcolumncomboitemid"]').change(function(){var b=this.id.toString().split('_')[1];if($(this).val()==0){$('form select[id^="playlistcolumnsectionid'+b+'"] option').remove();$('form select[id^="playlistcolumnsectionid'+b+'"]').append('<option value="">Select</option>'+myPlaylist.settings.sectionItems);return}$.showStatusDialog({message:'Loading section list...'});var c={action:'giml_get_playlistcombosections',_ajax_nonce:nonce,comboitemid:$(this).val()};$.post(ajaxurl,c,function(a){$('form select[id^="playlistcolumnsectionid'+b+'"] option').remove();$('form select[id^="playlistcolumnsectionid'+b+'"]').append('<option value="">Select</option>'+$.stripslashes(a['playlistsection']));$.showStatusDialog({show:false})},'json')});$("div#playlistrowentries").find('input[id^="playlistsortorder"]').each(function(a){$(this).val(q*(a+1))});if(h){var i=1;var p=this.settings;$('form select[id^="playlistcolumnsectionid"] option').remove();$('form select[id^="playlistcolumnsectionid"]').append("<option value=''>Select</option>"+p.sectionItems);$(h).each(function(){var e=this;var f=e['id'].split(",");$.each(p.playlistColumnRowElements,function(b,c){if($('form #'+c.replace('_',i.toString()+'_')).is(':checkbox')){if(parseInt(e[c.split('_')[0]])==1){$('form #'+c.replace('_',i.toString()+'_')).attr('checked','checked')}else{$('form #'+c.replace('_',i.toString()+'_')).removeAttr('checked')}}else{if(c==='playlistcolumncomboitemid'){$('form #'+c+'_'+i.toString()).val(e[c]);$('form select#playlistcolumnsectionid'+i.toString()).val('');if(e[c]!=0){var d={action:'giml_get_playlistcombosections',_ajax_nonce:nonce,comboitemid:e[c]};$.ajaxSetup({async:false});$.post(ajaxurl,d,function(a){$('form select#playlistcolumnsectionid'+i.toString()+' option').remove();$('form select#playlistcolumnsectionid'+i.toString()).append($.stripslashes(a['playlistsection']));$.ajaxSetup({async:true})},'json')}}else if(c==='playlistcolumnsectionid'){$('form #'+c+i.toString()).val($.stripslashes((e[c]==null)?"":e[c]))}else if(c==='playlistsortorder'){$('form #'+c+i.toString()).val($.stripslashes((e[c]==null)?"10":e[c]))}else{$('form #'+c.replace('_',i.toString()+'_')).val($.stripslashes((e[c]==null)?"":e[c]))}}});i++})}},populateFields:function(a){$('form input#subgroupdownloadlink1').val($.stripslashes(a['subgroupdownloadlink']));$('form input#subgroupdownloadlabel1').val($.stripslashes(a['subgroupdownloadlabel']));$('form input#subgroupdownloadcss1').val($.stripslashes(a['subgroupdownloadcss']));if(a['subgroupshowfilter']==1){$('form input#subgroupshowfilter1').attr('checked','checked')}else{$('form input#subgroupshowfilter1').removeAttr('checked')}if(a['subgroupshowcombo']==1){$('form input#subgroupshowcombo1').attr('checked','checked')}else{$('form input#subgroupshowcombo1').removeAttr('checked')}if(parseInt(a['subgroupshowcombo'])==1){if($('div#playlistcombo').is(":hidden"))$('div#playlistcombo').slideDown()}else{$('div#playlistcombo').slideUp()}$('form input#playlisttablecss').val($.stripslashes(a['playlisttablecss']));this.populateSections(a['playlistsection']);this.populateTableColumnComboBox(a['playlisttablecolumn']);this.populateComboItems(a);this.populateComboMain(a);this.populateSectionColumns(a['playlistsectioncolumn']);this.populatePlaylistColumnSections(a['playlistcolumnsections']);this.populatePlaylistColumns(a['playlistcolumns']);this.populatePlaylistPlaylistSections(a['playlistplaylistsections'])},populatePlaylistPlaylistSections:function(a){this.settings.playlistColumnSectionItems=$.stripslashes(a)},populatePlaylistColumns:function(a){this.settings.playlistColumnItems=$.stripslashes(a);$('form select#selectplaylist option').remove();$('form select#selectplaylist').append(this.settings.playlistColumnItems)},populateSectionColumns:function(a){this.settings.sectionColumnItems=$.stripslashes(a);$('form select#selectplaylistsectioncolumns option').remove();$('form select#selectplaylistsectioncolumns').append(this.settings.sectionColumnItems)},populatePlaylistColumnSections:function(a){this.settings.playlistSectionItems=$.stripslashes(a)},populateSections:function(a){this.settings.sectionItems=$.stripslashes(a);$('form select#selectplaylistsection option').remove();$('form select#selectplaylistsection').append(this.settings.sectionItems);$('form select#playlistselectcomboitem').append('<option value="">Select</option><option value="0">None</option>'+this.settings.comboItems);$('select#playlistselectsection option, select#selectplaylist option').remove()},populateComboItems:function(a){this.settings.comboItems=$.stripslashes(a['playlistcomboitem']);this.settings.comboItemsbysortorder=$.stripslashes(a['playlistcomboitembysortorder']);$('form select#selectplaylistcomboitem option').remove();$('form select#selectplaylistcomboitem').append(this.settings.comboItemsbysortorder);$('form select[id^="playlistsectioncomboitemid"] option').remove();$('form select[id^="playlistsectioncomboitemid"]').append('<option value="0">None</option>'+this.settings.comboItems);$('form select#playlistsectionselectcomboitem option, form select#playlistselectcomboitem option, form select#playlistsectioncolumnselectcomboitem option, form select[id^="playlistsectioncolumncomboitemid"] option, select[id^="playlistcolumncomboitemid"] option').remove();$('form select#playlistsectionselectcomboitem, form select#playlistsectioncolumnselectcomboitem, form select[id^="playlistsectioncolumncomboitemid"], select[id^="playlistcolumncomboitemid"]').append('<option value="0">None</option>'+this.settings.comboItems);$('form select#playlistselectcomboitem').append('<option value="">Select</option><option value="0">None</option>'+this.settings.comboItems);$('select#playlistselectsection option, select#selectplaylist option').remove()},populateComboMain:function(a){$('form input#playlistcombolabel').val($.stripslashes(a['playlistcombolabel']));$('form input#playlistcombocss').val($.stripslashes(a['playlistcombocss']));$('form select#playlistcombodirection').val($.stripslashes(a['playlistcombodirection']))},populateTableColumnComboBox:function(a){this.settings.tableColumnItems=$.stripslashes(a);$('form select#selectplaylisttablecolumn option').remove();$('form select#selectplaylisttablecolumn').append(this.settings.tableColumnItems);$('form input#btnplaylistsectioncolumncancel').click();$('form input#btnplaylistcancel').click();this.createSectionColumnRows(2);this.createPlaylistColumnRows(2);if($.trim(this.settings.tableColumnItems)==""){$('div#playlistsectioncolumnrowentries').html('');$('div#playlistrowentries').html('')}},populateTableColumnsHtml:function(a){var b="";var c=""}};function playlist(a){this.init(a);return this}$.extend(playlist.prototype,t);$.playlist=function(a){return new playlist(a)};myPlaylist=$.playlist();$.group('createRows',2);mySubgroup.createRows(2);myPlaylist.createTableColumnRows(2);myPlaylist.createComboItemRows(2);myPlaylist.createSectionRows(2);myPlaylist.createSectionColumnRows(2);myPlaylist.createPlaylistColumnRows(2);$('form select#selectsubgroup option').remove();$('form select#selectsubgroup').append(mySubgroup.settings.subgroupItemsbysortorder);$('form select#selectplaylistsubgroup option').remove();$('form select#selectplaylistsubgroup').append('<option value="">Select</option>'+mySubgroup.settings.subgroupItems);$('div#statusdialog').dialog({draggable:false,resizable:false,minWidth:false,minHeight:false,width:"auto",autoOpen:false});$(".ui-dialog-titlebar").hide();$("#gi-medialibrary-admintabs").bind('tabsselect',function(a,b){$.resetAllFields();$('form select#groupselectgroup, form select#playlistselectgroup').val(0);$('form select#groupselectgroup, form select#playlistselectgroup').change();$('form select#selectplaylistsubgroup').val("");$('form select#selectplaylistsubgroup').change()});$(window).resize(function(){$("div#statusdialog").dialog("option","position","center")});$.showStatusDialog({message:'Loading...'});var u={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,u,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$.showStatusDialog({show:false})},'json');$('form input#btngroupcancel').hide();$('form input#btngroupcancel').click(function(){$('form select#selectgroup').val(0);$('form select#selectgrouprows').val(2);$('form select#selectgrouprows').change();$('form select#selectgrouprows').removeAttr('disabled');$('form input#btngroupdelete').removeAttr('disabled');$('form input#btngroupadd').val('Add');$('form input#btngroupcancel').hide()});$('form input#btngroupedit').click(function(){if($('form select#selectgroup option:selected').length>0){$('form select#selectgrouprows').val(2);$('form select#selectgrouprows').change();$('form select#selectgrouprows').attr('disabled','disabled');$('form input#btngroupdelete').attr('disabled','disabled');$('form input#btngroupadd').val('Update');$('form input#btngroupcancel').show();var b=$('form select#selectgroup option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var c={action:'giml_group_edit',_ajax_nonce:nonce,groupid:b};$.post(ajaxurl,c,function(a){$.group('createRows',a.length,a);$.showStatusDialog({show:false})},'json')}});$('form input#btngroupadd').click(function(){if($(this).val()==='Update'){var d=[];$('form textarea[id^="grouplabel"]').each(function(a){if($.trim($(this).val()).length==0){d.push(true)}});if(d.length<$("div#grouprowentries").find(".form-row").length){$.showStatusDialog({message:'Updating group(s)...'});var e={action:'giml_group_update',_ajax_nonce:nonce,rows:$("div#grouprowentries").find(".form-row").length,fields:$.group.settings.rowElements};$('div#grouprowentries').find('input,select,textarea').each(function(){e[this.id]=$(this).val()});$.post(ajaxurl,e,function(b){var c={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,c,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$("form input#btngroupcancel").click();$.showStatusDialog({showImage:false,message:'Group(s) updated successfully.',show:false})},'json')})}}else{var d=[];$('form textarea[id^="grouplabel"]').each(function(a){if($.trim($(this).val()).length==0){d.push(true)}});if(d.length<parseInt($('form select#selectgrouprows').val())){$.showStatusDialog({message:'Creating group(s)...'});var e={action:'giml_group_add',_ajax_nonce:nonce,rows:$('form select#selectgrouprows').val(),fields:$.group.settings.rowElements};$('div#grouprowentries').find('input,select,textarea').each(function(){e[this.id]=$(this).val()});$.post(ajaxurl,e,function(b){var c={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,c,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$("form input#btngroupcancel").click();$.showStatusDialog({showImage:false,message:'Group(s) created successfully.',show:false})},'json')})}}});$('form input#btngroupdelete').click(function(){if($('form select#selectgroup option:selected').length>0){var d=$('form select#selectgroup option:selected').map(function(){return $(this).val()}).get().join(',');if(confirm('Are you sure you want to delete selected group(s)?')){$.showStatusDialog({message:'Deleting group(s)...'});var e={action:'giml_group_delete',_ajax_nonce:nonce,groupid:d};$.post(ajaxurl,e,function(b){var c={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,c,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$("form input#btngroupcancel").click();$.showStatusDialog({showImage:false,message:'Group(s) deleted successfully.',show:false})},'json')})}}});$("form select#selectgrouprows").change(function(){$.group('createRows',parseInt($(this).val()))});$('form input#btnsubgroupcancel').hide();$('form select#groupselectgroup, form select#playlistselectgroup').change(function(){$("form input#btnsubgroupcancel").click();if(this.id==="playlistselectgroup")$.clearPlaylist();if($(this).val()==0||$(this).val()===""){$('form select#selectsubgroup option').remove();$('form select#selectsubgroup').append(mySubgroup.settings.subgroupItemsbysortorder);$('form select#selectplaylistsubgroup option').remove();$('form select#selectplaylistsubgroup').append('<option value="">Select</option>'+mySubgroup.settings.subgroupItems);return}$.showStatusDialog({message:'Loading subgroup list...'});var b={action:'giml_get_shortcodedata',datatype:'admingroupsubgroups',_ajax_nonce:nonce,groupid:$(this).val()};$.post(ajaxurl,b,function(a){$('form select#selectsubgroup option').remove();$('form select#selectsubgroup').append($.stripslashes(a['subgroupsbysortorder']));$('form select#selectplaylistsubgroup option').remove();$('form select#selectplaylistsubgroup').append('<option value="">Select</option>'+$.stripslashes(a['subgroups']));$.showStatusDialog({show:false})},'json')});$("form select#selectsubgrouprows").change(function(){mySubgroup.createRows(parseInt($(this).val()))});$('form input#btnsubgroupcancel').click(function(){$('form select#selectsubgroup').val(0);$('form select#selectsubgrouprows').val(2);$('form select#selectsubgrouprows').change();$('form select#selectsubgrouprows').removeAttr('disabled');$('form input#btnsubgroupdelete').removeAttr('disabled');$('form input#btnsubgroupadd').val('Add');$('form input#btnsubgroupcancel').hide()});$('form input#btnsubgroupedit').click(function(){if($('form select#selectsubgroup option:selected').length>0){$('form select#selectsubgrouprows').val(2);$('form select#selectsubgrouprows').change();$('form select#selectsubgrouprows').attr('disabled','disabled');$('form input#btnsubgroupdelete').attr('disabled','disabled');$('form input#btnsubgroupadd').val('Update');$('form input#btnsubgroupcancel').show();var b=$('form select#selectsubgroup option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var c={action:'giml_subgroup_edit',_ajax_nonce:nonce,subgroupid:b};$.post(ajaxurl,c,function(a){mySubgroup.createRows(a.length,a);$.showStatusDialog({show:false})},'json')}});$('form input#btnsubgroupadd').click(function(){if($(this).val()==='Update'){var d=[];$('form textarea[id^="subgrouplabel"]').each(function(a){if($.trim($(this).val()).length==0){d.push(true)}});if(d.length<$("div#subgrouprowentries").find(".form-row").length){$.showStatusDialog({message:'Updating subgroup(s)...'});var e={action:'giml_subgroup_update',_ajax_nonce:nonce,rows:$("div#subgrouprowentries").find(".form-row").length,fields:mySubgroup.settings.rowElements};$('div#subgrouprowentries').find('input,select,textarea').each(function(){e[this.id]=$(this).val()});$.post(ajaxurl,e,function(b){var c={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,c,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$("form input#btnsubgroupcancel").click();$.showStatusDialog({showImage:false,message:'Subgroup(s) updated successfully.',show:false})},'json')})}}else{var d=[];$('form textarea[id^="subgrouplabel"]').each(function(a){if($.trim($(this).val()).length==0){d.push(true)}});if(d.length<parseInt($('form select#selectsubgrouprows').val())){$.showStatusDialog({message:'Creating subgroup(s)...'});var e={action:'giml_subgroup_add',_ajax_nonce:nonce,rows:$('form select#selectsubgrouprows').val(),fields:mySubgroup.settings.rowElements};$('div#subgrouprowentries').find('input,select,textarea').each(function(){e[this.id]=$(this).val()});$.post(ajaxurl,e,function(b){var c={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,c,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$("form input#btnsubgroupcancel").click();$.showStatusDialog({showImage:false,message:'Subgroup(s) created successfully.',show:false})},'json')})}}});$('form input#btnsubgroupdelete').click(function(){if($('form select#selectsubgroup option:selected').length>0){var d=$('form select#selectsubgroup option:selected').map(function(){return $(this).val()}).get().join(',');if(confirm('Are you sure you want to delete selected subgroup(s)?')){$.showStatusDialog({message:'Deleting subgroup(s)...'});var e={action:'giml_subgroup_delete',_ajax_nonce:nonce,subgroupid:d};$.post(ajaxurl,e,function(b){var c={action:'giml_get_shortcodedata',datatype:'admininit',_ajax_nonce:nonce};$.post(ajaxurl,c,function(a){mySubgroup.populateComboBox(a);$.group('populateComboBox',a['groups']);$("form input#btnsubgroupcancel").click();$.showStatusDialog({showImage:false,message:'Subgroup(s) deleted successfully.',show:false})},'json')})}}});$.clearPlaylist=function(){$('form :checkbox').each(function(){this.checked=false});$('div[id^="playlistsectioncolumnfields"],div[id^="playlistcolumnfields"]').html('');$('form input[type="text"],textarea').val('');$('select#playlistsectioncolumnselectcomboitem, select#playlistsectionselectcomboitem, select#playlistselectcomboitem, select#playlistselectsection, select#selectplaylistcomboitem, select#selectplaylisttablecolumn, select#selectplaylistsection, select#selectplaylistsectioncolumns, select#selectplaylist').find('option').remove();$('select[id^=playlistsectioncomboitemid], select[id^=playlistsectioncolumncomboitemid], select[id^=playlistsectionid], select[id^="playlistcolumncomboitemid"], select[id^=playlistcolumnsectionid]').find('option').remove();$('form select[name$="direction"]').val('ltr');$('div#playlistcombo').hide()};$('form select#selectplaylistsubgroup').change(function(){var b=this;$.resetAllFields();if($(b).val().length==0){$.clearPlaylist()}else{$.showStatusDialog({message:'Loading...'});var c={action:'giml_get_playlistdata',_ajax_nonce:nonce,subgroupid:$(b).val()};$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$.showStatusDialog({show:false})},'json')}});$('form input#btnplaylisttablecolumncancel').hide();$('form input#btnsubgroupdownloadupdate').click(function(){if($('form select#selectplaylistsubgroup').val().length>0){$.showStatusDialog({message:'Updating...'});var b={action:'giml_subgroup_update',_ajax_nonce:nonce,rows:1,subgroupid:$('form select#selectplaylistsubgroup').val(),fields:mySubgroup.settings.downloadElements};$('div#subgroupdownload').find('input,select').each(function(){if($(this).is(':checkbox')){b[this.id]=($(this).is(':checked'))?1:0}else{b[this.id]=$(this).val()}});$.post(ajaxurl,b,function(a){$.showStatusDialog({showImage:false,message:'Updated successfully.',show:false})})}});$('form input#btnplaylisttablecssupdate').click(function(){if($('form select#selectplaylistsubgroup').val().length>0){$.showStatusDialog({message:'Updating table css...'});var b={action:'giml_update',_ajax_nonce:nonce,table:'playlisttable',subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.tableElements};$('div#playlisttablemain').find('input,select').each(function(){b[this.id]=$(this).val()});$.post(ajaxurl,b,function(a){$.showStatusDialog({showImage:false,message:'Table CSS updated successfully.',show:false})})}});$("form select#selectplaylisttablecolumnrows").change(function(){myPlaylist.createTableColumnRows(parseInt($(this).val()))});$('form input#btnplaylisttablecolumncancel').click(function(){$('form select#selectplaylisttablecolumn').val(0);$('form select#selectplaylisttablecolumnrows').val(2);$('form select#selectplaylisttablecolumnrows').change();$('form select#selectplaylisttablecolumnrows').removeAttr('disabled');$('form input#btnplaylisttablecolumndelete').removeAttr('disabled');$('form input#btnplaylisttablecolumnadd').val('Add');$('form input#btnplaylisttablecolumncancel').hide()});$('form input#btnplaylisttablecolumnedit').click(function(){if($('form select#selectplaylisttablecolumn option:selected').length>0){$('form select#selectplaylisttablecolumnrows').val(2);$('form select#selectplaylisttablecolumnrows').change();$('form select#selectplaylisttablecolumnrows').attr('disabled','disabled');$('form input#btnplaylisttablecolumndelete').attr('disabled','disabled');$('form input#btnplaylisttablecolumnadd').val('Update');$('form input#btnplaylisttablecolumncancel').show();var b=$('form select#selectplaylisttablecolumn option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var c={action:'giml_edit',_ajax_nonce:nonce,table:'playlisttablecolumn',ids:b};$.post(ajaxurl,c,function(a){myPlaylist.createTableColumnRows(a.length,a);$.showStatusDialog({show:false})},'json')}});$('form input#btnplaylisttablecolumnadd').click(function(){if($('form select#selectplaylistsubgroup').val().length==0){return}if($(this).val()==='Update'){var b=[];$('form input[id^="playlisttablecolumnlabel"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length<$("div#playlisttablecolumnrowentries").find(".form-row").length){$.showStatusDialog({message:'Updating table column(s)...'});var c={action:'giml_update',_ajax_nonce:nonce,table:'playlisttablecolumn',rows:$("div#playlisttablecolumnrowentries").find(".form-row").length,subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.tableColumnRowElements};$('div#playlisttablecolumnrowentries').find('input,select').each(function(){c[this.id]=$(this).val()});$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylisttablecolumncancel").click();$.showStatusDialog({showImage:false,message:'Table column(s) updated successfully.',show:false})},'json')}}else{var b=[];$('form input[id^="playlisttablecolumnlabel"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length<parseInt($('form select#selectplaylisttablecolumnrows').val())){$.showStatusDialog({message:'Creating table column(s)...'});var c={action:'giml_insert',_ajax_nonce:nonce,table:'playlisttablecolumn',rows:$('form select#selectplaylisttablecolumnrows').val(),subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.tableColumnRowElements};$('div#playlisttablecolumnrowentries').find('input,select').each(function(){c[this.id]=$(this).val()});$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylisttablecolumncancel").click();$.showStatusDialog({showImage:false,message:'Table column(s) created successfully.',show:false})},'json')}}});$('form input#btnplaylisttablecolumndelete').click(function(){if($('form select#selectplaylisttablecolumn option:selected').length>0){var b=$('form select#selectplaylisttablecolumn option:selected').map(function(){return $(this).val()}).get().join(',');if(confirm('Are you sure you want to delete selected column(s)?')){$.showStatusDialog({message:'Deleting column(s)...'});var c={action:'giml_delete',_ajax_nonce:nonce,table:'playlisttablecolumn',subgroupid:$('form select#selectplaylistsubgroup').val(),ids:b};$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylisttablecolumncancel").click();$.showStatusDialog({showImage:false,message:'Column(s) deleted successfully.',show:false})},'json')}}});$('form input#btnplaylistcomboitemcancel').hide();$('form input#btnplaylistcomboupdate').click(function(){if($('form select#selectplaylistsubgroup').val().length>0){$.showStatusDialog({message:'Updating combo...'});var b={action:'giml_update',_ajax_nonce:nonce,table:'playlistcombo',subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.comboElements};$('div#playlistcombomain').find('input,select').each(function(){b[this.id]=$(this).val()});$.post(ajaxurl,b,function(a){$.showStatusDialog({showImage:false,message:'Combo updated successfully.',show:false})})}});$('form input#btnplaylistcomboitemadd').click(function(){if($('form select#selectplaylistsubgroup').val().length==0){return}if($(this).val()==='Update'){var b=[];$('form input[id^="playlistcomboitemlabel"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length<$("div#playlistcomboitemrowentries").find(".form-row").length){$.showStatusDialog({message:'Updating combo item(s)...'});var c={action:'giml_update',_ajax_nonce:nonce,table:'playlistcomboitem',rows:$("div#playlistcomboitemrowentries").find(".form-row").length,subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.comboItemRowElements};$('div#playlistcomboitemrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){c[this.id]=($(this).is(':checked'))?1:0}else{c[this.id]=$(this).val()}});$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylistcomboitemcancel").click();$.showStatusDialog({showImage:false,message:'Combo item(s) updated successfully.',show:false})},'json')}}else{var b=[];$('form input[id^="playlistcomboitemlabel"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length<parseInt($('form select#selectplaylistcomboitemrows').val())){$.showStatusDialog({message:'Creating combo item(s)...'});var c={action:'giml_insert',_ajax_nonce:nonce,table:'playlistcomboitem',rows:$('form select#selectplaylistcomboitemrows').val(),subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.comboItemRowElements};$('div#playlistcomboitemrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){c[this.id]=($(this).is(':checked'))?1:0}else{c[this.id]=$(this).val()}});$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylistcomboitemcancel").click();$.showStatusDialog({showImage:false,message:'Combo item(s) created successfully.',show:false})},'json')}}});$('form input#btnplaylistcomboitemedit').click(function(){if($('form select#selectplaylistcomboitem option:selected').length>0){$('form select#selectplaylistcomboitemrows').val(2);$('form select#selectplaylistcomboitemrows').change();$('form select#selectplaylistcomboitemrows').attr('disabled','disabled');$('form input#btnplaylistcomboitemdelete').attr('disabled','disabled');$('form input#btnplaylistcomboitemadd').val('Update');$('form input#btnplaylistcomboitemcancel').show();var b=$('form select#selectplaylistcomboitem option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var c={action:'giml_edit',_ajax_nonce:nonce,table:'playlistcomboitem',ids:b};$.post(ajaxurl,c,function(a){myPlaylist.createComboItemRows(a.length,a);$.showStatusDialog({show:false})},'json')}});$('form input#btnplaylistcomboitemcancel').click(function(){$('form select#selectplaylistcomboitem').val(0);$('form select#selectplaylistcomboitemrows').val(2);$('form select#selectplaylistcomboitemrows').change();$('form select#selectplaylistcomboitemrows').removeAttr('disabled');$('form input#btnplaylistcomboitemdelete').removeAttr('disabled');$('form input#btnplaylistcomboitemadd').val('Add');$('form input#btnplaylistcomboitemcancel').hide()});$("form select#selectplaylistcomboitemrows").change(function(){myPlaylist.createComboItemRows(parseInt($(this).val()))});$('form input#btnplaylistcomboitemdelete').click(function(){if($('form select#selectplaylistcomboitem option:selected').length>0){var b=$('form select#selectplaylistcomboitem option:selected').map(function(){return $(this).val()}).get().join(',');if(confirm('Are you sure you want to delete selected combo item(s)?')){$.showStatusDialog({message:'Deleting combo item(s)...'});var c={action:'giml_delete',_ajax_nonce:nonce,table:'playlistcomboitem',subgroupid:$('form select#selectplaylistsubgroup').val(),ids:b};$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylistcomboitemcancel").click();$.showStatusDialog({showImage:false,message:'Combo item(s) deleted successfully.',show:false})},'json')}}});$('form input#btnplaylistsectioncancel').hide();$('form input#btnplaylistsectionadd').click(function(){if($('form select#selectplaylistsubgroup').val().length==0){return}if($(this).val()==='Update'){var b=[];$('form input[id^="playlistsectionlabel"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length<$("div#playlistsectionrowentries").find(".form-row").length){$.showStatusDialog({message:'Updating playlist section(s)...'});var c={action:'giml_update',_ajax_nonce:nonce,table:'playlistsection',rows:$("div#playlistsectionrowentries").find(".form-row").length,subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.sectionRowElements};$('div#playlistsectionrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){c[this.id]=($(this).is(':checked'))?1:0}else{c[this.id]=$(this).val()}});$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylistsectioncancel").click();$("select#playlistsectionselectcomboitem").val(0);$.showStatusDialog({showImage:false,message:'Playlist section(s) updated successfully.',show:false})},'json')}}else{var b=[];$('form textarea[id^="playlistsectionlabel"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length<parseInt($('form select#selectplaylistsectionrows').val())){$.showStatusDialog({message:'Creating playlist section(s)...'});var c={action:'giml_insert',_ajax_nonce:nonce,table:'playlistsection',rows:$('form select#selectplaylistsectionrows').val(),subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.sectionRowElements};$('div#playlistsectionrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){c[this.id]=($(this).is(':checked'))?1:0}else{c[this.id]=$(this).val()}});$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylistsectioncancel").click();$("select#playlistsectionselectcomboitem").val(0);$.showStatusDialog({showImage:false,message:'Playlist section(s) created successfully.',show:false})},'json')}}});$('form input#btnplaylistsectionedit').click(function(){if($('form select#selectplaylistsection option:selected').length>0){$('form select#selectplaylistsectionrows').val(2);$('form select#selectplaylistsectionrows').change();$('form select#selectplaylistsectionrows').attr('disabled','disabled');$('form input#btnplaylistsectiondelete').attr('disabled','disabled');$('form input#btnplaylistsectionadd').val('Update');$('form input#btnplaylistsectioncancel').show();var b=$('form select#selectplaylistsection option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var c={action:'giml_edit',_ajax_nonce:nonce,table:'playlistsection',ids:b};$.post(ajaxurl,c,function(a){myPlaylist.createSectionRows(a.length,a);$.showStatusDialog({show:false})},'json')}});$('form input#btnplaylistsectiondelete').click(function(){if($('form select#selectplaylistsection option:selected').length>0){var b=$('form select#selectplaylistsection option:selected').map(function(){return $(this).val()}).get().join(',');if(confirm('Are you sure you want to delete selected section(s)?')){$.showStatusDialog({message:'Deleting playlist section(s)...'});var c={action:'giml_delete',_ajax_nonce:nonce,table:'playlistsection',subgroupid:$('form select#selectplaylistsubgroup').val(),ids:b};$.post(ajaxurl,c,function(a){myPlaylist.populateFields(a);$("form input#btnplaylistsectioncancel").click();$.showStatusDialog({showImage:false,message:'Playlist section(s) deleted successfully.',show:false})},'json')}}});$('form input#btnplaylistsectioncancel').click(function(){$('form select#selectplaylistsection').val(0);$('form select#selectplaylistsectionrows').val(2);$('form select#selectplaylistsectionrows').change();$('form select#selectplaylistsectionrows').removeAttr('disabled');$('form input#btnplaylistsectiondelete').removeAttr('disabled');$('form input#btnplaylistsectionadd').val('Add');$('form input#btnplaylistsectioncancel').hide()});$("form select#selectplaylistsectionrows").change(function(){myPlaylist.createSectionRows(parseInt($(this).val()))});$('form input#btnplaylistsectioncolumncancel').hide();$("form select#selectplaylistsectioncolumnrows").change(function(){myPlaylist.createSectionColumnRows(parseInt($(this).val()))});$('select#playlistsectionselectcomboitem').change(function(){if($(this).val()==0){$('form select#selectplaylistsection option').remove();$('form select#selectplaylistsection').append(myPlaylist.settings.sectionItems);return}$.showStatusDialog({message:'Loading section list...'});var b={action:'giml_get_playlistcombosections',_ajax_nonce:nonce,subgroupid:$('form select#selectplaylistsubgroup').val(),sortbysortorder:true,comboitemid:$(this).val()};$.post(ajaxurl,b,function(a){$('form select#selectplaylistsection option').remove();$('form select#selectplaylistsection').append($.stripslashes(a['playlistsection']));$.showStatusDialog({show:false})},'json')});$('select#playlistsectioncolumnselectcomboitem').change(function(){if($(this).val()==0){$('form select#selectplaylistsectioncolumns option').remove();$('form select#selectplaylistsectioncolumns').append(myPlaylist.settings.sectionColumnItems);return}$.showStatusDialog({message:'Loading section columns list...'});var b={action:'giml_get_playlistcombosectioncolumns',_ajax_nonce:nonce,subgroupid:$('form select#selectplaylistsubgroup').val(),comboitemid:$(this).val()};$.post(ajaxurl,b,function(a){$('form select#selectplaylistsectioncolumns option').remove();$('form select#selectplaylistsectioncolumns').append($.stripslashes(a['playlistsectioncolumn']));$.showStatusDialog({show:false})},'json')});$('form input#btnplaylistsectioncolumnadd').click(function(){if($('form select#selectplaylistsubgroup').val().length==0)return;var b=[];var c=[];$('form select[id^="playlistsectionid"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}else{c.push(this.id.toString().split('_')[1])}});c=c.join(",");if(b.length==parseInt($('form select#selectplaylistsectioncolumnrows').val()))return;var d={};var e=false;$('form select[id^="playlistsectionid"]').each(function(a){if($.trim($(this).val()).length>0){if(!d[parseInt($(this).val())]){d[parseInt($(this).val())]=true}else{e=true}}});if(e){alert('You cannot create duplicate section in one combo.');return}if($(this).val()==='Update'){$.showStatusDialog({message:'Updating section column(s)...'});var f={action:'giml_update',_ajax_nonce:nonce,table:'playlistsectioncolumn',rows:$("div#playlistsectioncolumnrowentries").find(".form-row").length,subgroupid:$('form select#selectplaylistsubgroup').val(),ids:c,fields:myPlaylist.settings.sectionColumnRowElements};$('div#playlistsectioncolumnrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){f[this.id]=($(this).is(':checked'))?1:0}else{f[this.id]=$(this).val()}});$.post(ajaxurl,f,function(a){myPlaylist.populateSectionColumns(a['playlistsectioncolumn']);myPlaylist.populatePlaylistColumnSections(a['playlistcolumnsections']);$("form input#btnplaylistsectioncolumncancel").click();$("select#playlistsectioncolumnselectcomboitem").val(0);$.showStatusDialog({showImage:false,message:'Playlist section column(s) updated successfully.',show:false})},'json')}else{$.showStatusDialog({message:'Creating section column(s)...'});var f={action:'giml_insert',_ajax_nonce:nonce,table:'playlistsectioncolumn',rows:$('form select#selectplaylistsectioncolumnrows').val(),subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.sectionColumnRowElements};$('div#playlistsectioncolumnrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){f[this.id]=($(this).is(':checked'))?1:0}else{f[this.id]=$(this).val()}});$.post(ajaxurl,f,function(a){myPlaylist.populateSectionColumns(a['playlistsectioncolumn']);myPlaylist.populatePlaylistColumnSections(a['playlistcolumnsections']);$("form input#btnplaylistsectioncolumncancel").click();$("select#playlistsectioncolumnselectcomboitem").val(0);$.showStatusDialog({showImage:false,message:'Playlist section column(s) created successfully.',show:false})},'json')}});$('form input#btnplaylistsectioncolumnedit').click(function(){if($('form select#selectplaylistsectioncolumns option:selected').length==0){return}$('form select#selectplaylistsectioncolumnrows').val(2);$('form select#selectplaylistsectioncolumnrows').change();$('form select#selectplaylistsectioncolumnrows').attr('disabled','disabled');$('form input#btnplaylistsectioncolumndelete').attr('disabled','disabled');$('form input#btnplaylistsectioncolumnadd').val('Update');$('form input#btnplaylistsectioncolumncancel').show();var b=$('form select#selectplaylistsectioncolumns option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var c={action:'giml_edit',_ajax_nonce:nonce,table:'playlistsectioncolumn',ids:b};$.post(ajaxurl,c,function(a){myPlaylist.createSectionColumnRows(a.length,a);$.showStatusDialog({show:false})},'json')});$('form input#btnplaylistsectioncolumndelete').click(function(){if($('form select#selectplaylistsectioncolumns option:selected').length>0){var b=$('form select#selectplaylistsectioncolumns option:selected').map(function(){return $(this).val().toString().split('_')[1]}).get().join(',');if(confirm('Are you sure you want to delete selected section column(s)?')){$.showStatusDialog({message:'Deleting section column(s)...'});var c={action:'giml_delete',_ajax_nonce:nonce,table:'playlistsectioncolumn',subgroupid:$('form select#selectplaylistsubgroup').val(),ids:b};$.post(ajaxurl,c,function(a){myPlaylist.populateSectionColumns(a['playlistsectioncolumn']);myPlaylist.populatePlaylistColumnSections(a['playlistcolumnsections']);$("form input#btnplaylistsectioncolumncancel").click();$("select#playlistsectioncolumnselectcomboitem").val(0);$.showStatusDialog({showImage:false,message:'Playlist section column(s) deleted successfully.',show:false})},'json')}}});$('form input#btnplaylistsectioncolumncancel').click(function(){$('form select#selectplaylistsectioncolumns').val(0);$('form select#selectplaylistsectioncolumnrows').val(2);$('form select#selectplaylistsectioncolumnrows').change();$('form select#selectplaylistsectioncolumnrows').removeAttr('disabled');$('form input#btnplaylistsectioncolumndelete').removeAttr('disabled');$('form input#btnplaylistsectioncolumnadd').val('Add');$('form input#btnplaylistsectioncolumncancel').hide()});$('select#playlistselectcomboitem').change(function(){if($(this).val()===""){$('select#playlistselectsection option').remove();$('form select#selectplaylist option').remove();$('form select#selectplaylist').append(myPlaylist.settings.playlistColumnItems);return}$.showStatusDialog({message:'Loading section list...'});var b={action:'giml_get_playlistcombosections',_ajax_nonce:nonce,subgroupid:$('form select#selectplaylistsubgroup').val(),sortbysortorder:false,comboitemid:$(this).val()};$.post(ajaxurl,b,function(a){$('form select#playlistselectsection option').remove();$('form select#playlistselectsection').append('<option value="">Select</option>'+$.stripslashes(a['playlistsection']));$.showStatusDialog({show:false})},'json')});$('select#playlistselectsection').change(function(){if($(this).val()===""){$('form select#selectplaylist option').remove();$('form select#selectplaylist').append(myPlaylist.settings.playlistColumnItems);return}$.showStatusDialog({message:'Loading playlist...'});var b={action:'giml_get_playlistcolumns',_ajax_nonce:nonce,sectionid:$('form select#playlistselectsection').val(),subgroupid:$('form select#selectplaylistsubgroup').val()};$.post(ajaxurl,b,function(a){$('form select#selectplaylist option').remove();$('form select#selectplaylist').append($.stripslashes(a['playlistcolumns']));$.showStatusDialog({show:false})},'json')});$('form input#btnplaylistcancel').hide();$('form input#btnplaylistadd').click(function(){if($('form select#selectplaylistsubgroup').val().length==0){return}var b=[];$('form select[id^="playlistcolumnsectionid"]').each(function(a){if($.trim($(this).val()).length==0){b.push(true)}});if(b.length==parseInt($('form select#selectplaylistrows').val())){return}if($(this).val()==='Update'){if(b.length>0){alert("You cannot update with empty section.");return}$.showStatusDialog({message:'Updating playlist column(s)...'});var c={action:'giml_update',_ajax_nonce:nonce,table:'playlistcolumn',rows:$("div#playlistrowentries").find(".form-row").length,subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.playlistColumnRowElements,ids:myPlaylist.settings.playlistColumnIDs};$('div#playlistrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){c[this.id]=($(this).is(':checked'))?1:0}else{c[this.id]=$(this).val()}});$.post(ajaxurl,c,function(a){myPlaylist.populatePlaylistColumns(a['playlistcolumns']);myPlaylist.populatePlaylistPlaylistSections(a['playlistplaylistsections']);$("form input#btnplaylistcancel").click();$('select#playlistselectcomboitem').val("");$('select#playlistselectsection option').remove();$.showStatusDialog({showImage:false,message:'Playlist column(s) updated successfully.',show:false})},'json')}else{$.showStatusDialog({message:'Creating playlist column(s)...'});var c={action:'giml_insert',_ajax_nonce:nonce,table:'playlistcolumn',rows:$('form select#selectplaylistrows').val(),subgroupid:$('form select#selectplaylistsubgroup').val(),fields:myPlaylist.settings.playlistColumnRowElements};$('div#playlistrowentries').find('input,select,textarea').each(function(){if($(this).is(':checkbox')){c[this.id]=($(this).is(':checked'))?1:0}else{c[this.id]=$(this).val()}});$.post(ajaxurl,c,function(a){myPlaylist.populatePlaylistColumns(a['playlistcolumns']);myPlaylist.populatePlaylistPlaylistSections(a['playlistplaylistsections']);$("form input#btnplaylistcancel").click();$('select#playlistselectcomboitem').val("");$('select#playlistselectsection option').remove();$.showStatusDialog({showImage:false,message:'Playlist column(s) created successfully.',show:false})},'json')}});$('form input#btnplaylistedit').click(function(){if($('form select#selectplaylist option:selected').length==0)return;$('form select#selectplaylistrows').val(2);$('form select#selectplaylistrows').change();$('form select#selectplaylistrows').attr('disabled','disabled');$('form input#btnplaylistdelete').attr('disabled','disabled');$('form input#btnplaylistadd').val('Update');$('form input#btnplaylistcancel').show();myPlaylist.settings.playlistColumnIDs=$('form select#selectplaylist option:selected').map(function(){return $(this).val()}).get().join(',');$.showStatusDialog({message:'Loading...'});var b={action:'giml_edit',_ajax_nonce:nonce,table:'playlistcolumn',ids:$('form select#selectplaylistsubgroup').val()+"::"+$('select#playlistselectcomboitem').val()+"_"+$('select#playlistselectsection').val()+"_"+myPlaylist.settings.playlistColumnIDs};$.post(ajaxurl,b,function(a){myPlaylist.createPlaylistColumnRows(a.length,a);$.showStatusDialog({show:false})},'json')});$('form input#btnplaylistdelete').click(function(){if($('form select#selectplaylist option:selected').length>0){var b=$('form select#selectplaylist option:selected').map(function(){return $(this).val()}).get().join(',');if(confirm('Are you sure you want to delete selected playlist column(s)?')){$.showStatusDialog({message:'Deleting playlist column(s)...'});var c={action:'giml_delete',_ajax_nonce:nonce,table:'playlistcolumn',subgroupid:$('form select#selectplaylistsubgroup').val(),ids:b};$.post(ajaxurl,c,function(a){myPlaylist.populatePlaylistColumns(a['playlistcolumns']);myPlaylist.populatePlaylistPlaylistSections(a['playlistplaylistsections']);$("form input#btnplaylistcancel").click();$('select#playlistselectcomboitem').val("");$('select#playlistselectsection option').remove();$.showStatusDialog({showImage:false,message:'Playlist column(s) deleted successfully.',show:false})},'json')}}});$('form input#btnplaylistcancel').click(function(){$('form select#selectplaylist').val(0);$('form select#selectplaylistrows').val(2);$('form select#selectplaylistrows').change();$('form select#selectplaylistrows').removeAttr('disabled');$('form input#btnplaylistdelete').removeAttr('disabled');$('form input#btnplaylistadd').val('Add');$('form input#btnplaylistcancel').hide();myPlaylist.settings.playlistColumnIDs=""});$("form select#selectplaylistrows").change(function(){myPlaylist.createPlaylistColumnRows(parseInt($(this).val()))});$('div#playlistcombo').hide();$('form input#subgroupshowcombo1').change(function(){$('div#playlistcombo').slideToggle()})});
</script>
<div class="clear"></div>
<div id="statusdialog" style="-webkit-box-shadow: 2px 2px 5px #888; -moz-box-shadow: 2px 2px 5px #888;">
	<div class="col"><img src="<?php echo plugins_url( 'images/ajax-loader.gif', dirname( __FILE__ ) ); ?>" style="display:none" alt="loading" id="loading" /></div>
	<div class="col-right"><span style="font-size: 20px; line-height:40px"></span></div>
	<div class="clear"></div>
</div>
<div class="wrap">
<?php echo screen_icon(); ?>
<h2>GI-Media Library</h2>
<form action="" method="post">

<div id="gi-medialibrary-admintabs">
	<ul>
		<li><a href="#group">Group/Subgroup</a></li>
		<li><a href="#playlist">Playlist</a></li>
                <li><a href="#aboutgiml">About</a></li>
	</ul>
	<div id="group">
		<h3>Groups</h3>
		<div class="form-row">
		 <div class="col">
		  <label for="selectgroup">Select Groups:</label><br />
		  <select class="postform" style="min-width:25em" id="selectgroup" name="selectgroup" multiple="multiple"></select>
		 </div>
		 <div class="col">
		  <input type="button" class="button" value="Edit" id="btngroupedit" name="btngroupedit"/>
		  <input type="button" class="button" value="Delete" id="btngroupdelete" name="btngroupdelete"/>
		 </div>
                    <div class="clear"></div>
		</div>
		<div class="form-row">
		 <div class="col">
		  <label for="selectgrouprows">Select row entries:</label>
		  <select class="postform" id="selectgrouprows" name="selectgrouprows">
			 <option value="2" selected="selected">2</option>
			 <option value="4">4</option>
			 <option value="8">8</option>
			 <option value="16">16</option>
			 <option value="32">32</option>
			 <option value="64">64</option>
			</select>
		 </div>
                    <div class="clear"></div>
		</div>
		<div id="grouprowentries" class="rowentries"></div>
		<div class="form-row">
		 <input type="button" class="button-primary" value="Add" id="btngroupadd" name="btngroupadd"/>
		 <input type="button" class="button" value="Cancel" id="btngroupcancel" name="btngroupcancel"/>
		</div>
		<br class="clear"/>
		<hr/>
		<h3>Subgroups</h3>
		<div class="form-row">
		 <div class="col">
		  <label for="groupselectgroup">Select Group:</label><br/>
		  <select class="postform" id="groupselectgroup" name="groupselectgroup"></select>
		 </div>
		 <div class="col">
		  <label for="selectsubgroup">Select Subgroups:</label><br/>
		  <select class="postform" style="min-width:25em" id="selectsubgroup" name="selectsubgroup" multiple="multiple"></select>
		 </div>
		 <div class="col">
		  <input type="button" class="button" value="Edit" id="btnsubgroupedit" name="btnsubgroupedit"/>
		  <input type="button" class="button" value="Delete" id="btnsubgroupdelete" name="btnsubgroupdelete"/>
		 </div>
                    <div class="clear"></div>
		</div>
		<div class="form-row">
		 <div class="col">
		  <label for="selectsubgrouprows">Select row entries:</label>
		  <select class="postform" id="selectsubgrouprows" name="selectsubgrouprows">
			 <option value="2" selected="selected">2</option>
			 <option value="4">4</option>
			 <option value="8">8</option>
			 <option value="16">16</option>
			 <option value="32">32</option>
			 <option value="64">64</option>
			</select>
		 </div>
                    <div class="clear"></div>
		</div>
		<div id="subgrouprowentries" class="rowentries"></div>
		<div class="form-row">
		 <input type="button" class="button-primary" value="Add" id="btnsubgroupadd" name="btnsubgroupadd"/>
		 <input type="button" class="button" value="Cancel" id="btnsubgroupcancel" name="btnsubgroupcancel"/>
		</div>
		<br class="clear"/>
	</div>
	<div id="playlist">
		<br/>
		<label for="playlistselectgroup">Select Group:</label>
		<select class="postform" id="playlistselectgroup" name="playlistselectgroup"></select>&nbsp;&nbsp;&nbsp;
	    <label for="selectplaylistsubgroup">Select Subgroup:</label>
		<select class="postform" id="selectplaylistsubgroup" name="selectplaylistsubgroup"></select>
		<div class="form-row"></div>
		<div class="form-row"></div>
		<div id="subgroupdownload">
			<div class="form-row">
			 <div class="col">
			  <label for="subgroupdownloadlink1">Download link:</label>
			  <input type="text" class="regular-text" id="subgroupdownloadlink1" name="subgroupdownloadlink1"/>
			 </div>
			 <div class="col">
			  <label for="subgroupdownloadlabel1">Label:</label>
			  <input type="text" id="subgroupdownloadlabel1" name="subgroupdownloadlabel1">
			 </div>
			 <div class="col">
			  <label for="subgroupdownloadcss1">CSS:</label>
			  <input type="text" class="small-text" id="subgroupdownloadcss1" name="subgroupdownloadcss1">
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <input type="checkbox" value="1" id="subgroupshowfilter1" name="subgroupshowfilter1"/>
			  <label for="subgroupshowfilter1">Show filter</label>
			 </div>
			 <div class="col">
			  <input type="checkbox" value="1" id="subgroupshowcombo1" name="subgroupshowcombo1"/>
			  <label for="subgroupshowcombo1">Show combo box</label>
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <input type="button" class="button" value="Update" id="btnsubgroupdownloadupdate" name="btnsubgroupdownloadupdate"/>
			 </div>
                            <div class="clear"></div>
			</div>
		</div>
		<div id="playlistcombo" class="form-row ui-widget ui-widget-content ui-corner-all" style="padding:10px 0 10px">
			<div class="form-row">
			 <div id="playlistcombomain">
			 <div class="col">
			  <label for="playlistcombolabel">Combo label:</label>
			  <input type="text" id="playlistcombolabel" name="playlistcombolabel">
			 </div>
			 <div class="col">
			  <label for="playlistcombocss">CSS:</label>
			  <input type="text" class="small-text" id="playlistcombocss" name="playlistcombocss">
			 </div>
			 <div class="col">
			  <label for="playlistcombodirection">Direction:</label>
			  <select id="playlistcombodirection" name="playlistcombodirection">
				 <option value="ltr" selected="selected">LTR</option>
				 <option value="rtl">RTL</option>
				</select>
			 </div>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Update" id="btnplaylistcomboupdate" name="btnplaylistcomboupdate"/>
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylistcomboitem">Combo Items:
			  <select class="postform" style="min-width:25em" id="selectplaylistcomboitem" name="selectplaylistcomboitem" multiple="multiple"></select>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Edit" id="btnplaylistcomboitemedit" name="btnplaylistcomboitemedit"/>
			  <input type="button" class="button" value="Delete" id="btnplaylistcomboitemdelete" name="btnplaylistcomboitemdelete"/>
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylistcomboitemrows">Select row entries:</label>
			  <select class="postform" id="selectplaylistcomboitemrows" name="selectplaylistcomboitemrows">
				 <option value="2" selected="selected">2</option>
				 <option value="4">4</option>
				 <option value="8">8</option>
				 <option value="16">16</option>
				 <option value="32">32</option>
				 <option value="64">64</option>
				</select>
			 </div>
                            <div class="clear"></div>
			</div>
			<div id="playlistcomboitemrowentries" class="rowentries"></div>
			<div class="form-row">
			 <input type="button" class="button-primary" value="Add" id="btnplaylistcomboitemadd" name="btnplaylistcomboitemadd"/>
			 <input type="button" class="button" value="Cancel" id="btnplaylistcomboitemcancel" name="btnplaylistcomboitemcancel"/>
			</div>
		</div>		<!--end playlist-combo-->
		
		<br class="clear"/>
		<div id="playlist-table-layout">	<!--playlist-table-layout-->
			<h3>Table Layout</h3>
			<div class="form-row">
			 <div id="playlisttablemain">
			 <div class="col">
			  <label for="playlisttablecss">CSS:</label>
			  <input type="text" class="small-text" id="playlisttablecss" name="playlisttablecss"/>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Update" id="btnplaylisttablecssupdate" name="btnplaylisttablecssupdate"/>
			 </div>
                             <div class="clear"></div>
			 </div>
			<div class="form-row"></div>
			<div class="form-row"></div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylisttablecolumn">Select Columns:</label>
			 </div>
			 <div class="col">
			  <select class="postform" style="min-width:25em" id="selectplaylisttablecolumn" name="selectplaylisttablecolumn" multiple="multiple"></select>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Edit" id="btnplaylisttablecolumnedit" name="btnplaylisttablecolumnedit"/>
			  <input type="button" class="button" value="Delete" id="btnplaylisttablecolumndelete" name="btnplaylisttablecolumndelete"/>
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylisttablecolumnrows">Select row entries:</label>
			  <select class="postform" id="selectplaylisttablecolumnrows" name="selectplaylisttablecolumnrows">
				 <option value="2" selected="selected">2</option>
				 <option value="4">4</option>
				 <option value="8">8</option>
				 <option value="16">16</option>
				 <option value="32">32</option>
				 <option value="64">64</option>
				</select>
			 </div>
                            <div class="clear"></div>
			</div>
			<div id="playlisttablecolumnrowentries" class="rowentries"></div>
			<div class="form-row">
			 <input type="button" class="button-primary" value="Add" id="btnplaylisttablecolumnadd" name="btnplaylisttablecolumnadd"/>
			 <input type="button" class="button" value="Cancel" id="btnplaylisttablecolumncancel" name="btnplaylisttablecolumncancel"/>
			</div>
			</div>
		</div>			<!--end playlist-table-layout-->
		<br class="clear"/>
		<hr/>
			<h3>Playlist</h3>
			
		 <div id="playlist-sections">
			<div class="form-row">
			 <h4>Playlist Sections</h4>
			 <div class="col">
			  <label for="playlistsectionselectcomboitem">Combo Item:</label><br/>
			  <select class="postform" id="playlistsectionselectcomboitem" name="playlistsectionselectcomboitem"></select>
			 </div>
			 <div class="col">
			  <label for="selectplaylistsection">Select sections:</label><br/>
			  <select class="postform" style="min-width:25em" id="selectplaylistsection" name="selectplaylistsection" multiple="multiple"></select>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Edit" id="btnplaylistsectionedit" name="btnplaylistsectionedit"/>
			  <input type="button" class="button" value="Delete" id="btnplaylistsectiondelete" name="btnplaylistsectiondelete"/>
			 </div>
                         <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylistsectionrows">Select row entries:</label>
			  <select class="postform" id="selectplaylistsectionrows" name="selectplaylistsectionrows">
				 <option value="2" selected="selected">2</option>
				 <option value="4">4</option>
				 <option value="8">8</option>
				 <option value="16">16</option>
				 <option value="32">32</option>
				 <option value="64">64</option>
				</select>
			 </div>
                            <div class="clear"></div>
			</div>
			<div id="playlistsectionrowentries" class="rowentries"></div>			<!--end playlistsectionrowentries-->
			<div class="form-row">
			 <input type="button" class="button-primary" value="Add" id="btnplaylistsectionadd" name="btnplaylistsectionadd"/>
			 <input type="button" class="button" value="Cancel" id="btnplaylistsectioncancel" name="btnplaylistsectioncancel"/>
			</div>
		 </div>			<!--end playlist-sections-->

			<div class="form-row">
			 <hr/>
			</div>
		  <div id="playlist-columns">
			<div class="form-row">
			 <h4>Section Columns</h4>
			 <div class="col">
			  <label for="playlistsectioncolumnselectcomboitem">Combo Item:</label><br/>
			  <select class="postform" id="playlistsectioncolumnselectcomboitem" name="playlistsectioncolumnselectcomboitem"></select>
			 </div>
			 <div class="col">
			  <label for="selectplaylistsectioncolumns">Select section columns:</label><br/>
			  <select class="postform" style="min-width:25em" id="selectplaylistsectioncolumns" name="selectplaylistsectioncolumns" multiple="multiple"></select>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Edit" id="btnplaylistsectioncolumnedit" name="btnplaylistsectioncolumnedit"/>
			  <input type="button" class="button" value="Delete" id="btnplaylistsectioncolumndelete" name="btnplaylistsectioncolumndelete"/>
			 </div>
                         <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylistsectioncolumnrows">Select row entries:</label>
			  <select class="postform" id="selectplaylistsectioncolumnrows" name="selectplaylistsectioncolumnrows">
				 <option value="2" selected="selected">2</option>
				 <option value="4">4</option>
				 <option value="8">8</option>
				 <option value="16">16</option>
				 <option value="32">32</option>
				 <option value="64">64</option>
				</select>
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col"><font color="#FF0000">Note:
			 <ul>
			 <li>For download, audio, link and iconic link type columns, provide two colons separated urls.</li>
			 <li>To provide title for audio and link, separate the url and title with two vertical bars "||". For eg. http://www.test.com/test.mp3||Test Audio</li>
			 </ul></font></div>
                            <div class="clear"></div>
			</div>
			<div id="playlistsectioncolumnrowentries" class="rowentries"></div>
			<div class="form-row">
			 <input type="button" class="button-primary" value="Add" id="btnplaylistsectioncolumnadd" name="btnplaylistsectioncolumnadd"/>
			 <input type="button" class="button" value="Cancel" id="btnplaylistsectioncolumncancel" name="btnplaylistsectioncolumncancel"/>
			</div>
		 
			<div class="form-row"><hr/></div>
			<div class="form-row">
			 <h4>Playlist Columns</h4>
			 <div class="col">
			  <label for="playlistselectcomboitem">Combo Item:</label><br/>
			  <select class="postform" id="playlistselectcomboitem" name="playlistselectcomboitem"></select>
			 </div>
			 <div class="col">
			  <label for="playlistselectsection">Section:</label><br/>
			  <select class="postform" id="playlistselectsection" name="playlistselectsection"></select>
			 </div>
			 <div class="col">
			  <label for="selectplaylist">Select playlists:</label><br/>
			  <select class="postform" style="min-width:25em; max-width:80em;" id="selectplaylist" name="selectplaylist" multiple="multiple"></select>
			 </div>
			 <div class="col">
			  <input type="button" class="button" value="Edit" id="btnplaylistedit" name="btnplaylistedit"/>
			  <input type="button" class="button" value="Delete" id="btnplaylistdelete" name="btnplaylistdelete"/>
			 </div>
                         <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col">
			  <label for="selectplaylistrows">Select row entries:</label>
			  <select class="postform" id="selectplaylistrows" name="selectplaylistrows">
				 <option value="2" selected="selected">2</option>
				 <option value="4">4</option>
				 <option value="8">8</option>
				 <option value="16">16</option>
				 <option value="32">32</option>
				 <option value="64">64</option>
				</select>
			 </div>
                            <div class="clear"></div>
			</div>
			<div class="form-row">
			 <div class="col"><font color="#FF0000">Note:
			 <ul>
			 <li>For download, audio, link and iconic link type columns, provide two colons separated urls.</li>
			 <li>To provide title for audio and link, separate the url and title with two vertical bars "||". For eg. http://www.test.com/test.mp3||Test Audio</li>
			 </ul></font></div>
                            <div class="clear"></div>
			</div>
			<div id="playlistrowentries" class="rowentries"></div>
		<div class="form-row">
		 <input type="button" class="button-primary" value="Add" id="btnplaylistadd" name="btnplaylistadd"/>
		 <input type="button" class="button" value="Cancel" id="btnplaylistcancel" name="btnplaylistcancel"/>
		</div>
		</div>
		<br class="clear"/>
	</div>			<!--end playlist tab-->
        <div id="aboutgiml">
            <div id="post-body" class="metabox-holder column-2">
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <h3>About GI-Media Library</h3>
                                <div class="inside">
                                    <p>GI-Media Library was developed especially for institutions providing online education. With this plugin, it's easy to create your course/media library in a tabular form without any effort of custom building pages and layouts. You can organize it into a group and subgroup, create playlist section and then add your links to course materials or media's under that section. You can create your own table with desired number of columns like topic, duration, files etc. It supports all type of libraries like audio, video, pdf, doc etc. This plugin also comes with built-in HTML5 player from jPlayer and it supports following media formats:</p>
                                    <ul>
                                        <li>HTML5: mp3, mp4 (AAC/H.264), ogg (Vorbis/Theora), webm (Vorbis/VP8), wav</li>
                                        <li>Flash: mp3, mp4 (AAC/H.264), rtmp, flv</li>
                                    </ul>
                                    <p>You can fully customize the layout by providing CSS stylesheet class and change the text direction from LTR to RTL, if you want to use Arabic, Persian, Urdu languages.</p>
                                </div>
                            </div>
                            <div class="postbox">
                                <h3>User's Manual</h3>
                                <div class="inside">
                                    <div class="col-right"><a href="http://www.adobe.com/go/getreader"><img src="<?php echo GIML_URI; ?>images/get_adobe_reader.gif"></a></div>
                                    <div class="clear"></div>
                                    <iframe src="http://www.glareofislam.com/softwares/gimedialibrary-manual.pdf?nocache=<?php echo time(); ?>" data-auto-height="false" data-aspect-ratio="undefined" scrolling="no" width="100%" height="600" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-2" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <h3>Author and License</h3>
                                <div class="inside">
                                    <p>This plugin was written and developed by <a href="http://www.glareofislam.com/softwares/gimedialibrary.html">Zishan Javaid</a>. It is licensed as Free Software under <a href="http://www.gnu.org/licenses/gpl-2.0.html">GNU General Public License 2 (GPL 2)</a>. If you like the plugin, giving a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8">donation</a> is recommended. Please rate and review the plugin in the <a href="http://wordpress.org/plugins/gi-media-library/">WordPress Plugin Directory</a>. Donations and good ratings encourage me to further develop the plugin and to provide countless hours of support. Any amount is appreciated! Thanks!</p>
                                </div>
                            </div>
                            <div class="postbox">
                                <h3>Donate by PayPal</h3>
                                <div class="inside">
                                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                    <input type="hidden" name="cmd" value="_s-xclick">
                                    <input type="hidden" name="hosted_button_id" value="HQ2DHNS7TQNZ8">
                                    <p align="center"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="clear"></div>
        </div>      <!-- END ABOUT TAB -->
</div>	<!--END TAB BOX-->
</form>
</div>