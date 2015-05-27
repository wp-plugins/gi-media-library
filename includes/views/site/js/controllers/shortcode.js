'use strict';

giml.controller('Shortcode', function($scope, PAGE_LINK, $http, AJAX_URL, URI, PLAYLIST_DATA, NONCE, SETTINGS) {
    this.showLoader = false;
    this.loaderText = "";
    
    this.data = PLAYLIST_DATA;
    this.uri = URI;
    
    this.combo = {};
    this.combo.selected = null;
    
    this.filter = {};
    this.filter.selected = {id: 0, playlistsectionlabel: 'None'};
    
    var self = this;
    
    $scope.$watchCollection(function(){return self.data.combo;}, function(newVal, oldVal){
        if (ld.isEmpty(newVal))
            return;
        
        if (newVal)
            self.combo.selected = angular.copy(newVal.defaultitem);
    });
    $scope.$watchCollection(function(){return self.data.sections;}, function(newVal, oldVal){
        if (newVal) {
            self.filter.data = angular.copy(newVal);
            self.filter.data.unshift({id: 0, playlistsectionlabel: 'None'});
            self.filter.selected = {id: 0, playlistsectionlabel: 'None'};
        }
    });
    $scope.$watchCollection(function(){return self.data.audioplaylist;}, function(newVal, oldVal){
        if (newVal) {
            if (jQuery('div#giml-player').length) {
                jQuery('div#giml-player').remove();
                if (self.player) {
                    self.player.pause();
                    self.player.remove();
                }
            }
            
            var div = document.createElement('div'),
            audioElement = document.createElement('audio'),
            source;
            div.id = 'giml-player';// background-audio-' + Drupal.settings.background_audio.settings.position;

            /* Do not preload media - this prevents media loading if no autoplay. */
            audioElement.setAttribute('preload', 'none');
            //if (Drupal.settings.background_audio.settings.autoplay) {
              //audioElement.setAttribute('autoplay', 'autoplay');
            //}
            /* If this attribute is set - then the "ended" event doesn't fire after song and it repeats just the first song */
            /*if (Drupal.settings.background_audio.background_audio_loop) {
              audioElement.setAttribute('loop', 'loop');
            }*/
            audioElement.setAttribute('controls', 'controls');

            document.body.appendChild(div);
            div.appendChild(audioElement);
            
            angular.forEach(newVal, function(val){
                source = document.createElement('source');
                source.setAttribute('src', val.split('||')[0]);
                source.setAttribute('title', val.split('||')[1] + '||' + val.split('||')[2]);
                //source.setAttribute('type', element.type);
                audioElement.appendChild(source);
            });
            
            //if (!self.player) {console.log("new");
                self.player = new MediaElementPlayer('#giml-player > audio', {
                    loop: true,
                    shuffle: false,
                    playlist: false,
                    audioHeight: 30,
                    audioWidth: '100%',
                    playlistposition: 'top',
                    features: ['playlistfeature', 'downloadtrack', 'prevtrack', 'playpause', 'nexttrack', 'playlist', 'current', 'progress', 'duration', 'volume'],
                });
            //}
            jQuery('div#giml-player .mejs-container, div#giml-player .mejs-embed, div#giml-player .mejs-embed body, div#giml-player .mejs-container .mejs-controls').css('background', SETTINGS.player_color);
        }
        
    });
    
    this.combo.change = function comboChange() {
        self.showLoader = true;

        self.loaderText = 'Loading...';

        var data = {
            action: 'giml_table_playlist_get',
            subgroupId: self.data.subgroup.id,
            itemId: self.combo.selected.id,
            tableId: self.data.table.id,
            pageLink: PAGE_LINK,
            _ajax_nonce: NONCE
        };

        $http.post(AJAX_URL, jQuery.param(data)).then(function(response) {
            self.showLoader = false;
            if (response.data.success) {
                self.data.sections = response.data.data.sections;
                self.data.audioplaylist = response.data.data.audioplaylist;
            } else {

            }
            
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
});
giml.directive('gimlTd', function($compile, $sce) {
    return {
        restrict: 'A',
        scope: {
            colData: '=',
            colType: '='
        },
        link: function(scope, element, attrs) {
            if(scope.colData && scope.colType === 'audio'){
                scope.play = function play(event, url) {
                    event.preventDefault();

                    //var track = jQuery.find('.mejs-playlist > ul li[data-url="' + url + '"]')

                    scope.$parent.shortcode.player.playTrackURL(ld.stripslashes(url));//jQuery(track).attr('data-url'));
                    //jQuery(track).addClass('current').siblings().removeClass('current');

                };
                element.append($compile(scope.colData)(scope));
            }else
                element.append(scope.colData);
            
        }
    };
});