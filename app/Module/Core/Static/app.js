
angular.module('constants', [])
    .constant('MODULE_PATH', Gundi.Setting['core.path'] + 'app/Module/')
    .constant('BASE_PATH', Gundi.Setting['core.path'] + 'index.php/');

var GundiModule = angular.module('gundi', [
    'constants',
    'ui.router',
    'toastr',
    'infinite-scroll',
    'chieffancypants.loadingBar',
    'ui.bootstrap',
    'gundi.news.module'
]);

GundiModule.config(['$stateProvider', '$urlRouterProvider', '$httpProvider', function ($stateProvider, $urlRouterProvider, $httpProvider) {

    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    $httpProvider.defaults.transformResponse = [function (data, headers) {

        if (angular.isString(data)) {
            // Strip json vulnerability protection prefix and trim whitespace
            var tempData = data.replace(angular.JSON_PROTECTION_PREFIX, '').trim();
            if (tempData) {
                function isJson(tempData) {
                    var JSON_START = /^\[|^\{(?!\{)/;
                    var JSON_ENDS = {
                        '[': /]$/,
                        '{': /}$/
                    };
                    var jsonStart = tempData.match(JSON_START);
                    return jsonStart && JSON_ENDS[jsonStart[0]].test(tempData);
                }

                var contentType = headers('Content-Type');
                if ((contentType && (contentType.indexOf(angular.APPLICATION_JSON) === 0)) || isJson(tempData)) {
                    data = angular.fromJson(tempData);
                    Gundi['token'] = data.meta.token;
                }
            }
        }

        return data;
    }];

    $httpProvider.defaults.transformRequest = [function (data) {
        /**
         * transform to x-www-form-urlencoded string.
         * @param {Object} obj
         * @return {String}
         */
        var param = function (obj) {
            var query = '';
            var name, value, fullSubName, subValue, innerObj, i;

            for (name in obj) {
                value = obj[name];

                if (value instanceof Array) {
                    if (value.length > 0) {
                        for (i = 0; i < value.length; ++i) {
                            subValue = value[i];
                            fullSubName = name + '[' + i + ']';
                            innerObj = {};
                            innerObj[fullSubName] = subValue;
                            query += param(innerObj) + '&';
                        }
                    } else {
                        subValue = '';
                        fullSubName = name + '[' + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if (value instanceof Object) {
                    for (var subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if (value !== undefined && value !== null) {
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
                }
            }
            return query.length ? query.substr(0, query.length - 1) : query;
        };

        return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
    }];
}]).run(['$rootScope', 'MODULE_PATH', function ($rootScope, MODULE_PATH) {
    $rootScope.MODULE_PATH = MODULE_PATH
}]);