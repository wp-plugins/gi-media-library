/* 
 * 
 * @author Zishan J.
 */

giml.controller('Settings', function(NONCE, $timeout, $http, SETTINGS) {
    this.statusMessage = "";
    this.searchBarCaption = SETTINGS.search_bar_caption;
    this.searchPageTitle = SETTINGS.search_page_title;
    this.disableJqueryuiCss = (SETTINGS.disable_jqueryui_css === 'true')?true:false;
    this.disableBootstrapCss = (SETTINGS.disable_bootstrap_css === 'true')?true:false;
    this.template = SETTINGS.template;
    this.playerColor = SETTINGS.player_color;
    
    var self = this;
    
    this.save = function save() {
        jQuery.showStatusDialog({'message': 'Saving settings&hellip;'});
        
        var data = {
            action: 'giml_save_settings',
            settings: {
                search_bar_caption: self.searchBarCaption,
                search_page_title: self.searchPageTitle,
                disable_jqueryui_css: self.disableJqueryuiCss,
                disable_bootstrap_css: self.disableBootstrapCss,
                player_color: self.playerColor,
                template: self.template
            },
            _ajax_nonce: NONCE
        };

        $http.post(ajaxurl, jQuery.param(data)).then(function(response) {
            if (response.data.success){
                
            }else{

            }
            self.statusMessage = response.data.message;
            jQuery.scrollTo('#settings-message', 800, {offset: -35});
            jQuery.showStatusDialog({show: false});
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
});