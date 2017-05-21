'use strict';

angular.module('tpApp.shop', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/shop', {
    templateUrl: 'shop/shop.html',
    controller: ['restangular', ShopController]
  });
}]);

function ShopController(resangular) {
	var abc = 3;
	
	
	var def = 5;
}

//.controller('ShopController', [function(restangular) {
//	Restangular.all('committee').getList();
//}]);