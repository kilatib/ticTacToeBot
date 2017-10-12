(function () {
    'use strict';

    angular.module('ticTacToeClient')
        .provider('AppSettings',
            function AppSettingsProvider () {
                var appSettings = {};

                this.initAppSettings = function (
                    apiUrl,
                    resourceList,
                    templateDir
                ) {
                    appSettings.apiUrl       = apiUrl;
                    appSettings.resourceList = resourceList;
                    appSettings.templateDir  = templateDir;
                };

                this.$get = [
                    function AppSettingsFactory () {
                        return {
                            getTemplateDir: function () {
                                return  appSettings.templateDir;
                            },

                            getApiResourceUrl: function (path) {
                                if (angular.isUndefined(appSettings.resourceList[path])) {
                                    new Error('Resource: ' + path + " wasn't provided in app config");
                                }

                                return appSettings.apiUrl + appSettings.resourceList[path];
                            }
                        }
                    }

                ];
            });
})();

