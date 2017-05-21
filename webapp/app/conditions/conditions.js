'use strict';

angular.module('tpApp.conditions', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/conditions', {
    templateUrl: 'conditions/conditions.html',
    controller: 'ConditionsController'
  });
}])

.controller('ConditionsController', [function() {

}]);