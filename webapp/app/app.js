'use strict';

// Declare app level module which depends on views, and components
angular.module('tpApp', [
  'ngRoute',
  'tpApp.shop',
  'tpApp.conditions',
  'tpApp.contact',
  'tpApp.version',
  'Restangular'
]).
config(['$locationProvider', '$routeProvider', 'restangular', function($locationProvider, $routeProvider) {
  $locationProvider.hashPrefix('!');

  $routeProvider.otherwise({redirectTo: '/shop'});
  
  //restangular.setBaseUrl('../../backend/v1/public/api');
  
}]);
