'use strict';

gimlSearch.controller('Search', function ($scope, $timeout, $http, URI, NONCE, SEARCH) {
    this.caption = SEARCH.search_bar_caption;
    this.showPagination = SEARCH.show_pagination;
    this.itemsPerPage = SEARCH.items_per_page;
    this.maxSize = SEARCH.max_size;
    this.dir = 'ltr';
    this.lang = 'en';
    
    var self = this;
    /*$scope.$watchCollection(function(){return SEARCH;}, function(newVal, oldVal){
     console.log(gimlData);
     self.caption = newVal;
     });*/
    
    this.changeLanguage = function changeLanguage(lang, dir) {
        self.dir = dir;
        self.lang = lang;
    };
})
.directive('text', function () {
    return {
        require: 'ngModel',
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$validators.text = function (modelValue, viewValue) {
                if (ctrl.$isEmpty(ld.trim(modelValue))) {
                    return false;
                }
                return true;
            };
        }
    };
});