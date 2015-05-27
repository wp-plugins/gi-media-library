'use strict';

giml.controller('Table', function($rootScope, $timeout, $http, $scope) {
    this.columns = {};
    this.columns.selected = null;
    this.columns.rows = [];
    
    var self = this;
    
    $scope.$on('playlists.loaded', function(e, data) {
        self.columns.data = data.table_columns;
        if(data.combo) {
            self.table_id = data.table[0].id;
            self.playlisttablecss = data.table[0].playlisttablecss;
            self.columns.cancel();
        }else{
            self.cancel();
        }
    });
    
    this.cancel = function cancel() {
        self.table_id = null;
        self.playlisttablecss = null;
        self.columns.cancel();
    };
    
    this.update = function update() {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Updating&hellip;'});

        var row = [{
            id: self.table_id,
            playlisttablecss: self.playlisttablecss,
        }];
        var data = {
            action: 'giml_table_update',
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
    
    this.columns.insertRow = function insertRow() {
        var newid = (self.columns.rows.length > 0) ? self.columns.rows[self.columns.rows.length - 1].id + 1 : 1;
        self.columns.rows.push({id: newid, playlisttableid:self.table_id, playlisttablecolumnlabel: '', playlisttablecolumncss: '', playlisttablecolumndirection:'ltr', 
            playlisttablecolumnsortorder: newid * 10, playlisttablecolumntype: 'text'});
    };
    
    this.columns.removeRow = function removeRow(id) {
        ld.remove(self.columns.rows, function(row) {
            return row.id == id;
        });
    };
    
    this.columns.edit = function edit() {
        self.columns.isEditMode = true;
        $timeout(function(){
            self.columns.rows = angular.copy(self.columns.selected);
        });
    };
    
    this.columns.cancel = function cancel(){
        self.columns.isEditMode = false;
        self.columns.selected = null;
        self.columns.rows = [];
    };
    
    this.columns.update = function columnsUpdate() {
        jQuery('#playlist-message').html("").hide();
        
        var tmpRows = angular.copy(self.columns.rows);
        ld.remove(tmpRows, function(row){
            return (ld.isEmpty(ld.trim(row.playlisttablecolumnlabel)));
        });
        if (tmpRows.length == 0) {
            return false;
        }
        
        if (self.columns.isEditMode) {
            jQuery.showStatusDialog({'message': 'Updating table column(s)&hellip;'});
        }else{
            jQuery.showStatusDialog({'message': 'Adding table column(s)&hellip;'});
        }
        
        var data = {
            action: (self.columns.isEditMode)?'giml_table_columns_update':'giml_table_columns_add',
            rows: tmpRows,
            subgroupid: $scope.playlists.subgroup.selected['id'],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.columns.cancel();
                self.columns.data = tmpData.data.columns;
                $rootScope.$broadcast('table.columns.updated', tmpData.data);
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
    
    this.columns.delete = function columnsDelete() {
        jQuery('#playlist-message').html("").hide();
        
        if (self.columns.selected == null) {
            return false;
        }
        jQuery.showStatusDialog({message: 'Deleting table column(s)&hellip;'});
        
        var data = {
            action: 'giml_table_columns_delete',
            ids: ld.pluck(self.columns.selected, 'id').join(","),
            subgroupid: $scope.playlists.subgroup.selected['id'],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.columns.cancel();
                self.columns.data = tmpData.data.columns;
                $rootScope.$broadcast('table.columns.updated', tmpData.data);
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
