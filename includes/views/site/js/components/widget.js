'use strict';

giml.directive('giWidget', function($rootScope, $timeout, $http, TEMPLATE_URI) {
        return {
            restrict: "E",
            replace: true,
            scope: {
                widgetId: '@'
            },
            controller: function($scope) {
                
                /*var tmp = ld.find($scope.$parent.$parent.$parent.catalog.tab.data, function(tab) {
                    return tab.id == $scope.tabId;
                });
                $scope.collection = tmp.accordions;
                $scope.showAccordionContent = (tmp.showaccordioncontent == "true");*/
                
                
            },
            templateUrl: TEMPLATE_URI + 'widget.html',
            link: function(scope, element, attrs) {
                scope.collection = gimlWidgetData.groups;
                scope.current_group_id = gimlWidgetData.current_group_id;
                scope.defaultId = 0;
                
            }
        };
    })
    .directive('widgetItemLoaded', function($timeout, $compile, $window) {
        return function(scope, element, attrs) {
            if (!ld.isEmpty(gimlWidgetData.current_group_id)) {
                if (scope.group.id == gimlWidgetData.current_group_id) {
                    scope.$parent.defaultId = scope.$index;
                }
            }
            if (scope.$last) {
                $timeout(function(){
                    jQuery( "#" + scope.widgetId ).accordion({
                        header: "> div > h1, > div > h2, > div > h3, > div > h4, > div > h5, > div > h6",
                        heightStyle: "content",
                        icons: false,
                        collapsible: true,
                        active: scope.$parent.defaultId
                    });
                });
            }
        };
    });