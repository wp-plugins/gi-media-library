'use strict';

giml.controller('Groups', function($rootScope, $timeout, $http) {
    this.rows = [];
    this.data = gimlData.groups;
    this.selected = null;
    this.isEditMode = false;
    
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
    
    this.insertRow = function insertRow() {
        var newid = (self.rows.length > 0) ? self.rows[self.rows.length - 1].id + 1 : 1;
        self.rows.push({id: newid, grouplabel: '', grouprightlabel: '', groupleftlabel:'', groupcss: '', groupdirection: 'ltr'});
        
    };
    
    this.removeRow = function removeRow(id) {
        ld.remove(self.rows, function(row) {
            return row.id == id;
        });
        /*if (self.rows.length == 1) {
            self.rows[0].id = 1;
        }*/
    };
    
    this.update = function update() {
        jQuery('#group-message').html("").hide();
        
        var tmpRows = angular.copy(self.rows);
        ld.remove(tmpRows, function(row){
            return (ld.isEmpty(ld.trim(row.grouplabel)));
        });
        if (tmpRows.length == 0) {
            return false;
        }
        
        if (self.isEditMode) {
            jQuery.showStatusDialog({'message': 'Updating group(s)&hellip;'});
        }else{
            jQuery.showStatusDialog({'message': 'Adding group(s)&hellip;'});
        }
        
        var data = {
            action: (self.isEditMode)?'giml_group_update':'giml_group_add',
            rows: tmpRows,
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.cancel();
                self.data = tmpData.data.groups;
                $rootScope.$broadcast('group.updated', tmpData.data);
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
    
    this.grpDelete = function grpDelete() {
        jQuery('#group-message').html("").hide();
        
        if (self.selected == null) {
            return false;
        }
        jQuery.showStatusDialog({message: 'Deleting group(s)&hellip;'});
        
        var data = {
            action: 'giml_group_delete',
            ids: ld.pluck(self.selected, 'id').join(","),
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.cancel();
                self.data = tmpData.data.groups;
                $rootScope.$broadcast('group.updated', tmpData.data);
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
});
/*.directive('groupRowsLoaded', function() {
    return function(scope, element, attrs) {
        if (angular.isUndefined(scope.row.id)){
            scope.row.id = scope.$index + 1;
        }
        scope.row.grouplabel = ld.stripslashes(scope.row.grouplabel);
    };
});*/