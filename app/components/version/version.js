'use strict';

angular.module('tpApp.version', [
  'tpApp.version.interpolate-filter',
  'tpApp.version.version-directive'
])

.value('version', '0.1');
