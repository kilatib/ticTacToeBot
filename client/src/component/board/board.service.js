(function() {
    'use strict';

    angular.module('ticTacToeClient').service('boardService', [
        '$resource',
        function($resource) {
            this.board = $resource('make-move', {}, {
                makeMove: {
                    method: 'POST'
                }
            });
            return this.event;
        }
    ]);
})();