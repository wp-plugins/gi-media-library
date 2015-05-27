'use strict';

gimlSearchResult.controller('SearchResult', function ($scope, $rootScope, $timeout, $http, AJAX_URL, URI, NONCE, PLAYLIST_DATA, SETTINGS, PAGE_LINK) {
    this.data = PLAYLIST_DATA;
    this.uri = URI;
    this.pagination = {};
    this.pagination.show = SETTINGS.pagination.show;
    this.pagination.searchedString = SETTINGS.pagination.searched_string;
    this.pagination.totalItems = parseInt(PLAYLIST_DATA.total_items);
    this.pagination.currentPage = 1;
    this.pagination.maxSize = parseInt(SETTINGS.pagination.max_size);
    this.pagination.itemsPerPage = parseInt(SETTINGS.pagination.items_per_page);
    
    var self = this;
    
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
    
    this.pagination.pageChanged = function pageChanged() {
        self.showLoader = true;

        self.loaderText = 'Loading...';

        var data = {
            action: 'giml_search_result_page_changed',
            search_string: ld.stripslashes(self.pagination.searchedString),
            current_page: self.pagination.currentPage,
            items_per_page: self.pagination.itemsPerPage,
            page_link: PAGE_LINK,
            _ajax_nonce: NONCE
        };

        $http.post(AJAX_URL, jQuery.param(data)).then(function(response) {
            self.showLoader = false;
            if (response.data.success) {
                self.data = response.data.data;
            } else {

            }
            
        }, function(rejectReason) {
            console.log("request error");
            console.dir(rejectReason);
        });
    };
    
})
        .directive('gimlTd', function($compile, $rootScope) {
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

                    scope.$parent.result.player.playTrackURL(ld.stripslashes(url));//jQuery(track).attr('data-url'));
                    //jQuery(track).addClass('current').siblings().removeClass('current');

                };
                element.append($compile(scope.colData)(scope));
            }else
                element.append(scope.colData);
            
        }
    };
});