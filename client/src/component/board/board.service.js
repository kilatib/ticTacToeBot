(function() {
    'use strict';

    angular.module('ticTacToeClient').service('boardService', [
        '$resource',
        function($resource) {
            this.field = $resource('move/:symbol', {
                symbol: '@symbol'
            }, {
                move: {
                    method: 'PUT'
                },
                calculate: {
                    method: 'GET'
                }
            });
            return this.event;
        }
    ]);
})();