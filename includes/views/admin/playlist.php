<!--

@author Zishan J.

-->
<script>
    jQuery(function($) {
    $.showStatusDialog = function(options) {
    var defaults = {
    showImage: true,
            show: true,
            message: ''
    };
            var merged_options = $.extend({}, defaults, options);
            if (merged_options.showImage) {
    $('div#statusdialog').find('img').show();
    } else{
    $('div#statusdialog').find('img').hide();
    }
    if (merged_options.message != '') {
    $('div#statusdialog').find('span').html(merged_options.message);
    }
    if (merged_options.show) {
    $('div#statusdialog').dialog('open');
    } else{
    setTimeout(function(){
    $('div#statusdialog').dialog('close');
    }, 1000);
    }
    };
            $('div#statusdialog').dialog({
    draggable: false,
            resizable: false,
            modal: true,
            minWidth: false,
            minHeight: false,
            width: "auto",
            autoOpen: false
    });
            $(".ui-dialog-titlebar").hide();
            $(window).resize(function() {
    $("div#statusdialog").dialog("option", "position", "center");
    });
    });</script>
<div ng-app="GI-MediaLibrary">
    <div class="clear"></div>
    <div id="statusdialog" style="-webkit-box-shadow: 2px 2px 5px #888; -moz-box-shadow: 2px 2px 5px #888; padding: 5px;">
        <div class="col"><img src="<?php echo GIML_URI . 'images/ajax-loader.gif'; ?>" style="display:none;" alt="loading" id="loading" /></div>
        <div class="col-right"><span style="font-size: 20px; line-height:37px"></span></div>
        <div class="clear"></div>
    </div>
    <div id="playlist-message" style="display: none;"></div>
    <div ng-controller="Playlists as playlists">
        <div class="col">
            Group:<br />
            <ui-select ng-model="playlists.group.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="playlists.group.change()">
                <ui-select-match placeholder="Select or search for group...">{{$select.selected.grouplabel}}</ui-select-match>
                <ui-select-choices repeat="group in playlists.group.data | filter: {grouplabel:$select.search} track by group.id">
                    <div ng-bind-html="group.grouplabel | highlight: $select.search"></div>
                </ui-select-choices>
            </ui-select>
        </div>
        <div class="col">
            Subgroup:<br />
            <ui-select ng-model="playlists.subgroup.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="playlists.subgroup.change()">
                <ui-select-match placeholder="Select or search for subgroup...">{{$select.selected.subgrouplabel | htmlToPlain}}</ui-select-match>
                <ui-select-choices repeat="subgroup in playlists.subgroup.data | filter: {subgrouplabel:$select.search} track by subgroup.id">
                    <div ng-bind-html="subgroup.subgrouplabel | highlight: $select.search"></div>
                </ui-select-choices>
            </ui-select>
        </div>
        <div class="clear"></div>
        <div style="margin: 20px 0"></div>
        <div ng-show="playlists.subgroup.selected" class="show-hide">
            <div class="form-row">
                <div class="col">
                    <label for="subgroupdownloadlink">Download link:</label><br/>
                    <input type="text" class="regular-text" id="subgroupdownloadlink" name="subgroupdownloadlink" ng-model="playlists.subgroupdownloadlink">
                </div>
                <div class="col">
                    <label for="subgroupdownloadlabel">Label:</label><br/>
                    <input type="text" id="subgroupdownloadlabel" name="subgroupdownloadlabel" ng-model="playlists.subgroupdownloadlabel">
                </div>
                <div class="col">
                    <label for="subgroupdownloadcss">CSS:</label><br/>
                    <input type="text" class="small-text" id="subgroupdownloadcss" name="subgroupdownloadcss" ng-model="playlists.subgroupdownloadcss">
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-row">
                <div class="col">
                    <label><input type="checkbox" ng-model="playlists.subgroupshowfilter" ng-true-value="1" ng-false-value="0"> Show filter</label>
                </div>
                <div class="col">
                    <label><input type="checkbox" ng-model="playlists.subgroupshowcombo" ng-true-value="1" ng-false-value="0"> Show combo box</label>
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input type="button" class="button" value="Update" ng-click="playlists.update()">
                </div>
                <div class="clear"></div>
            </div>

            <div class="accordion-container">
                <ul class="outer-border">
                    <li class="control-section accordion-section show-hide" ng-controller="Combo as combo" ng-show="playlists.subgroupshowcombo">
                        <h3 class="accordion-section-title hndle" tabindex="0" title="Combo">Combo</h3>
                        <div class="accordion-section-content">
                            <div class="inside">
                                <div class="form-row">
                                    <div class="col">
                                        <label for="playlistcombolabel">Combo label:</label><br/>
                                        <input type="text" id="playlistcombolabel" name="playlistcombolabel" ng-model="combo.playlistcombolabel">
                                    </div>
                                    <div class="col">
                                        <label for="playlistcombocss">CSS:</label><br/>
                                        <input type="text" class="small-text" id="playlistcombocss" name="playlistcombocss" ng-model="combo.playlistcombocss">
                                    </div>
                                    <div class="col">
                                        <label for="playlistcombodirection">Direction:</label><br/>
                                        <select id="playlistcombodirection" name="playlistcombodirection" ng-model="combo.playlistcombodirection">
                                            <option value="ltr" selected="selected">LTR</option>
                                            <option value="rtl">RTL</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <input type="button" class="button" value="Update" ng-click="combo.update()">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="col-container">
                                    <div id="col-right">
                                        <div class="col-wrap">
                                            <div class="form-row">
                                                <div class="col">
                                                    <label for="selectcomboitems">Items:</label> (<span ng-bind="(combo.items.data)?combo.items.filtered.length:0"></span>)<br />
                                                    <input type="search" ng-model="combo.q" placeholder="search items..." /><br/>
                                                    <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectcomboitems" name="selectcomboitems" multiple="multiple" ng-model="combo.items.selected" ng-options="item.playlistcomboitemlabel | text for item in combo.items.filtered = (combo.items.data | filter:combo.q | filter:{playlistcomboitemlabel:'!None'}:true) track by item.id"></select><br />
                                                </div>
                                                <div class="col">
                                                    <input type="button" class="button" value="Edit" ng-disabled="combo.items.selected == null || combo.items.filtered.length==0" ng-click="combo.items.edit()"/>
                                                    <input type="button" class="button" value="Delete" ng-disabled="(combo.items.selected == null || combo.items.isEditMode || combo.items.filtered.length==0)" ng-click="combo.items.delete()"/>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-wrap col">
                                                    <p>
                                                        <strong>Note:</strong><br/>
                                                        Deleting an item will also delete all it's linked data.
                                                    </p>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="col-left">
                                        <div class="col-wrap">
                                            <div class="rowentries">
                                                <div ng-repeat="row in combo.items.rows track by row.id" class="form-row repeated-item">
                                                    <div class="col"><strong ng-bind="row.id + '.'"></strong></div>
                                                    <div class="col"><label for="itemlabel{{row.id}}"><font color="#FF0000">*</font>Label:</label><br><input id="rightlabel{{row.id}}" type="text" ng-model="row.playlistcomboitemlabel"></div>
                                                    <div class="col"><label for="itemdescription{{row.id}}">Description:</label><br><textarea ui-tinymce="playlists.itemEd" id="itemdescription{{row.id}}" ng-model="row.playlistcomboitemdescription" required></textarea></div>
                                                    <div class="col"><label for="itemsortorder{{row.id}}">Order#:</label><br><input class="small-text" id="subgroupsortorder{{row.id}}" type="number" ng-model="row.playlistcomboitemsortorder"></div>
                                                    <div class="col"><label for="itemdownloadlink{{row.id}}">Download link:<br><input id="itemdownloadlink{{row.id}}" class="regular-text" type="text" ng-model="row.playlistcomboitemdownloadlink"></div>
                                                    <div class="col"><label for="itemdownloadlabel{{row.id}}">Download label:</label><br><input id="itemdownloadlabel{{row.id}}" type="text" ng-model="row.playlistcomboitemdownloadlabel"></div>
                                                    <div class="col"><label for="itemdownloadcss{{row.id}}">Download CSS:</label><br><input class="small-text" id="itemdownloadcss{{row.id}}" type="text" ng-model="row.playlistcomboitemdownloadcss"></div>
                                                    <div class="col"><label><input type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="row.playlistcomboitemdefault"> Default</label></div>
                                                    <div class="col">
                                                        <div class="remove-row">
                                                            <p>
                                                                <img src="<?php echo GIML_URI . 'images/delete-icon.png'; ?>" alt="remove row" title="remove row" ng-show="combo.items.rows.length > 1" ng-click="combo.items.removeRow(row.id)">
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="clear"></div>
                                                    </div>
                                                </div>
                                                <div class="form-row" ng-hide="combo.items.isEditMode">
                                                    <div class="insert-row">
                                                        <p><span ng-click="combo.items.insertRow()"><img src="<?php echo GIML_URI . 'images/add-icon.png'; ?>" alt="insert row" title="insert row">&nbsp;insert row</span></p>
                                                    </div>
                                                </div>
                                                <div class="form-row" ng-hide="combo.items.rows.length == 0">
                                                    <input type="button" class="button button-primary" ng-value="(combo.items.isEditMode)?'Update':'Add'" ng-click="combo.items.update()"/>
                                                    <input type="button" class="button" value="Cancel" ng-click="combo.items.cancel()" ng-show="combo.items.isEditMode"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br class="clear"/>
                                </div>
                            </div>
                    </li>
                    <li class="control-section accordion-section" ng-controller="Table as table">
                        <h3 class="accordion-section-title hndle" tabindex="0" title="Table">Table (ID: <span ng-bind-html="playlists.data.table[0].id"></span>)</h3>
                        <div class="accordion-section-content">
                            <div class="inside">
                                <div class="form-row">
                                    <div class="col">
                                        <label for="tablecss">CSS:</label><br/>
                                        <input type="text" class="small-text" id="tablecss" name="tablecss" ng-model="table.playlisttablecss">
                                    </div>
                                    <div class="col">
                                        <input type="button" class="button" value="Update" ng-click="table.update()">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="col-container">
                                    <div id="col-right">
                                        <div class="col-wrap">
                                            <div class="form-row">
                                                <div class="col">
                                                    <label for="selectcolumns">Columns:</label> (<span ng-bind="(table.columns.data)?table.columns.filtered.length:0"></span>)<br />
                                                    <input type="search" ng-model="table.q" placeholder="search columns..." /><br/>
                                                    <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectcolumns" name="selectcolumns" multiple="multiple" ng-model="table.columns.selected" ng-options="column.playlisttablecolumnlabel | text for column in table.columns.filtered = (table.columns.data | filter:table.q) track by column.id"></select><br />
                                                </div>
                                                <div class="col">
                                                    <input type="button" class="button" value="Edit" ng-disabled="table.columns.selected == null || table.columns.filtered.length==0" ng-click="table.columns.edit()"/>
                                                    <input type="button" class="button" value="Delete" ng-disabled="(table.columns.selected == null || table.columns.isEditMode || table.columns.filtered.length==0)" ng-click="table.columns.delete()"/>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-wrap col">
                                                    <p>
                                                        <strong>Note:</strong><br/>
                                                        Deleting a column will also delete all it's linked data.
                                                    </p>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="col-left">
                                        <div class="col-wrap">
                                            <div class="rowentries">
                                                <div ng-repeat="row in table.columns.rows track by row.id" class="form-row repeated-item">
                                                    <div class="col"><strong ng-bind="row.id + '.'"></strong></div>
                                                    <div class="col"><label for="columnlabel{{row.id}}"><font color="#FF0000">*</font>Column:</label><br><input id="columnlabel{{row.id}}" type="text" ng-model="row.playlisttablecolumnlabel"></div>
                                                    <div class="col"><label for="columncss{{row.id}}">CSS:</label><br><input class="small-text" id="columncss{{row.id}}" type="text" ng-model="row.playlisttablecolumncss"></div>
                                                    <div class="col">
                                                        <label for="columndirection{{row.id}}">Direction:</label><br>
                                                        <select id="columndirection{{row.id}}" ng-model="row.playlisttablecolumndirection">
                                                            <option value="ltr">LTR</option>
                                                            <option value="rtl">RTL</option>
                                                        </select>
                                                    </div>
                                                    <div class="col"><label for="columnsortorder{{row.id}}">Order#:</label><br><input class="small-text" id="columnsortorder{{row.id}}" type="number" ng-model="row.playlisttablecolumnsortorder"></div>
                                                    <div class="col">
                                                        <label for="columntype{{row.id}}">Column type:</label><br>
                                                        <select id="columntype{{row.id}}" name="columntype{{row.id}}" ng-model="row.playlisttablecolumntype">
                                                            <option value="text" selected="selected">Text</option>
                                                            <option value="link">Link</option>
                                                            <option value="iconiclink">Iconic Link</option>
                                                            <option value="audio">Audio/Video</option>
                                                            <option value="video">External Video</option>
                                                            <option value="download">Download</option>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <div class="remove-row">
                                                            <p>
                                                                <img src="<?php echo GIML_URI . 'images/delete-icon.png'; ?>" alt="remove row" title="remove row" ng-show="table.columns.rows.length > 1" ng-click="table.columns.removeRow(row.id)">
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                            <div class="form-row" ng-hide="table.columns.isEditMode">
                                                <div class="insert-row">
                                                    <p><span ng-click="table.columns.insertRow()"><img src="<?php echo GIML_URI . 'images/add-icon.png'; ?>" alt="insert row" title="insert row">&nbsp;insert row</span></p>
                                                </div>
                                            </div>
                                            <div class="form-row" ng-hide="table.columns.rows.length == 0">
                                                <input type="button" class="button button-primary" ng-value="(table.columns.isEditMode)?'Update':'Add'" ng-click="table.columns.update()"/>
                                                <input type="button" class="button" value="Cancel" ng-click="table.columns.cancel()" ng-show="table.columns.isEditMode"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br class="clear"/>
                            </div>
                        </div>
                    </li>
                    <li class="control-section accordion-section" ng-controller="Section as section">
                        <h3 class="accordion-section-title hndle" tabindex="0" title="Table Sections">Table Sections</h3>
                        <div class="accordion-section-content">
                            <div class="inside">
                                <div id="col-container">
                                    <div id="col-right">
                                        <div class="col-wrap">
                                            <div class="form-row">
                                                <div class="col">
                                                    Combo Item:<br />
                                                    <ui-select ng-model="section.comboItem.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="section.comboItem.change()">
                                                        <ui-select-match placeholder="Select or search for combo item...">{{$select.selected.playlistcomboitemlabel}}</ui-select-match>
                                                        <ui-select-choices repeat="item in section.comboItem.data | filter: {playlistcomboitemlabel:$select.search} track by item.id">
                                                          <div ng-bind-html="item.playlistcomboitemlabel | highlight: $select.search"></div>
                                                        </ui-select-choices>
                                                    </ui-select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <label for="selectsections">Sections:</label> (<span ng-bind="(section.data)?section.filtered.length:0"></span>)<br />
                                                    <input type="search" ng-model="section.q" placeholder="search sections..." /><br/>
                                                    <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectsections" name="selectsections" multiple="multiple" ng-model="section.selected" ng-options="item.playlistsectionlabel | htmlToPlain for item in section.filtered = (section.data | filter:section.q) track by item.id"></select><br />
                                                </div>
                                                <div class="col">
                                                    <input type="button" class="button" value="Edit" ng-disabled="section.selected == null || section.filtered.length==0" ng-click="section.edit()"/>
                                                    <input type="button" class="button" value="Delete" ng-disabled="(section.selected == null || section.isEditMode || section.filtered.length==0)" ng-click="section.delete()"/>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-wrap col">
                                                    <p>
                                                        <strong>Note:</strong><br/>
                                                        Deleting a section will also delete all it's linked data.
                                                    </p>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="col-left" style="overflow-y: visible;">
                                        <div class="col-wrap" style="min-height: 320px;">
                                            <div class="rowentries">
                                                <div ng-repeat="row in section.rows track by row.id" class="form-row repeated-item">
                                                    <div class="col"><strong ng-bind="row.id + '.'"></strong></div>
                                                    <div class="col">Combo Item:<br/>
                                                        <ui-select ng-model="row.playlistsectioncomboitem" theme="select2" ng-disabled="disabled" style="width: 18em">
                                                            <ui-select-match placeholder="Select or search for combo item...">{{$select.selected.playlistcomboitemlabel}}</ui-select-match>
                                                            <ui-select-choices repeat="item in section.comboItem.data | filter: {playlistcomboitemlabel:$select.search} track by item.id">
                                                              <div ng-bind-html="item.playlistcomboitemlabel | highlight: $select.search"></div>
                                                            </ui-select-choices>
                                                        </ui-select>
                                                    </div>
                                                    <div class="col"><label for="sectionlabel{{row.id}}"><font color="#FF0000">*</font>Section:</label><br><textarea ui-tinymce="playlists.itemEd" id="sectionlabel{{row.id}}" ng-model="row.playlistsectionlabel" required></textarea></div>
                                                    <div class="col"><label for="sectioncss{{row.id}}">CSS:</label><br><input class="small-text" id="sectioncss{{row.id}}" type="text" ng-model="row.playlistsectioncss"></div>
                                                    <div class="col"><label for="sectionsortorder{{row.id}}">Order#:</label><br><input class="small-text" id="sectionsortorder{{row.id}}" type="number" ng-model="row.playlistsectionsortorder"></div>
                                                    <div class="col">
                                                        <label for="sectiondirection{{row.id}}">Direction:</label><br>
                                                        <select id="sectiondirection{{row.id}}" ng-model="row.playlistsectiondirection">
                                                            <option value="ltr">LTR</option>
                                                            <option value="rtl">RTL</option>
                                                        </select>
                                                    </div>
                                                    <div class="col"><label for="sectiondownloadlink{{row.id}}">Download link:<br><input id="sectiondownloadlink{{row.id}}" class="regular-text" type="text" ng-model="row.playlistsectiondownloadlink"></div>
                                                    <div class="col"><label for="sectiondownloadlabel{{row.id}}">Download label:</label><br><input id="sectiondownloadlabel{{row.id}}" type="text" ng-model="row.playlistsectiondownloadlabel"></div>
                                                    <div class="col"><label for="sectiondownloadcss{{row.id}}">Download CSS:</label><br><input class="small-text" id="sectiondownloadcss{{row.id}}" type="text" ng-model="row.playlistsectiondownloadcss"></div>
                                                    <div class="col"><label><input type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="row.playlistsectionhide"> Hide</label></div>
                                                    <div class="col">
                                                        <div class="remove-row">
                                                            <p>
                                                                <img src="<?php echo GIML_URI . 'images/delete-icon.png'; ?>" alt="remove row" title="remove row" ng-show="section.rows.length > 1" ng-click="section.removeRow(row.id)">
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                            <div class="form-row" ng-hide="section.isEditMode">
                                                <div class="insert-row">
                                                    <p><span ng-click="section.insertRow()"><img src="<?php echo GIML_URI . 'images/add-icon.png'; ?>" alt="insert row" title="insert row">&nbsp;insert row</span></p>
                                                </div>
                                            </div>
                                            <div class="form-row" ng-hide="section.rows.length == 0">
                                                <input type="button" class="button button-primary" ng-value="(section.isEditMode)?'Update':'Add'" ng-click="section.update()"/>
                                                <input type="button" class="button" value="Cancel" ng-click="section.cancel()" ng-show="section.isEditMode"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br class="clear"/>
                            </div>
                        </div>
                    </li>
                    <li class="control-section accordion-section" ng-controller="SectionColumn as seccolumn">
                        <h3 class="accordion-section-title hndle" tabindex="0" title="Section Columns Data">Section Columns Data</h3>
                        <div class="accordion-section-content">
                            <div class="inside">
                                <div id="col-container">
                                    <div id="col-right">
                                        <div class="col-wrap">
                                            <div class="form-row">
                                                <div class="col">
                                                    Combo Item:<br />
                                                    <ui-select ng-model="seccolumn.comboItem.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="seccolumn.comboItem.change()">
                                                        <ui-select-match placeholder="Select or search for combo item...">{{$select.selected.playlistcomboitemlabel | htmlToPlain}}</ui-select-match>
                                                        <ui-select-choices repeat="item in seccolumn.comboItem.data | filter: {playlistcomboitemlabel:$select.search} track by item.id">
                                                          <div ng-bind-html="item.playlistcomboitemlabel | highlight: $select.search"></div>
                                                        </ui-select-choices>
                                                    </ui-select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    Section:<br />
                                                    <ui-select ng-model="seccolumn.section.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="seccolumn.section.change()">
                                                        <ui-select-match placeholder="Select or search for section...">{{$select.selected.playlistsectionlabel | htmlToPlain}}</ui-select-match>
                                                        <ui-select-choices repeat="item in seccolumn.section.data | filter: {playlistsectionlabel:$select.search} track by item.id">
                                                          <div ng-bind-html="item.playlistsectionlabel | highlight: $select.search"></div>
                                                        </ui-select-choices>
                                                    </ui-select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <label for="selectsectioncolumns">Section Columns:</label> (<span ng-bind="(seccolumn.data1)?seccolumn.data1.length:0"></span>)<br />
                                                    <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectsectioncolumns" name="selectsectioncolumns" multiple="multiple" ng-model="seccolumn.selected" ng-options="item.data for item in seccolumn.data1 track by item.id"></select><br />
                                                </div>
                                                <div class="col">
                                                    <input type="button" class="button" value="Edit" ng-disabled="seccolumn.selected == null" ng-click="seccolumn.edit()"/>
                                                    <input type="button" class="button" value="Delete" ng-disabled="(seccolumn.selected == null || seccolumn.isEditMode)" ng-click="seccolumn.delete()"/>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="col-left" style="overflow-y: visible;">
                                        <div class="col-wrap" style="min-height: 320px;">
                                            <giml-table combo-items="seccolumn.comboItem.data" table-columns="seccolumn.tableColumns.data" 
                                                      row-table-sections="seccolumn.rowSections" edit="seccolumn.isEditMode" 
                                                      on-save="seccolumn.save(rows)" on-item-change="seccolumn.rowItemChange(id, rowid)"
                                                      rows-data="seccolumn.rowsData"/>
                                        </div>
                                    </div>
                                </div>
                                <br class="clear"/>
                            </div>
                        </div>
                    </li>
                    <li class="control-section accordion-section" ng-controller="Column as column">
                        <h3 class="accordion-section-title hndle" tabindex="0" title="Columns Data">Columns Data</h3>
                        <div class="accordion-section-content">
                            <div class="inside">
                                <div id="col-container">
                                    <div id="col-right">
                                        <div class="col-wrap">
                                            <div class="form-row">
                                                <div class="col">
                                                    Combo Item:<br />
                                                    <ui-select ng-model="column.comboItem.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="column.comboItem.change()">
                                                        <ui-select-match placeholder="Select or search for combo item...">{{$select.selected.playlistcomboitemlabel | htmlToPlain}}</ui-select-match>
                                                        <ui-select-choices repeat="item in column.comboItem.data | filter: {playlistcomboitemlabel:$select.search} track by item.id">
                                                          <div ng-bind-html="item.playlistcomboitemlabel | highlight: $select.search"></div>
                                                        </ui-select-choices>
                                                    </ui-select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    Section:<br />
                                                    <ui-select ng-model="column.section.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="column.section.change()">
                                                        <ui-select-match placeholder="Select or search for section...">{{$select.selected.playlistsectionlabel | htmlToPlain}}</ui-select-match>
                                                        <ui-select-choices repeat="item in column.section.data | filter: {playlistsectionlabel:$select.search} track by item.id">
                                                          <div ng-bind-html="item.playlistsectionlabel | highlight: $select.search"></div>
                                                        </ui-select-choices>
                                                    </ui-select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <label for="selectcolumns">Columns data:</label> (<span ng-bind="(column.data1)?column.data1.length:0"></span>)<br />
                                                    <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectcolumns" name="selectcolumns" multiple="multiple" ng-model="column.selected" ng-options="item.data for item in column.data1 track by item.id"></select><br />
                                                </div>
                                                <div class="col">
                                                    <input type="button" class="button" value="Edit" ng-disabled="column.selected == null" ng-click="column.edit()"/>
                                                    <input type="button" class="button" value="Delete" ng-disabled="(column.selected == null || column.isEditMode)" ng-click="column.delete()"/>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="col-left" style="overflow-y: visible;">
                                        <div class="col-wrap" style="min-height: 320px;">
                                            <giml-table combo-items="column.comboItem.data" table-columns="column.tableColumns.data" 
                                                      row-table-sections="column.rowSections" edit="column.isEditMode" 
                                                      on-save="column.save(rows)" on-item-change="column.rowItemChange(id, rowid)"
                                                      rows-data="column.rowsData" show-sort-order="true"/>
                                        </div>
                                    </div>
                                </div>
                                <br class="clear"/>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>