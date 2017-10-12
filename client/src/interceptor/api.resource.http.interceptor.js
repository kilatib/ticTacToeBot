angular.module('ticTacToeClient').factory('$apiResourceHttpInterceptor',[
    'AppSettings',
    function(AppSettings) {
        return {
            'request': function(config) {
                if (
                    config.url.indexOf('http://')     < 0
                    && config.url.indexOf('https://') < 0
                    && config.url.substr(-5, 5) !== '.html' // disable template *.html
                ) {
                    config.url = AppSettings.getApiResourceUrl(config.url);
                }
                return config;
            }
        };
    }
]);