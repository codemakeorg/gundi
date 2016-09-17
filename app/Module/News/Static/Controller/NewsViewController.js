(function (windows, angular) {
    'use strict';
    angular
        .module('gundi.news.module')
        .controller('NewsViewController', ['$scope', 'toastr', 'news.news', '$stateParams', '$sce',

            function ($scope, toastr, newsRes, $stateParams, $sce) {

                $scope.news = {};

                /**
                 * Escape html
                 * @param html_code
                 * @returns {*}
                 */
                $scope.renderHtml = function (html_code) {
                    return $sce.trustAsHtml(html_code);
                };

                /**
                 * ****************************
                 * get news from server
                 * ****************************
                 */
                newsRes.get({id: $stateParams.id}).$promise.then(
                    function (res) {
                        $scope.news = res.news;
                    },
                    function (res) {
                        toastr.error(res.statusText);
                    }
                );
            }
        ]);
})(window, window.angular);