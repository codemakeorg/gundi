(function (windows, angular) {
    'use strict';
    angular.module('gundi.news.module').controller('NewsNewController', ['$scope', 'toastr', 'news.news', '$location', function ($scope, toastr, newsRes, $location) {

        var news = new newsRes();

        $scope.news = news;
        $scope.categories = [];
        $scope.token = '';
        $scope.error = {};

        /**
         * ****************************
         * get news add form data
         * ****************************
         */
        newsRes.add().$promise.then(
            function (res) {
                $scope.token = res.meta.token;
                $scope.categories = res.categories;
            },
            function (res) {
                toastr.error(res.statusText);
            }
        );

        /**
         * ****************************
         * Send to server
         * ****************************
         */
        $scope.add = function () {
            var data = {news: $scope.news, secure_token: $scope.token};
            data.news.published = $scope.news.published == true ? 1 : 0;
            newsRes.save(data).$promise.then(
                function (res) {
                    if (res.news != undefined) {

                        toastr.success('News successfully added');
                        $location.path('/news');

                    } else {
                        toastr.error('Unknone error');
                    }
                },
                function (res) {
                    $scope.token = res.data.meta.token;
                    $scope.error = res.data.errors;
                    toastr.error(res.data.sErrorMessage);
                }
            );
        }
    }]);
})(window, window.angular);