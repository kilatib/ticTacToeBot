(function () {
    'use strict';

    angular.module('ticTacToeClient')
        .component('board', {
            templateUrl: '/board/board.html',
            bindings: {
                'size': '<'
            },
            controller: [
                'AppSettings', 'boardService',
                function boardController(
                    AppSettings, boardService
                ) {
                    var $ctrl = this;
                    $ctrl.screenLock = true;
                    $ctrl.playerSymbolId = 0;
                    $ctrl.score = {};

                    $ctrl.$onInit = function() {
                        $ctrl.symbols = boardService.symbols.query();
                        $ctrl.symbols.$promise.then(
                            function() {
                                $ctrl.symbols.forEach(function(symbol){
                                    $ctrl.score[symbol] = 0;
                                });
                                $ctrl.reset();
                            }
                        );

                        $ctrl.board = [];
                        $ctrl.stylePath = AppSettings.getTemplateDir() + '/board';

                        for(var i = 0; i < $ctrl.size; i++ ) {
                            for(var j = 0; j < $ctrl.size; j++) {
                                $ctrl.board.push({
                                    x: i,
                                    y: j,
                                    value: ''
                                });
                            }
                        }
                    };

                    /**
                     * Do 2 call
                     *      1. Get AI move
                     *      2. Check board state on winning
                     * @param field
                     */
                    $ctrl.move = function (field) {
                        if ('' === field.value && !$ctrl.screenLock) {
                            $ctrl.screenLock = true;
                            field.value = $ctrl.symbols[$ctrl.playerSymbolId];

                            boardService
                                .board
                                .makeMove({
                                    'board': $ctrl.board
                                })
                                .$promise
                                .then(function(nextField){
                                    $ctrl.board.forEach(function(field) {
                                        if (
                                            field.x === nextField.x
                                            && field.y === nextField.y
                                        ) {
                                            field.value = nextField.value;
                                        }
                                    });

                                    // check is winner combination present
                                    boardService
                                        .board
                                        .gameWinner({
                                            'board': $ctrl.board
                                        })
                                        .$promise
                                        .then(function(response){
                                            if (response.winner !== '') {
                                                $ctrl.score[response.winner]++;

                                                // disable fields
                                                $ctrl.board.forEach(function (field) {
                                                    if (field.value === '') {
                                                        field.value = '-';
                                                    }
                                                });

                                                // show message
                                                // why error just reuse styles and logic
                                                $ctrl.fetchErrorList = [{
                                                    code:   200,
                                                    detail: 'You are fail :('
                                                }];
                                            }

                                            // unlock action
                                            $ctrl.screenLock = false;
                                        })
                                        .catch(parseError);
                                })
                                .catch(parseError);
                        }
                    };

                    /**
                     * Reset all controller state except score
                     *
                     */
                    $ctrl.reset = function () {
                        $ctrl.screenLock = false;
                        $ctrl.board.forEach(function (field) {
                            field.value = '';
                        });
                        $ctrl.fetchErrorList = [];
                    };


                    /**
                     * Parse Errors from response
                     * @param response
                     */
                    var parseError = function (response) {
                        var code = response.status || response.data.error.code;

                        $ctrl.screenLock = false;
                        $ctrl.fetchErrorList = angular.isArray(response.data)
                            ? response.data
                            : [
                                {
                                    code:   code,
                                    detail: response.data.error.message
                                }
                            ];

                        if (
                            code === 401
                            && !angular.isUndefined(response.data[0])
                            && response.data[0].detail === 'Winner combination present'
                        ) {
                            var playerSymbol = $ctrl.symbols[$ctrl.playerSymbolId];
                            $ctrl.score[playerSymbol]++;
                            // disable fields
                            $ctrl.board.forEach(function (field) {
                                if (field.value === '') {
                                    field.value = '-';
                                }
                            });
                        }
                    }
                }
            ]
        });
})();