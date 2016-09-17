(function (windows, angular) {
    'use strict';
    angular.module('gundi.news.module').controller('NewsEditController', ['$scope', 'toastr', 'news.news', '$location', '$stateParams', function ($scope, toastr, newsRes, $location, $stateParams) {

        $scope.news = {};
        $scope.categories = {};
        $scope.goto = 'here';
        $scope.token = '';
        $scope.error = {};

        $scope.options = {
            allowedContent: true,
            entities: false
        };

        /**
         * *****************************
         * Get News entry and categories
         * *****************************
         */
        newsRes.get({id: $stateParams.id}).$promise.then(
            function (res) {
                res.news.published = res.news.published == 1;//for fix checkbox model value; about bug you read from https://github.com/angular/angular.js/issues/7109
                $scope.token = res.meta.token;
                $scope.news = res.news;
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
        $scope.save = function () {
            var data = {news: $scope.news, secure_token: $scope.token};
            data.id = $scope.news.id;
            data.news.published = + $scope.news.published; //boolean to integer
            newsRes.update(data).$promise.then(
                //success callback
                function (res) {
                    if (res.news != undefined) {
                        toastr.success('News successfully saved');
                        if ($scope.goto == 'back') {
                            $location.path('/news');
                        }
                        $scope.news.published = res.news.published == 1;
                    } else {
                        toastr.error('Unknone error');
                    }
                },
                //error callback
                function (res) {
                    $scope.token = res.data.meta.token;
                    $scope.error = res.data.errors;
                    toastr.error(res.data.sErrorMessage);
                }
            );
        };
    }]);
})(window, window.angular);