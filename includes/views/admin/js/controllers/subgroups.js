'use strict';

giml.controller('Subgroups', function($rootScope, $timeout, $http, $scope) {
    this.group = {};
    this.group.data = gimlData.groups;
    this.group.selected = null;//(this.group.data) ? this.group.data[0] : [];
    
    this.rows = [];//[{id: 1, groupid:0, subgrouplabel: '', subgrouprightlabel: '', subgroupleftlabel:'', subgroupcss:'', subgroupdescription:'', subgroupsortorder:10, subgroupdirection: 'ltr'}];
    this.data = [];
    this.selected = null;
    this.isEditMode = false;
    this.selectedRowIndex = null;
    this.tmpAddGroup = {};
    this.tmpAddGroup.data = null;
    this.tmpAddGroup.selected = null;
    
    this.tmpNewGroup = {};
    this.tmpNewGroup.data = null;
    this.tmpNewGroup.selected = null;
    
    var self = this;
    
    this.basicEd = {
        menubar: false,
      	plugins: [
            "advlist lists charmap print preview anchor",
            "searchreplace visualblocks code",
            "insertdatetime media paste"
        ],
      	toolbar: "undo redo | styleselect | bold italic underline | code",
        width: 350,
        height: 150,
        forced_root_block: false
    };
    
    this.itemEd = {
        menubar: false,
      	plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media contextmenu paste"
        ],
      	toolbar: "undo redo | styleselect | bold italic underline | link image | code fullscreen",
        width: 350,
        height: 150
    };
    
    $scope.$on('group.updated', function(e, data) {
        self.group.data = data.groups;
        self.group.selected = null;
        self.data = null;
        //self.group.change();
        self.cancel();
    });
    
    this.insertRow = function insertRow() {
        var newid = (self.rows.length > 0) ? self.rows[self.rows.length - 1].id + 1 : 1;
        self.rows.push({id: newid, groupid: 0, subgrouplabel: '', subgrouprightlabel: '', subgroupleftlabel:'', subgroupcss:'', subgroupdescription:'', subgroupsortorder: newid * 10, subgroupdirection: 'ltr'});
    };
    
    this.removeRow = function removeRow(id) {
        ld.remove(self.rows, function(row) {
            return row.id == id;
        });
        
        /*if (self.rows.length == 1) {
            self.rows[0].id = 1;
        }*/
    };
    
    this.group.change = function changeGroup() {
        jQuery('#group-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Retrieving subgroups&hellip;'});

        var data = {
            action: 'giml_group_subgroups_get',
            groupid: (angular.isObject(self.group.selected))?self.group.selected["id"]:"",
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.cancel();
            } else {

            }
            $timeout(function() {
                self.data = ld.sortBy(response.data.data, "subgroupsortorder");
            });
            jQuery('#group-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#group-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.edit = function edit() {
        self.isEditMode = true;
        $timeout(function(){
            self.rows = angular.copy(self.selected);
        });        
    };
    
    this.cancel = function cancel(){
        self.isEditMode = false;
        self.selected = null;
        self.rows = [];
    };
    
    this.addGroup = function addGroup(groupid, rowindex) {
        tb_show('Add/Remove Groups', '#TB_inline?inlineId=add-groups&width=530&height=400');
        
        self.tmpNewGroup.selected = null;
        self.selectedRowIndex = rowindex;
        /*self.tmpAddGroup.q = null;
        self.tmpAddGroup.selected = null;
        
        if (angular.isArray(self.group.data)) {
            self.tmpAddGroup.data = angular.copy(self.group.data);
            self.tmpAddGroup.data.shift();
            groupid = angular.isArray(angular.fromJson(groupid))?angular.fromJson(groupid):angular.fromJson("[" + groupid + "]");
            //groupid = groupid.split(",");
            self.tmpNewGroup.data = ld.filter(self.tmpAddGroup.data, function(grp){
                //if (angular.isArray(groupid))
                    return ld.contains(groupid, grp['id']);
                //else
                //    return grp.id == groupid;
            });
            ld.remove(self.tmpAddGroup.data, function(grp){
                //if (angular.isArray(groupid))
                    return ld.contains(groupid, grp['id']);
                //else
                //    return grp.id == groupid;
            });
        }*/
        // NEW CODE
        if (self.group.data) {
            self.tmpAddGroup.data = angular.copy(self.group.data);
            self.tmpAddGroup.data.shift();
            groupid = angular.isArray(angular.fromJson(groupid))?angular.fromJson(groupid):angular.fromJson("[" + groupid + "]");
            self.tmpNewGroup.selected = ld.filter(self.tmpAddGroup.data, function(grp){
                return ld.contains(groupid, grp['id']);
            });
        }
    };
    
    this.update = function update() {
        jQuery('#group-message').html("").hide();
        
        var tmpRows = angular.copy(self.rows);
        ld.remove(tmpRows, function(row){
            return (ld.isEmpty(ld.trim(row.subgrouplabel)));
        });
        if (tmpRows.length == 0) {
            return false;
        }
        
        if (self.isEditMode) {
            jQuery.showStatusDialog({'message': 'Updating subgroup(s)&hellip;'});
        }else{
            jQuery.showStatusDialog({'message': 'Adding subgroup(s)&hellip;'});
        }
        
        var data = {
            action: (self.isEditMode)?'giml_subgroup_update':'giml_subgroup_add',
            rows: tmpRows,
            groupid: (angular.isObject(self.group.selected))?self.group.selected["id"]:"",
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.cancel();
                self.data = ld.sortBy(tmpData.data.subgroups, "subgroupsortorder");
            }else{

            }
            
            jQuery('#group-message').html(tmpData.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#group-message', 800, {offset: -35});
            $timeout(function(){
                /*if (response.data.success){
                    $rootScope.$broadcast('group.updated', tmpData.data);
                }*/
            }, 500);
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.tmpNewGroup.add = function newGrpAdd() {
        self.tmpNewGroup.data.push(self.tmpAddGroup.selected);
        self.tmpNewGroup.data = ld.flatten(self.tmpNewGroup.data);
        
        ld.remove(self.tmpAddGroup.data, function(grp){
            return ld.contains(ld.pluck(self.tmpNewGroup.data, 'id'), grp['id']);
        });
        self.tmpAddGroup.selected = null;
    };
    
    this.tmpAddGroup.add = function addGrpAdd() {
        self.tmpAddGroup.data.push(self.tmpNewGroup.selected);
        self.tmpAddGroup.data = ld.flatten(self.tmpAddGroup.data);
        
        ld.remove(self.tmpNewGroup.data, function(grp){
            return ld.contains(ld.pluck(self.tmpAddGroup.data, 'id'), grp['id']);
        });
        self.tmpNewGroup.selected = null;
    };
    
    this.tmpNewGroup.save = function newGrpSave() {
        
        /*if (self.tmpNewGroup.data.length == 0)
            self.rows[self.selectedRowIndex].groupid = angular.toJson([0]);
        else {
            var tmpid = ld.pluck(self.tmpNewGroup.data, 'id');
            self.rows[self.selectedRowIndex].groupid = angular.toJson(tmpid);
        }*/
        if (self.tmpNewGroup.selected.length == 0)
            self.rows[self.selectedRowIndex].groupid = angular.toJson([0]);
        else {
            var tmpid = ld.pluck(self.tmpNewGroup.selected, 'id');
            self.rows[self.selectedRowIndex].groupid = angular.toJson(tmpid);
        }
        
        tb_remove();
    };
    
    this.subgroupDelete = function subgroupDelete() {
        jQuery('#group-message').html("").hide();
        
        if (self.selected == null) {
            return false;
        }
        jQuery.showStatusDialog({message: 'Deleting subgroup(s)&hellip;'});
        
        var data = {
            action: 'giml_subgroup_delete',
            ids: ld.pluck(self.selected, 'id').join(","),
            groupid: (angular.isObject(self.group.selected))?self.group.selected["id"]:"",
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.cancel();
                self.data = ld.sortBy(tmpData.data.subgroups, 'subgroupsortorder');
            }else{

            }
            
            jQuery('#group-message').html(tmpData.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#group-message', 800, {offset: -35});
            
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
        
    };
});