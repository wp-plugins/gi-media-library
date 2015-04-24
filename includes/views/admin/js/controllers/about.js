'use strict';

giml.controller('About', function(NONCE, $timeout, $http) {
    postboxes.add_postbox_toggles(pagenow);
    this.statusMessage = "";
    this.name = "";
    this.email = "";
    this.subject = "";
    this.message = "";
    
    this.basicEd = {
        menubar: false,
      	plugins: [
            "advlist lists charmap print preview anchor",
            "searchreplace visualblocks code",
            "insertdatetime media paste"
        ],
      	toolbar: "undo redo | styleselect | bold italic underline | code",
        width: 270,
        height: 150,
        //forced_root_block: false
    };
    
    var self = this;
        
    this.send = function send() {
        if (jQuery.trim(self.name)==='' || jQuery.trim(self.email)==='' || jQuery.trim(self.subject)==='' || jQuery.trim(self.message)==='')
            return false;
        
        jQuery.showStatusDialog({'message': 'Sending message&hellip;'});
        
        var data = {
            action: 'giml_send_message',
            message: {name: jQuery.trim(self.name), email: jQuery.trim(self.email), subject: jQuery.trim(self.subject), message: jQuery.trim(self.message)},
            _ajax_nonce: NONCE
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success){
                
            }else{

            }
            self.name = self.email = self.subject = self.message = "";
            self.statusMessage = response.data.message;
            jQuery.scrollTo('#about-message', 800, {offset: -35});
            jQuery.showStatusDialog({show: false});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
        
    };
    
    
});
