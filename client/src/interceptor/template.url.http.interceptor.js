angular.module('ticTacToeClient').factory('$templateUrlHttpInterceptor',[
    'AppSettings',
    function(AppSettings) {
        return {
            'request': function(config) {
                if (
                    config.url.substr(-5, 5) === '.html'
                ) {
                    config.url = AppSettings.getTemplateDir() + config.url;
                }
                return config;
            }
        };
    }
]);