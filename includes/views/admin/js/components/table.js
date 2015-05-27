'use strict';

giml.directive('gimlTable', function($compile, TEMPLATE_URI, URI, $timeout){
    return {
        restrict: 'E',
        scope: {
            isEditMode: '=edit',
            comboItems: '=',
            tableColumns: '=',
            rowTableSections: '=',
            itemChange: '&onItemChange',
            save: '&onSave',
            rowsData: '=',
            showSortOrder: '@'
        },
        link: function(scope, element, attrs) {
            scope.uniqueId = new Date().getTime();
            scope.rows = [];
            scope.myURI = URI;
            scope.link = {};
            scope.link.rows = [];
            scope.video = {};
            scope.video.rows = [];
            scope.selectedRowId = null;
            scope.selectedColumnId = null;
            scope.selectedColumnType = 'link';
            scope.itemEd = {
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
            
            scope.$watchCollection(function(){return scope.rowTableSections;}, function(newVal, oldVal){
                if (angular.isObject(newVal)) {
                    scope.rows[newVal.id].sections = newVal.sections;
                    scope.rows[newVal.id].section = null;
                }else{
                    scope.cancel();
                }
            });
            
            scope.$watchCollection(function(){return scope.tableColumns;}, function(newVal, oldVal){
                scope.cancel();
            });
            
            scope.$watchCollection(function(){return scope.rowsData;}, function (newVal, oldVal){
                scope.rows = newVal;
            });
            
            scope.insertRow = function insertRow() {
                var newid = (scope.rows.length > 0) ? scope.rows[scope.rows.length - 1].id + 1 : 1;
                scope.rows.push({id: newid, playlistsortorder: newid*10, comboitem: null, sections:[], section: null, columns: null});
                
            };
            
            scope.remove = function remove(id) {
                ld.remove(scope.rows, function(row) {
                    return row.id == id;
                });
            };
            
            scope.cancel = function cancel(){
                scope.isEditMode = false;
                scope.rows = [];
                scope.link.rows = [];
                scope.video.rows = [];
            };
            
            scope.openForm = function openForm(row, colId, colType, data) {
                
                if (colType === 'video') 
                    tb_show('Add/Remove Videos', '#TB_inline?inlineId=giml-add-video-form' + scope.uniqueId + '&width=530&height=400');
                else
                    tb_show('Add/Remove Links', '#TB_inline?inlineId=giml-add-link-form' + scope.uniqueId + '&width=530&height=400');                

                scope.link.rows = [];
                scope.video.rows = [];
                scope.selectedRowId = row.id;
                scope.selectedColumnId = colId;
                scope.selectedColumnType = colType;
                if(data) {
                    var rows = data.split('::');
                    angular.forEach(rows, function(row, key){
                        if(colType === 'video')
                            scope.video.rows.push({id: key+1, type: row.split('||')[0], videoid: row.split('||')[1]});
                        else
                            scope.link.rows.push({id: key+1, link: row.split('||')[0], text: row.split('||')[1] || ''});
                    });
                }
            };
            
            scope.link.insertRow = function linkInsertRow() {
                var newid = (scope.link.rows.length > 0) ? scope.link.rows[scope.link.rows.length - 1].id + 1 : 1;
                scope.link.rows.push({id: newid, link: '', text: ''});                
            };
            
            scope.link.remove = function linkRemove(id) {
                ld.remove(scope.link.rows, function(row) {
                    return row.id == id;
                });
            };
            
            scope.link.save = function linkSave() {
                var rows = [];
                var tmpRows = angular.copy(scope.link.rows);
                ld.remove(tmpRows, function(row){
                    return (ld.isEmpty(ld.trim(row.link)));
                });
                
                angular.forEach(tmpRows, function(row, key){
                    if (scope.selectedColumnType === 'iconiclink')
                        rows.push(row.link);
                    else
                        rows.push(row.link + '||' + row.text);
                });
                var tmpRow = ld.filter(scope.rows, function(row){
                    return row.id == scope.selectedRowId;
                });
                
                if (tmpRow)
                    tmpRow[0].columns[scope.selectedColumnId] = rows.join('::');
                
                tb_remove();
            };
            
            scope.video.insertRow = function videoInsertRow() {
                var newid = (scope.video.rows.length > 0) ? scope.video.rows[scope.video.rows.length - 1].id + 1 : 1;
                scope.video.rows.push({id: newid, videoid: '', type: 'youtube'});                
            };
            
            scope.video.remove = function videoRemove(id) {
                ld.remove(scope.video.rows, function(row) {
                    return row.id == id;
                });
            };
            
            scope.video.save = function videoSave() {
                var rows = [];
                var tmpRows = angular.copy(scope.video.rows);
                ld.remove(tmpRows, function(row){
                    return (ld.isEmpty(ld.trim(row.videoid)));
                });
                
                angular.forEach(tmpRows, function(row, key){
                    rows.push(row.type + '||' + row.videoid);
                });
                scope.rows[scope.selectedRowId].columns[scope.selectedColumnId] = rows.join('::');
                
                tb_remove();
            };
        },
        templateUrl: TEMPLATE_URI + 'table.html'
        
    };
});