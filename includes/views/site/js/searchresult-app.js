'use strict';

var gimlSearchResult = angular.module('GIML-SearchResult', ['ngSanitize', 'ngAnimate', 'ui.bootstrap'])
    .run(function($window, $http){
        
        function stripslashes(str) {
            if (str) {
                    str=str.replace(/\\'/g,'\'');
                    str=str.replace(/\\"/g,'"');
                    str=str.replace(/\\0/g,'\0');
                    str=str.replace(/\\\\/g,'\\');
            }
            return str;
        }
        ld.mixin({'stripslashes': stripslashes});
        ld.mixin(s.exports());
    })
    .constant('URI', gimlSearchResult.URI)
    .constant('PAGE_LINK', gimlSearchResult.page_link)
    .constant('NONCE', gimlSearchResult.nonce)
    .constant('AJAX_URL', gimlSearchResult.ajax_url)
    .constant('SEARCH', gimlSearchResult.search)
    .constant('PLAYLIST_DATA', gimlSearchResult.data)
    .constant('SETTINGS', gimlSearchResult.settings)
    .animation('.repeated-item', function() {
        return {
          enter : function(element, done) {
            element.css('opacity',0);
            jQuery(element).animate({
              opacity: 1
            }, 500, done);

            // optional onDone or onCancel callback
            // function to handle any post-animation
            // cleanup operations
            return function(isCancelled) {
              if(isCancelled) {
                jQuery(element).stop();
              }
            }
          },
          leave : function(element, done) {
            element.css('opacity', 1);
            jQuery(element).animate({
              opacity: 0
            }, 500, done);

            // optional onDone or onCancel callback
            // function to handle any post-animation
            // cleanup operations
            return function(isCancelled) {
              if(isCancelled) {
                jQuery(element).stop();
              }
            }
          },
          move : function(element, done) {
            element.css('opacity', 0);
            jQuery(element).animate({
              opacity: 1
            }, 500, done);

            // optional onDone or onCancel callback
            // function to handle any post-animation
            // cleanup operations
            return function(isCancelled) {
              if(isCancelled) {
                jQuery(element).stop();
              }
            }
          },

          // you can also capture these animation events
          addClass : function(element, className, done) {},
          removeClass : function(element, className, done) {}
        }
    })
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.post = {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        };
        //$httpProvider.interceptors.push('requestRejector');
        // Removing 'requestRecoverer' will result to failed request
        //$httpProvider.interceptors.push('responseRecoverer');
    }])
    .factory('requestRejector', ['$q', function($q) {
        var requestRejector = {
            request: function(config) {
                return $q.reject('requestRejector');
            }
        };
        return requestRejector;
    }])
    .factory('responseRecoverer', ['$q', '$injector', function($q, $injector) {
        var responseRecoverer = {
            responseError: function(response) {
                //refer to the status code reference at http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
                if (response.status == 0) {
                    // may be offline
                    //uncomment below to retry the request
                    /*var $http = $injector.get('$http');
                    return $http(response.config);*/
                } else {
                    return $q.reject(response);
                }
            },
            /*requestError: function(response) {
                console.log("reloading");
                if (response.message === "reload") {
                    return response.data.config;
                }
            }*/
        };
        return responseRecoverer;
    }])
    .directive('contenteditable', function() {
        return {
          require: 'ngModel',
          link: function(scope, elm, attrs, ctrl) {
            // view -> model
            elm.on('blur', function() {
              scope.$apply(function() {
                ctrl.$setViewValue(elm.html());
              });
            });

            // model -> view
            ctrl.$render = function() {
              elm.html(ctrl.$viewValue);
            };

            // load init value from DOM
            ctrl.$setViewValue(elm.html());
          }
        };
    })
    .filter('text', function() {
        return function(str) {
            return ld.stripslashes(str);
        };
    })
    .filter('html', function($sce) {
        return function(input) {
            return $sce.trustAsHtml(ld.stripslashes(input));
        };
    })
    .filter('htmlToPlain', function() {
        return function(text) {
          return String(text).replace(/<[^>]+>/gm, '');
        };
    });
angular.element("#giml-search-result").ready(function() {
    angular.bootstrap("#giml-search-result", ['GIML-SearchResult']);
});