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

                    $ctrl.$onInit = function() {
                        $ctrl.board = [];
                        $ctrl.stylePath = AppSettings.getTemplateDir() + '/board/board.css';

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
                        if ('' === field.value ) {
                            field.value = $ctrl.symbols.reverse()[0];

                            boardService
                                .field
                                .move({
                                        'symbol': field.value
                                     },
                                    $ctrl.board
                                )
                                .$promise
                                .then(function(){
                                    var newFiled =  boardService
                                        .field
                                        .calculate();
                                    newFiled
                                        .$promise
                                        .then(function(){

                                        })
                                        .catch(function(errorList){
                                            $ctrl.fetchErrorList = errorList;
                                        });
                                })
                                .catch(function(errorList) {
                                    $ctrl.fetchErrorList = errorList;
                                });
                        }
                    };

                    $ctrl.reset = function () {
                        $ctrl.board.forEach(function (field) {
                            field.value = '';
                        });
                    };
                }
            ]
        });
})();