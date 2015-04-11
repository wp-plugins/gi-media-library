'use strict';

giml.controller('Section', function($rootScope, $timeout, $http, $scope) {
    this.comboItem = {};
    this.comboItem.selected = null;
    this.comboItem.data = [];
    
    var self = this;
    
    $scope.$on('playlists.loaded', function(e, data) {
        self.table_id = data.table[0].id;
        self.comboItem.data = data.combo_items;
        self.comboItem.selected = null;
        self.data = [];
        self.cancel();
    });
    
    $scope.$on('combo.items.updated', function(e, data) {
        self.comboItem.data = data.items;
        self.comboItem.selected = null;
        self.data = [];
        self.cancel();
    });
    
    this.insertRow = function insertRow() {
        var newid = (self.rows.length > 0) ? self.rows[self.rows.length - 1].id + 1 : 1;
        self.rows.push({id: newid, playlisttableid: self.table_id, playlistsectioncomboitem: {id:0,playlistcomboitemlabel:'None'}, playlistsectionlabel: '', playlistsectioncss: '', playlistsectionsortorder:newid * 10, 
            playlistsectiondirection:'ltr', playlistsectiondownloadlink:'', playlistsectiondownloadlabel:'', playlistsectiondownloadcss:'',
            playlistsectionhide:0});
    };
    
    this.removeRow = function removeRow(id) {
        ld.remove(self.rows, function(row) {
            return row.id == id;
        });
    };
    
    this.comboItem.change = function comboitemChange() {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Retrieving sections&hellip;'});

        var data = {
            action: 'giml_comboitem_sections_get',
            tableid: self.table_id,
            comboitemid: self.comboItem.selected["id"],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.cancel();
            } else {

            }
            $timeout(function() {
                self.data = ld.sortBy(response.data.data, "playlistsectionsortorder");
            });
            jQuery('#playlist-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.edit = function edit() {
        self.isEditMode = true;
        self.rows = angular.copy(self.selected);
            
        $timeout(function(){
            angular.forEach(self.rows, function(row){
                row['playlistsectioncomboitem'] = self.comboItem.selected;
            });
        });        
    };
    
    this.cancel = function cancel(){
        self.isEditMode = false;
        self.selected = null;
        self.rows = [];
    };
    
    this.update = function sectionUpdate() {
        jQuery('#playlist-message').html("").hide();
        
        var tmpRows = angular.copy(self.rows);
        ld.remove(tmpRows, function(row){
            return (ld.isEmpty(ld.trim(row.playlistsectionlabel)));
        });
        if (tmpRows.length == 0) {
            return false;
        }
        
        if (self.isEditMode) {
            jQuery.showStatusDialog({'message': 'Updating section(s)&hellip;'});
        }else{
            jQuery.showStatusDialog({'message': 'Adding section(s)&hellip;'});
        }
        
        angular.forEach(tmpRows, function(row){
            row['playlistsectioncomboitem'] = row['playlistsectioncomboitem'].id;
        });
        
        var data = {
            action: (self.isEditMode)?'giml_section_update':'giml_section_add',
            rows: tmpRows,
            tableid: self.table_id,
            comboitemid: (self.comboItem.selected)?self.comboItem.selected['id']:'',
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.cancel();
                self.data = tmpData.data;
                $rootScope.$broadcast('sections.updated', tmpData.data);
            }else{

            }
            
            jQuery('#playlist-message').html(tmpData.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
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
    
    this.delete = function sectionDelete() {
        jQuery('#playlist-message').html("").hide();
        
        if (self.selected == null) {
            return false;
        }
        jQuery.showStatusDialog({message: 'Deleting section(s)&hellip;'});
        
        var data = {
            action: 'giml_section_delete',
            ids: ld.pluck(self.selected, 'id').join(","),
            tableid: self.table_id,
            comboitemid: self.comboItem.selected["id"],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            var tmpData = response.data;
            if (response.data.success){
                self.cancel();
                self.data = tmpData.data;
                $rootScope.$broadcast('sections.updated', tmpData.data);
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