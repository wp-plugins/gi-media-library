'use strict';
angular.module('giPlayer', [])
.controller('Player', function ($scope, $rootScope, $timeout, $window, PLAYLIST_DATA, $log, URI) {
    this.isHide = false;
    this.isAvailable = false;
    this.processingPlaylistClick = false;
    this.processingDisplayClick = false;
    this.playlist = {};
    this.playlist.isHide = true;
    this.playlist.data = PLAYLIST_DATA.audioplaylist;
    this.playlist.player = null;

    var self = this;

    $scope.$on('giml.player.playlist.updated', function(e, data) {
        self.playlist.data = data;
    });
    
    $scope.$on('giml.player.playlist.play', function(e, track) {
        self.playlist.player.play(ld.findIndex(self.playlist.player.playlist, ld.find(self.playlist.player.playlist, function(playlist) {
                return playlist.url === ld.unescapeHTML(decodeURIComponent(track));
            })
        ));
    });
    
    this.display = function display() {
        if (self.processingDisplayClick)
            return;

        self.processingDisplayClick = true;

        if (!self.isHide)
            jQuery('#giml-player .giplayer-controlbar').removeClass('giplayer-show');

        $timeout(function () {
            jQuery("#giml-player").animate({
                right: (self.isHide) ? '0' : '-' + (jQuery("#giml-player").width() - 51) + 'px'
            }, 1000, function () {
                self.isHide = !self.isHide;
                if (!self.isHide)
                    jQuery('#giml-player .giplayer-controlbar').addClass('giplayer-show');
                $scope.$apply();
                self.processingDisplayClick = false;
            });
        }, (!self.isHide) ? 300 : 0);
    };

    this.playlist.show = function showPlaylist() {
        if (self.processingPlaylistClick)
            return;

        self.processingPlaylistClick = true;
        
        jQuery('#giml-player .giplayer-playlist').animate({
            height: (self.playlist.isHide) ? '200px' : '0'
        }, 1000, function () {
            self.playlist.isHide = !self.playlist.isHide;
            self.processingPlaylistClick = false;
            if (self.playlist.isHide)
                jQuery(self.playlist.player.cssSelector.playlist).scrollTo(jQuery(self.playlist.player.cssSelector.playlist + ' .jp-playlist-current'), 800, {offset: -35});
        });
    };

    $scope.$watchCollection(function () {
        return self.playlist.data;
    }, function (newVal, oldVal) {
        if (newVal) {
            self.isAvailable = true;
            
            var playlist = [];
            angular.forEach(newVal, function (val) {
                var url = ld.unescapeHTML(decodeURIComponent(val.split('||')[0]));
                var item = {};
                item['title'] = val.split('||')[1];//(ld.trim(val.split('||')[1])!=='')?ld.trim(val.split('||')[1]):self.getTrackName(url);
                item[url.substring(url.lastIndexOf('.') + 1)] = url;
                item['url'] = url; //used for searching
                item['download'] = val.split('||')[2];
                playlist.push(item);
            });
            
            if (jQuery('#giml-player .giplayer').data("jPlayer")) {
                self.playlist.player.setPlaylist(playlist);
                return;
            }

            self.playlist.player = new jPlayerPlaylist({
                player: '#giml-player',
                jPlayer: '#giml-player .giplayer',
                cssSelectorAncestor: '#giml-player .giplayer-container',
            }, playlist, {
                cssSelector: {
                    seekBar: ".giplayer-seek-bar",
                    playBar: ".giplayer-play-bar",
                    currentTime: '.giplayer-current-time',
                    duration: '.giplayer-duration',
                    play: '.giplayer-play',
                    mute: '.giplayer-mute',
                },
                swfPath: 'https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/',
                smoothPlayBar: true,
                useStateClassSkin: true
            });
            jQuery(self.playlist.player.cssSelector.jPlayer).bind(jQuery.jPlayer.event.setmedia, function(e){
                jQuery(self.playlist.player.cssSelector.playlist).scrollTo(jQuery(self.playlist.player.cssSelector.playlist + ' .jp-playlist-current'), 800, {offset: -35});
            });
            jQuery(self.playlist.player.cssSelector.player + ' .giplayer-volume-bar').slider({
                range: "min",
                value: jQuery(self.playlist.player.cssSelector.jPlayer).data("jPlayer").options.volume,
                min: 0,
                step: 0.1,
                max: 1,
                slide: function(e, ui) {
                    jQuery(self.playlist.player.cssSelector.jPlayer).jPlayer("volume", ui.value);
                }
            });
        }else{
            self.isAvailable = false;
        }
    });

    this.next = function next() {
        self.playlist.player.next();
    };
    
    this.previous = function previous() {
        self.playlist.player.previous();
    };
    
    this.getTrackName = function getTrackName (trackUrl) {
        var trackUrlParts = trackUrl.split("/");
        if (trackUrlParts.length > 0) {
            return decodeURIComponent(trackUrlParts[trackUrlParts.length - 1]);
        }
        else {
            return '';
        }
    };
    
    this.downloadTrack = function downloadTrack() {
        $window.location.href = self.playlist.player.playlist[self.playlist.player.current].download;
    };
});