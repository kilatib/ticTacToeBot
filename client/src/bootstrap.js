(function () {
    'use strict';
    angular.module("ticTacToeClient",  [ 'ngResource' ]);

    angular.module('ticTacToeClient').config([
        '$httpProvider', 'AppSettingsProvider', '$windowProvider',
        function($httpProvider, AppSettingsProvider, $windowProvider) {
            $httpProvider.interceptors.push('$apiResourceHttpInterceptor');  // rewrite api source link
            $httpProvider.interceptors.push('$templateUrlHttpInterceptor');  // create right path to templates

            //
            // init app
            var $window = $windowProvider.$get();
            AppSettingsProvider.initAppSettings(
                $window.APP.apiUrl,
                $window.APP.resourceList,
                $window.APP.templateDir
            );
        }
    ]);
})();
