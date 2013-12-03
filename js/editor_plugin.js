/*
*			Editor Plugin ver:1.1
*			Author: Zishan Javaid
*			Glare of Islam
*			http://www.glareofislam.com
*			Email: info@glareofislam.com
*
*/

(function(){tinymce.create('tinymce.plugins.gi_medialibrary',{init:function(g,h){tinymce.plugins.gi_medialibrary.theurl=h;jQuery.stripslashes=function(a){a=a.replace(/\\'/g,'\'');a=a.replace(/\\"/g,'"');a=a.replace(/\\0/g,'\0');a=a.replace(/\\\\/g,'\\');return a};jQuery.addslashes=function(a){return(a+'').replace(/[\\"']/g,'\\$&').replace(/\u0000/g,'\\0')};jQuery('body').append('					<div id="giml_shortcode" style="overflow:hidden; padding-bottom:15px;">					<div id="giml_loader">					 <p align="center"><img src="'+tinymce.plugins.gi_medialibrary.theurl+'/ajax-loader.gif" width="16">&nbsp;Loading . . .</p>					</div>					<div class="row">					 <div class="col">					 					 </div>					</div>					<div class="row">					 <div class="col">					  <label for="giml_group">Select Group:</label><br/>					  <select id="giml_group" name="giml_group" style="width:250px"><option value="">Select</option></select>					 </div>					</div>					<div class="row">					 <div class="col">					  <label for="giml_resource">Select Resource:</label><br/>					  <select id="giml_resource" name="giml_resource" style="width:250px"><option value="">Select</option></select>					 </div>					</div>					<div class="row"></div>					<div id="giml_options" class="row">					 <div class="col"><input type="checkbox" id="giml_setasdefault" name="giml_setasdefault" value="1"/>&nbsp;<label for="giml_setasdefault">Set as default</label></div>					</div>					<div class="row"></div>					<input type="button" class="button" id="giml_shortcode_add" name="giml_shortcode_add" value="Add"/>					</div>					<div style="clear:both"></div>										');jQuery('div#giml_shortcode').dialog({title:'Add GI-Media Library shortcode',resizable:false,minWidth:false,minHeight:false,width:"300px",autoOpen:false,open:function(){var e={settings:{groupItems:'',subgroupItems:''},init:function(a){jQuery.shortcode.settings=jQuery.extend({},jQuery.shortcode.settings,a)}};function shortcode(a){this.init(a);return this}jQuery.extend(shortcode.prototype,e);jQuery.shortcode=function(a){return new shortcode(a)};myshortcode=jQuery.shortcode();jQuery('select#giml_group,select#giml_resource').val("");jQuery.fetchingstart=function(){jQuery('select#giml_group,select#giml_resource,input#giml_shortcode_add,input#giml_setasdefault').attr('disabled','disabled');jQuery('div#giml_loader').show();jQuery('div#giml_options').hide()};jQuery.fetchingend=function(){jQuery('select#giml_group,select#giml_resource,input#giml_shortcode_add,input#giml_setasdefault').removeAttr('disabled');jQuery('div#giml_loader').hide();jQuery('input#giml_setasdefault').attr('checked',false)};jQuery.fetchingstart();var f={action:'giml_get_shortcodedata',datatype:'init'};jQuery.post(ajaxurl,f,function(a){myshortcode.settings.groups=jQuery.stripslashes(a['groups']);myshortcode.settings.subgroups=jQuery.stripslashes(a['subgroups']);jQuery('select#giml_group option').remove();jQuery('select#giml_group').append('<option value="">Select</option>'+myshortcode.settings.groups);jQuery('select#giml_resource option').remove();jQuery('select#giml_resource').append('<option value="">Select</option>'+myshortcode.settings.subgroups);jQuery.fetchingend()},'json');jQuery('select#giml_group').change(function(){if(jQuery(this).val().length==0){jQuery('select#giml_resource option').remove();jQuery('select#giml_resource').append('<option value="">Select</option>'+myshortcode.settings.subgroups);jQuery('div#giml_shortcode').find('div#giml_options').hide();jQuery('input#giml_setasdefault').attr('checked',false);return}jQuery.fetchingstart();var b={action:'giml_get_shortcodedata',datatype:'groupsubgroups',groupid:jQuery(this).val()};jQuery.post(ajaxurl,b,function(a){jQuery('select#giml_resource option').remove();jQuery('select#giml_resource').append('<option value="">Select</option>'+jQuery.stripslashes(a['subgroups']));jQuery.fetchingend()},'json')});jQuery('select#giml_resource').change(function(){if(jQuery(this).val().length==0){jQuery('div#giml_shortcode').find('div#giml_options').hide();return}jQuery('div#giml_shortcode').find('div#giml_options').show()});jQuery('input#giml_shortcode_add').click(function(){if(jQuery('select#giml_resource').val().length==0)return;var a=jQuery('select#giml_resource').val();var b=a.split(':::')[0];var c=a.split(':::')[1];var d=(jQuery('input#giml_setasdefault:checked').length>0)?"1":"0";tinymce.activeEditor.selection.setContent('[gi_medialibrary id="'+b+'" default="'+d+'"]');jQuery('div#giml_resource').find(':checkbox').each(function(){this.checked=false})})},close:function(){jQuery('select#giml_resource').unbind('change');jQuery('input#giml_shortcode_add').unbind('click')}})},createControl:function(n,a){switch(n){case'gi_medialibrary':var c=a.createButton('gi_medialibrary',{title:'Insert media items from GI-Media Library',image:tinymce.plugins.gi_medialibrary.theurl+'/medialibrary_button_icon2.png',onclick:function(){jQuery('#giml_shortcode').dialog('open')}});return c}},getInfo:function(){return{longname:"GI-Media Library Button",author:'Glare of Islam',authorurl:'http://www.glareofislam.com/',infourl:'Email: info@glareofislam.com',version:"1.1"}}});tinymce.PluginManager.add('gi_medialibrary',tinymce.plugins.gi_medialibrary)})();