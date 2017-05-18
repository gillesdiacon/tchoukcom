'use strict';

angular.module('tpApp.shop', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/shop', {
    templateUrl: 'shop/shop.html',
    controller: 'ShopController'
  });
}])

.controller('ShopController', [function() {

}]);