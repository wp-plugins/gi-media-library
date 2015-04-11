'use strict';

giml.controller('Column', function($rootScope, $timeout, $http, $scope) {
    this.comboItem = {};
    this.comboItem.selected = null;
    this.comboItem.data = [];
    
    this.section = {};
    this.section.selected = null;
    this.section.data = [];
    
    this.tableColumns = {};
    this.tableColumns.data = [];
    
    var self = this;
    
    $scope.$on('playlists.loaded', function(e, data) {
        self.table_id = data.table[0].id;
        self.comboItem.data = data.combo_items;
        self.comboItem.selected = null;
        self.tableColumns.data = data.table_columns;
        self.section.selected = null;
        self.section.data = [];
        self.data = [];
        self.data1 = [];
        self.cancel();
    });
    
    $scope.$on('combo.items.updated', function(e, data) {
        self.comboItem.data = data.items;
        self.comboItem.selected = null;
        self.section.data = [];
        self.section.selected = null;
        self.data = [];
        self.data1 = [];
        self.cancel();
    });
    
    $scope.$on('sections.updated', function(e, data) {
        self.comboItem.selected = null;
        self.section.data = [];
        self.section.selected = null;
        self.data = [];
        self.data1 = [];
        self.cancel();
    });
    
    $scope.$on('table.columns.updated', function(e, data) {
        self.tableColumns.data = data.columns;
    });
    
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
                self.selected = null;
            } else {

            }
            $timeout(function() {
                self.section.data = response.data.data;
            });
            jQuery('#playlist-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.section.change = function sectionChange() {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Retrieving columns data &hellip;'});

        var data = {
            action: 'giml_columns_get',
            sectionId: self.section.selected.id,
            subgroupId: $scope.playlists.subgroup.selected['id'],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.selected = null;
            } else {

            }
            $timeout(function() {
                self.populateColumnsData(response.data.data);
            });
            jQuery('#playlist-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.rowItemChange = function rowItemChange(itemid, rowid) {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Retrieving sections&hellip;'});

        var data = {
            action: 'giml_comboitem_sections_get',
            tableid: self.table_id,
            comboitemid: itemid,
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.rowSections = {id:rowid, sections:response.data.data};
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
    
    this.save = function save(rows) {
        jQuery('#playlist-message').html("").hide();
        
        var tmpRows = angular.copy(rows);
        ld.remove(tmpRows, function(row){
            return (ld.isEmpty(row.section));
        });
        if (tmpRows.length == 0) {
            return false;
        }
        
        if (self.isEditMode) {
            jQuery.showStatusDialog({'message': 'Updating columns data&hellip;'});
        }else{
            jQuery.showStatusDialog({'message': 'Adding columns data&hellip;'});
        }
        
        angular.forEach(tmpRows, function(row){
            row['sections'] = null;
            row['comboitem'] = row['comboitem'].id;
            row['section'] = row['section'].id;
            var tmpCols = [];
            angular.forEach(row['columns'], function(col,key){
                if (col || col==="")
                    tmpCols.push({id:key, data:col});
            });
            row['columns'] = tmpCols;
        });
        
        var data = {
            action: (self.isEditMode)?'giml_columns_update':'giml_columns_add',
            subgroupId: $scope.playlists.subgroup.selected['id'],
            sectionId: (self.section.selected)?self.section.selected.id:'',
            rows: tmpRows,
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.cancel();
            } else {

            }
            $timeout(function() {
                self.populateColumnsData(response.data.data);
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
        self.rowsData = [];
        var tmpCols = [];
        var tmpRow = null;
        angular.forEach(self.selected, function(sel){
            angular.forEach(self.tableColumns.data, function(col) {
                tmpRow = ld.filter(self.data, function(row){
                    return row.rowid == sel.id;
                })[0];
                tmpCols[col.id] = tmpRow['data'][col.id] || '';
            });
            self.rowsData.push({id:sel.id, playlistsortorder: tmpRow['playlistsortorder'], comboitem:self.comboItem.selected, sections:self.section.data, section:self.section.selected, columns:tmpCols});
            tmpCols = [];            
        });
    };
    
    this.cancel = function cancel(){
        self.isEditMode = false;
        self.selected = null;
        self.rowSections = null;
        self.rowsData = [];
    };
    
    this.delete = function deleteColumns() {
        jQuery('#playlist-message').html("").hide();
        
        if (self.selected == null) {
            return false;
        }
        jQuery.showStatusDialog({message: 'Deleting columns data&hellip;'});
        
        var data = {
            action: 'giml_columns_delete',
            ids: ld.pluck(self.selected, 'id').join(","),
            subgroupId: $scope.playlists.subgroup.selected['id'],
            sectionId: (self.section.selected)?self.section.selected.id:'',
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.cancel();
            } else {

            }
            $timeout(function() {
                self.populateColumnsData(response.data.data);
            });
            jQuery('#playlist-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
            
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.populateColumnsData = function populateData(data) {
        self.data = ld.sortBy(data, "playlistsortorder");
        self.data1 = [];
        var tmpCols = [];
        angular.forEach(self.data, function(cols, key){
            tmpCols = [];
            angular.forEach(cols['data'], function(col){
                tmpCols.push(ld.truncate(ld.stripTags(col), 30));
            });
            if (tmpCols.length > 0)
                self.data1.push({id:cols['rowid'], data:tmpCols.join(' :: ')});
        });
    };
});