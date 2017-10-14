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

                    $ctrl.symbols = ['X', 'O'];
                    $ctrl.screenLock = false;

                    $ctrl.$onInit = function() {
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
                            field.value = $ctrl.symbols.reverse()[0];

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
                                    $ctrl.fetchErrorList = response.data;
                                });
                        }
                    };

                    $ctrl.reset = function () {
                        $ctrl.board.forEach(function (field) {
                            field.value = '';
                        });
                        $ctrl.fetchErrorList = [];
                    };
                }
            ]
        });
})();