'use strict';

giml.controller('Import', function ($scope, $http, NONCE, FileUploader) {
    var uploader = $scope.uploader = new FileUploader({
        url: ajaxurl,
        autoUpload: true,
        formData: [{
            _ajax_nonce: NONCE,
            action: 'giml_import_upload'
        }]
    });
    this.isUploading = false;
    this.isImported = true;
    
    var self = this;

    uploader.filters.push({
        name: 'customFilter',
        fn: function (item /*{File|FileLikeObject}*/, options) {
            return item.type === 'text/xml';
        }
    });

    uploader.onWhenAddingFileFailed = function (item /*{File|FileLikeObject}*/, filter, options) {
        if (item.type !== 'text/xml') {
            self.message = '<div class="error"><p>Invalid filetype. Only XML filetype is supported.</p></div>';
        }

    };
    
    uploader.onAfterAddingFile = function (item) {
        self.message = '';
        self.statusMessage = '';
    };
    
    uploader.onBeforeUploadItem = function (item) {
        self.isUploading = true;
        self.isImported = false;
    };
    
    uploader.onSuccessItem = function (item, response, status, headers) {
        if (!response.success) {
            self.message = response.message;
            return;
        }
        self.statusMessage = '<p>File uploaded successfully.</p>';
        self.statusMessage += '<p>Importing file . . .</p>';
        self.isUploading = false;
        uploader.clearQueue();
        
        var data = {
            action: 'giml_import',
            _ajax_nonce: NONCE,
            file: response.data.file
        };
        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success){
                
            }else{

            }
            self.isImported = true;
            self.statusMessage += '<p>File imported successfully.</p>';
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
});