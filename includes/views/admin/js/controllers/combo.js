'use strict';

giml.controller('Combo', function($rootScope, $timeout, $http, $scope) {
    this.items = {};
    this.items.selected = null;
    this.items.rows = [];
    
    var self = this;
    
    $scope.$on('playlists.loaded', function(e, data) {
        self.items.data = data.combo_items;
        self.combo_id = data.combo[0].id;
        self.playlistcombolabel = data.combo[0].playlistcombolabel;
        self.playlistcombocss = data.combo[0].playlistcombocss;
        self.playlistcombodirection = data.combo[0].playlistcombodirection;
        self.items.cancel();
    });
        
    this.update = function update() {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Updating&hellip;'});

        var row = [{
            id: self.combo_id,
            playlistcombolabel: self.playlistcombolabel,
            playlistcombocss: self.playlistcombocss,
            playlistcombodirection: self.playlistcombodirection
        }];
        var data = {
            action: 'giml_combo_update',
            data: row,
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                //self.cancel();
            } else {

            }
            jQuery('#playlist-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.items.insertRow = function insertRow() {
        var newid = (self.items.rows.length > 0) ? self.items.rows[self.items.rows.length - 1].id + 1 : 1;
        self.items.rows.push({id: newid, playlistcomboid:self.combo_id, playlistcomboitemlabel: '', playlistcomboitemdescription: '', playlistcomboitemsortorder:newid * 10, 
            playlistcomboitemdownloadlink: '', playlistcomboitemdownloadlabel: '', playlistcomboitemdownloadcss: '', playlistcomboitemdefault:0});
    };
    
    this.items.removeRow = function removeRow(id) {
        ld.remove(self.items.rows, function(row) {
            return row.id == id;
        });
    };
    
    this.items.edit = function edit() {
        self.items.isEditMode = true;
        $timeout(function(){
            self.items.rows = angular.copy(self.items.selected);
        });
    };
    
    this.items.cancel = function cancel(){
        self.items.isEditMode = false;
        self.items.selected = null;
        self.items.rows = [];
    };
    
    this.items.update = function itemsUpdate() {
        jQuery('#playlist-message').html("").hide();
        
        var tmpRows = angular.copy(self.items.rows);
        ld.remove(tmpRows, function(row){
            return (ld.isEmpty(ld.trim(row.playlistcomboitemlabel)));
        });
        if (tmpRows.length == 0) {
            return false;
        }
        
        if (self.items.isEditMode) {
            jQuery.showStatusDialog({'message': 'Updating combo item(s)&hellip;'});
        }else{
            jQuery.showStatusDialog({'message': 'Adding combo item(s)&hellip;'});
        }
        
        var data = {
            action: (self.items.isEditMode)?'giml_combo_items_update':'giml_combo_items_add',
            rows: tmpRows,
            subgroupid: $scope.playlists.subgroup.selected['id'],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.items.cancel();
                self.items.data = tmpData.data.items;
                $rootScope.$broadcast('combo.items.updated', tmpData.data);
            }else{

            }
            
            jQuery('#playlist-message').html(tmpData.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
        
    };
    
    this.items.delete = function itemsDelete() {
        jQuery('#playlist-message').html("").hide();
        
        if (self.items.selected == null) {
            return false;
        }
        jQuery.showStatusDialog({message: 'Deleting combo item(s)&hellip;'});
        
        var data = {
            action: 'giml_combo_items_delete',
            ids: ld.pluck(self.items.selected, 'id').join(","),
            subgroupid: $scope.playlists.subgroup.selected['id'],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.items.cancel();
                self.items.data = tmpData.data.items;
                $rootScope.$broadcast('combo.items.updated', tmpData.data);
            }else{

            }
            
            jQuery('#playlist-message').html(tmpData.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
});
