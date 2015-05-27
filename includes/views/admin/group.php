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
                $( 'div#statusdialog' ).find('img').show();
            }else{
                $( 'div#statusdialog' ).find('img').hide();
            }
            if (merged_options.message != '') {
                $( 'div#statusdialog' ).find( 'span' ).html(merged_options.message);
            }
            if (merged_options.show) {
                $( 'div#statusdialog' ).dialog('open');
            }else{
                setTimeout(function(){
                    $( 'div#statusdialog' ).dialog('close');
                },1000);
            }
        };
        $( 'div#statusdialog' ).dialog({
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
    });
</script>
<div ng-app="GI-MediaLibrary">
<div class="clear"></div>
<div id="statusdialog" style="-webkit-box-shadow: 2px 2px 5px #888; -moz-box-shadow: 2px 2px 5px #888; padding: 5px;">
    <div class="col"><img src="<?php echo GIML_URI . 'images/ajax-loader.gif'; ?>" style="display:none;" alt="loading" id="loading" /></div>
    <div class="col-right"><span style="font-size: 20px; line-height:37px"></span></div>
    <div class="clear"></div>
</div>
<div id="group-message" style="display: none;"></div>
<div class="accordion-container">
    <ul class="outer-border">
        <li class="control-section accordion-section <?php echo ($menu === 'groups') ? 'open' : '' ?>" ng-controller="Groups as groups">
            <h3 class="accordion-section-title hndle" tabindex="0" title="Groups">Groups</h3>
            <div class="accordion-section-content">
                <div class="inside">
                    <div id="col-container">
                        <div id="col-right">
                            <div class="col-wrap">
                                <div class="form-row">
                                    <div class="col">
                                        <label for="selectgroups">Groups:</label> (<span ng-bind="(groups.data)?groups.filtered.length:0"></span>)<br />
                                        <input type="search" ng-model="groups.q" placeholder="search groups..." /><br/>
                                        <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectgroups" name="selectgroups" multiple="multiple" ng-model="groups.selected" ng-options="group.grouplabel | htmlToPlain for group in groups.filtered = (groups.data | filter:{grouplabel:'!None'}:true | filter:groups.q) track by group.id"></select><br />
                                    </div>
                                    <div class="col">
                                        <input type="button" class="button" value="Edit" ng-disabled="groups.selected == null || groups.filtered.length==0" ng-click="groups.edit()"/>
                                        <input type="button" class="button" value="Delete" ng-disabled="(groups.selected == null || groups.isEditMode || groups.filtered.length==0)" ng-click="groups.grpDelete()"/>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-row">
                                    <div class="form-wrap col">
                                        <p>
                                            <strong>Note:</strong><br/>
                                            Deleting a group will also delete all it's subgroups & playlists.
                                        </p>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                        <div id="col-left">
                            <div class="col-wrap">
                                <div class="rowentries">
                                    <div ng-repeat="row in groups.rows track by row.id" class="form-row repeated-item">
                                        <div class="col"><strong ng-bind="row.id + '.'"></strong></div>
                                        <div class="col"><label for="label{{row.id}}"><font color="#FF0000">*</font>Label:</label><br><textarea ui-tinymce="groups.basicEd" id="label{{row.id}}" ng-model="row.grouplabel" required></textarea></div>
                                        <div class="col"><label for="rightlabel{{row.id}}">Right Label:</label><br><input id="rightlabel{{row.id}}" type="text" ng-model="row.grouprightlabel"></div>
                                        <div class="col"><label for="leftlabel{{row.id}}">Left Label:</label><br><input id="leftlabel{{row.id}}" type="text" ng-model="row.groupleftlabel"></div>
                                        <div class="col"><label for="css{{row.id}}">CSS:</label><br><input class="small-text" id="css{{row.id}}" type="text" ng-model="row.groupcss"></div>
                                        <div class="col">
                                            <label for="direction{{row.id}}">Direction:</label><br>
                                            <select id="direction{{row.id}}" ng-model="row.groupdirection">
                                                <option value="ltr">LTR</option>
                                                <option value="rtl">RTL</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <div class="remove-row">
                                                <p>
                                                    <img src="<?php echo GIML_URI . 'images/delete-icon.png'; ?>" alt="remove row" title="remove row" ng-show="groups.rows.length > 1" ng-click="groups.removeRow(row.id)">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="form-row" ng-hide="groups.isEditMode">
                                    <div class="insert-row">
                                        <p><span ng-click="groups.insertRow()"><img src="<?php echo GIML_URI . 'images/add-icon.png'; ?>" alt="insert row" title="insert row">&nbsp;insert row</span></p>
                                    </div>
                                </div>
                                <div class="form-row" ng-hide="groups.rows.length == 0">
                                    <input type="button" class="button button-primary" ng-value="(groups.isEditMode)?'Update':'Add'" id="btngroupadd" name="btngroupadd" ng-click="groups.update()"/>
                                    <input type="button" class="button" value="Cancel" id="btncategorycancel" name="btncategorycancel" ng-click="groups.cancel()" ng-show="groups.isEditMode"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>
            </div>
        </li>
        <li class="control-section accordion-section <?php echo ($menu === 'subgroups') ? 'open' : '' ?>" ng-controller="Subgroups as subgroups">
            <h3 class="accordion-section-title hndle" tabindex="0" title="Subgroups">Subgroups</h3>
            <div class="accordion-section-content">
                <div class="inside">
                    <div id="col-container">
                        <div id="col-right">
                            <div class="col-wrap">
                                <div class="form-row">
                                    <div class="col">
                                        <label for="selectsubgroupgroup">Group:</label><br />
