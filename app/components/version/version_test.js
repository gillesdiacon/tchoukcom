'use strict';

describe('tpApp.version module', function() {
  beforeEach(module('tpApp.version'));

  describe('version service', function() {
    it('should return current version', inject(function(version) {
      expect(version).toEqual('0.1');
    }));
  });
});
