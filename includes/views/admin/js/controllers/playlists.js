'use strict';

giml.controller('Playlists', function($rootScope, $timeout, $http, $scope) {
    this.group = {};
    this.group.data = gimlData.groups;
    this.group.selected = null;
    
    this.subgroup = {};
    
    var self = this;
    
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
    
    this.group.change = function changeGroup() {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Retrieving subgroups&hellip;'});

        var data = {
            action: 'giml_group_subgroups_get',
            groupid: (angular.isObject(self.group.selected))?self.group.selected["id"]:"",
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                //self.cancel();
            } else {

            }
            $timeout(function() {
                self.subgroup.selected = null;
                self.subgroup.data = response.data.data;
            });
            jQuery('#playlist-message').html(response.data.message).show();
            jQuery.showStatusDialog({show: false});
            jQuery.scrollTo('#playlist-message', 800, {offset: -35});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.subgroup.change = function changeSubgroup() {
        self.subgroupdownloadlink = self.subgroup.selected.subgroupdownloadlink;
        self.subgroupdownloadlabel = self.subgroup.selected.subgroupdownloadlabel;
        self.subgroupdownloadcss = self.subgroup.selected.subgroupdownloadcss;
        self.subgroupshowfilter = self.subgroup.selected.subgroupshowfilter;
        self.subgroupshowcombo = self.subgroup.selected.subgroupshowcombo;
        
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Loading&hellip;'});

        var data = {
            action: 'giml_playlist_get',
            subgroupid: self.subgroup.selected['id'],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.data = response.data.data;
                $rootScope.$broadcast('playlists.loaded', self.data);
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
    
    this.update = function update() {
        jQuery('#playlist-message').html("").hide();

        jQuery.showStatusDialog({'message': 'Updating&hellip;'});

        var row = [{
            id: self.subgroup.selected.id,
            update_downloadlink: 1,
            subgroupdownloadlink: self.subgroupdownloadlink,
            subgroupdownloadlabel: self.subgroupdownloadlabel,
            subgroupdownloadcss: self.subgroupdownloadcss,
            subgroupshowfilter: self.subgroupshowfilter,
            subgroupshowcombo: self.subgroupshowcombo,
        }];
        var data = {
            action: 'giml_subgroup_downloadlink_update',
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
});