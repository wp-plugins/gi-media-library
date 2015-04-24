'use strict';

giml.directive('giMenu', function($rootScope, $compile, $timeout, $http, AJAX_URL, NONCE, TEMPLATE_URI) {
    return {
        restrict: "E",
        replace: true,
        scope: {
            menuId: '@',
            menuCollection: '=',
            defaultSubgroup: '='
        },
        controller: function($scope) {
            /*$rootScope.$broadcast('widget.loader.show');
            var data = {
                action: 'gicatalog_get_category',
                groupid: $scope.menuGroupId,
                _ajax_nonce: NONCE
            };

            $http.post(AJAX_URL, jQuery.param(data)).then(function(response) {
                if (response.data.success) {
                    
                    $scope.collection = response.data.data;
                } else {

                }
                $timeout(function() {
                    $rootScope.$broadcast('widget.loader.hide');
                });
            }, function(rejectReason) {
                //self.showAddCategoriesDialog();
                console.log("request error");
                console.dir(rejectReason);
            });*/

        },
        template: '<ul id="{{::menuId}}" class="parent-menu">\n\
                        <collection collection="menuCollection" default-subgroup="defaultSubgroup" menu-items-loaded></collection>\n\
                    </ul>',
        link: function(scope, element, attrs) {

        }
    };
})
        .directive('menuItemsLoaded', function($timeout) {
    return function(scope, element, attrs) {
        if (scope.$last) {
            $timeout(function() {
                jQuery("#" + scope.$parent.$parent.menuId).menu();
               
            });
        }
    };
})
        .directive('submenuItemsLoaded', function($timeout) {
    return function(scope, element, attrs) {
        if (scope.$last) {
            $timeout(function() {
                //jQuery("[id^='gicatalog-widget-category-menu']").menu("destroy");
                //jQuery("[id^='gicatalog-widget-category-menu']").menu();
            });
        }
    };
})
        .directive("collection", function() {
    return {
        restrict: "E",
        replace: true,
        scope: {
            collection: '=',
            defaultSubgroup: '='
        },
        template: "<category ng-repeat='category in ::collection track by $index' default-subgroup='defaultSubgroup' category='category'></category>"
    };
})
        .directive('category', function($compile, $rootScope, QUERY_SUBGROUPID_URL) {
    return {
        restrict: "E",
        replace: true,
        scope: {
            category: '=',
            defaultSubgroup: '='
        },
        controller: function($scope) {
            $scope.clicked = function clicked(e, id) {
                //e.preventDefault();
                
                $rootScope.$broadcast('menu.link.click', id);
            };

        },
        template: "<li ng-class=\"{selected: (defaultSubgroup==category.id)}\"><a ng-href=\"{{menu_url + '=' + category.id}}\" ng-click=\"clicked($event, category.id)\" ng-bind-html='category.subgrouplabel|text' class=\"{{((category.subgroupdirection === 'rtl')?'text-right':'text-left') + ' ' + category.subgroupcss}}\" ng-style=\"{'direction': category.subgroupdirection}\"></a></li>",
        link: function(scope, element, attrs) {
            scope.menu_url = QUERY_SUBGROUPID_URL;
            if (angular.isDefined(scope.category.subcategory)) {
                element.append("<ul><collection collection='category.subcategory' submenu-items-loaded></collection></ul>");
                $compile(element.contents())(scope);
            }

        }
    };
});