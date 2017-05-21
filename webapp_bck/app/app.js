'use strict';

// Declare app level module which depends on views, and components
angular.module('tpApp', [
  'ngRoute',
  'tpApp.shop',
  'tpApp.conditions',
  'tpApp.contact',
  'tpApp.version'
]).
config(['$locationProvider', '$routeProvider', function($locationProvider, $routeProvider) {
  $locationProvider.hashPrefix('!');

  $routeProvider.otherwise({redirectTo: '/shop'});  
}]);