<!--                                        <select class="postform" style="min-width:13em; max-width:22em;" id="selectsubgroupgroup" name="selectsubgroupgroup" ng-change="subgroups.group.change()" ng-model="subgroups.group.selected" ng-options="group.grouplabel | text for group in subgroups.group.data track by group.id">
                                            <option value="" selected="selected">None</option>
                                        </select>-->
                                        <ui-select ng-model="subgroups.group.selected" theme="select2" ng-disabled="disabled" style="width: 18em" on-select="subgroups.group.change()">
                                            <ui-select-match placeholder="Select or search for group...">{{$select.selected.grouplabel}}</ui-select-match>
                                            <ui-select-choices repeat="group in subgroups.group.data | filter: {grouplabel:$select.search} track by group.id">
                                              <div ng-bind-html="group.grouplabel | highlight: $select.search"></div>
                                            </ui-select-choices>
                                        </ui-select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <label for="selectsubgroups">Subgroups:</label> (<span ng-bind="(subgroups.data)?subgroups.filtered.length:0"></span>)<br />
                                        <input type="search" ng-model="subgroups.q" placeholder="search subgroups..." /><br/>
                                        <select class="postform" style="min-width:13em; max-width:22em; height:15em;" id="selectsubgroups" name="selectsubgroups" multiple="multiple" ng-model="subgroups.selected" ng-options="subgroup.subgrouplabel | htmlToPlain for subgroup in subgroups.filtered = (subgroups.data | filter:subgroups.q) track by subgroup.id"></select><br />
                                    </div>
                                    <div class="col">
                                        <input type="button" class="button" value="Edit" ng-disabled="subgroups.selected == null || subgroups.filtered.length==0" ng-click="subgroups.edit()"/>
                                        <input type="button" class="button" value="Delete" ng-disabled="(subgroups.selected == null || subgroups.isEditMode || subgroups.filtered.length==0)" ng-click="subgroups.subgroupDelete()"/>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-row">
                                    <div class="form-wrap col">
                                        <p>
                                            <strong>Note:</strong><br/>
                                            Deleting a subgroup will also delete all it's playlists.
                                        </p>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                        <div id="col-left">
                            <div class="col-wrap">
                                <div class="rowentries">
                                    <div ng-repeat="row in subgroups.rows track by row.id" class="form-row repeated-item">
                                        <div class="col"><strong ng-bind="row.id + '.'"></strong></div>
                                        <div class="col">Link to Group(s):<br/><input type="button" class="button" value="Add/Remove Groups" ng-click="subgroups.addGroup(row.groupid, $index)"/></div>
                                        <div class="col"><label for="subgrouplabel{{row.id}}"><font color="#FF0000">*</font>Label:</label><br><textarea ui-tinymce="subgroups.basicEd" id="subgrouplabel{{row.id}}" ng-model="row.subgrouplabel" required></textarea></div>
                                        <div class="col"><label for="subgrouprightlabel{{row.id}}">Right Label:</label><br><input id="subgrouprightlabel{{row.id}}" type="text" ng-model="row.subgrouprightlabel"></div>
                                        <div class="col"><label for="subgroupleftlabel{{row.id}}">Left Label:</label><br><input id="subgroupleftlabel{{row.id}}" type="text" ng-model="row.subgroupleftlabel"></div>
                                        <div class="col"><label for="subgroupcss{{row.id}}">CSS:</label><br><input class="small-text" id="subgroupcss{{row.id}}" type="text" ng-model="row.subgroupcss"></div>
                                        <div class="col"><label for="subgroupdescription{{row.id}}">Description:</label><br><textarea ui-tinymce="subgroups.itemEd" id="subgroupdescription{{row.id}}" ng-model="row.subgroupdescription"></textarea></div>
                                        <div class="col"><label for="subgroupsortorder{{row.id}}">Order#:</label><br><input class="small-text" id="subgroupsortorder{{row.id}}" type="number" ng-model="row.subgroupsortorder"></div>
                                        <div class="col">
                                            <label for="subgroupdirection{{row.id}}">Direction:</label><br>
                                            <select id="subgroupdirection{{row.id}}" ng-model="row.subgroupdirection">
                                                <option value="ltr">LTR</option>
                                                <option value="rtl">RTL</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <div class="remove-row">
                                                <p>
                                                    <img src="<?php echo GIML_URI . 'images/delete-icon.png'; ?>" alt="remove row" title="remove row" ng-show="subgroups.rows.length > 1" ng-click="subgroups.removeRow(row.id)">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="form-row" ng-hide="subgroups.isEditMode">
                                    <div class="insert-row">
                                        <p><span ng-click="subgroups.insertRow()"><img src="<?php echo GIML_URI . 'images/add-icon.png'; ?>" alt="insert row" title="insert row">&nbsp;insert row</span></p>
                                    </div>
                                </div>
                                <div class="form-row" ng-hide="subgroups.rows.length == 0">
                                    <input type="button" class="button button-primary" ng-value="(subgroups.isEditMode)?'Update':'Add'" ng-click="subgroups.update()"/>
                                    <input type="button" class="button" value="Cancel" ng-click="subgroups.cancel()" ng-show="subgroups.isEditMode"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="add-groups" style="display: none">
                        <div class="form-row">
                            <div class="col">
                                <ui-select multiple ng-model="subgroups.tmpNewGroup.selected" theme="select2" ng-disabled="disabled" style="width: 37em">
                                    <ui-select-match placeholder="Select or search for group...">{{$item.grouplabel}}</ui-select-match>
                                    <ui-select-choices repeat="group in subgroups.group.data | filter:{id:'!None', grouplabel: $select.search} track by group.id">
                                      <div ng-bind-html="group.grouplabel | highlight: $select.search"></div>
                                    </ui-select-choices>
                                </ui-select>
                            </div>
                            <div class="clear"></div>
                        </div>
