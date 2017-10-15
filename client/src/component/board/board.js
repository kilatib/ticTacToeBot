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

                    $ctrl.$onInit = function() {
                        $ctrl.symbols = boardService.symbols.query();
                        $ctrl.symbols.$promise.then(
                            function() {
                                $ctrl.screenLock = false;
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
                                    $ctrl.screenLock = false;
                                    $ctrl.board.forEach(function(field) {
                                        if (
                                            field.x === nextField.x
                                            && field.y === nextField.y
                                        ) {
                                            field.value = nextField.value;
                                        }
                                    });
                                })
                                .catch(function(response) {
                                    $ctrl.screenLock = false;
                                    $ctrl.fetchErrorList = angular.isArray(response.data)
                                        ? response.data
                                        : [
                                            {
                                                code:   response.data.error.code,
                                                detail: response.data.error.message
                                            }
                                        ];
                                });
                        }
                    };

                    $ctrl.reset = function () {
                        $ctrl.screenLock = false;
                        $ctrl.board.forEach(function (field) {
                            field.value = '';
                        });
                        $ctrl.fetchErrorList = [];
                    };
                }
            ]
        });
})();