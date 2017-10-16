(function() {
    'use strict';

    angular.module('ticTacToeClient').service('boardService', [
        '$resource',
        function($resource) {
            this.board = $resource('make-move', {}, {
                makeMove: {
                    method: 'POST'
                },
                gameWinner: {
                    url:    'game-winner',
                    method: 'POST'
                }
            });

            this.symbols = $resource('symbols', {}, {
                query: {
                    method: 'GET',
                    isArray: true
                }
            });
            return this.event;
        }
    ]);
})();