<!--                        <div class="form-row">
                            <div class="col">
                                <label for="addgrpnewgroup">Selected Groups:</label> (<span ng-bind="(subgroups.tmpNewGroup.data)?subgroups.tmpNewGroup.data.length:0"></span>)<br />
                                <select id="addgrpnewgroup" name="addgrpnewgroup" class="postform" style="width:15em; height:20em;" multiple="multiple" ng-model="subgroups.tmpNewGroup.selected" ng-options="group.grouplabel | text for group in subgroups.tmpNewGroup.data | filter:{id:'!0'} track by group.id"></select>
                            </div>
                            <div class="col" style="padding-top: 10em;">
                                <input type="button" class="button" value="&#8594;" title="remove" ng-disabled="subgroups.tmpNewGroup.selected == null" ng-click="subgroups.tmpAddGroup.add()"/><br/>
                                <input type="button" class="button" value="&#8592;" title="add" ng-disabled="subgroups.tmpAddGroup.selected == null" ng-click="subgroups.tmpNewGroup.add()"/>
                            </div>
                            <div class="col">
                                <label for="addgrpaddgroup">Groups:</label> (<span ng-bind="(subgroups.tmpAddGroup.data)?subgroups.tmpAddGroup.data.length:0"></span>)<br />
                                <input type="search" ng-model="subgroups.tmpAddGroup.q" placeholder="search groups..."/><br/>
                                <select id="addgrpaddgroup" name="addgrpaddgroup" class="postform" style="width:15em; height:20em" multiple="multiple" ng-model="subgroups.tmpAddGroup.selected" ng-options="group.grouplabel | text for group in subgroups.tmpAddGroup.data | filter:{id:'!0'} | filter:subgroups.tmpAddGroup.q track by group.id"></select>
                            </div>
                            <div class="clear"></div>
                        </div>-->
                        <div class="form-row">
                            <input type="button" class="button button-primary" value="Save" ng-click="subgroups.tmpNewGroup.save()" ng-disabled="!subgroups.tmpNewGroup.selected"/>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>
            </div>
        </li>
    </ul>
</div>
</div>
        