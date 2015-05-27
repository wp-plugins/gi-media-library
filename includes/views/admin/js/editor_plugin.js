(function() {
    tinymce.create('tinymce.plugins.gi_medialibrary', {
        init: function(ed, url) {
            tinymce.plugins.gi_medialibrary.theurl = url;
            
            ed.addButton('btn_gimedialibrary', {
                title: 'Insert shortcode from GI-Media Library',
                image: gimlData.URI + '/images/medialibrary_button_icon2.png',
                onclick: function() {
                    jQuery('#giml-shortcode').dialog('open');
                }
            });

        },
        createControl: function(n, cm) {
            return null;

        },
        getInfo: function() {
            return {
                longname: "GI-Media Library",
                author: 'Glare of Islam',
                authorurl: 'http://www.glareofislam.com/',
                infourl: 'Email: info@glareofislam.com',
                version: "2.0"
            };
        }
    });
    tinymce.PluginManager.add('gi_medialibrary', tinymce.plugins.gi_medialibrary);
})();