'use strict';

giml.controller('Shortcode', function($compile, $timeout, $http, URI, NONCE) {
    this.showLoader = false;
    this.setAsDefault = false;
    this.showPagination = false;
    this.itemsPerPage = 10;
    
    this.uri = URI;
    this.group = {};
    this.group.data = [];
    this.group.selected = null;
    this.subgroup = {};
    this.subgroup.data = [];
    this.subgroup.selected = null;
    
    var self = this;
    
    jQuery('#giml-shortcode').dialog({
        open: function(e) {
            self.group.data = [];
            self.subgroup.data = [];
            self.group.selected = null;
            self.subgroup.selected = null;
            
            self.showLoader = true;
            var data = {
                action: 'giml_groups_get',
                _ajax_nonce: NONCE
            };

            $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
                if (response.data.success) {
                    self.group.data = response.data.data;
                } else {

                }
                $timeout(function() {
                    self.showLoader = false;
                });
            }, function(rejectReason) {
                console.log("request error");
                console.dir(rejectReason);
            });
        }
    });
    
    this.group.change = function changeGroup() {
        self.showLoader = true;

        var data = {
            action: 'giml_group_subgroups_get',
            groupid: self.group.selected["id"],
            _ajax_nonce: gimlData.nonce
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success) {
                self.subgroup.selected = null;
                self.subgroup.data = response.data.data;
            } else {

            }
            $timeout(function() {
                self.showLoader = false;
            });
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
    this.insert = function shortcodeInsert() {
        var shortcode = '[gi_medialibrary id="' + self.subgroup.selected['id'] + '" \n\
                                                        group_id="' + self.group.selected['id'] + '" \n\
                                                        default="' + self.setAsDefault + '"';/* \n\
                                                        show_pagination="' + self.showPagination + '"';*/
        
        shortcode += (self.showPagination==true)?' items_per_page="' + self.itemsPerPage + '"]':']';
            
        tinymce.editors['content'].selection.setContent(shortcode);
    };
});