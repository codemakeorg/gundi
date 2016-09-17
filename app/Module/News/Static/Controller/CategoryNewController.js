(function (windows, angular) {
    'use strict';
    angular.module('gundi.news.module').controller('CategoryNewController', ['$scope', 'toastr', 'news.category', '$location', function ($scope, toastr, categoryRes, $location) {

        var category = new categoryRes();
        category.name = '';
        category.description = '';

        $scope.category = category;
        $scope.goto = 'here';
        $scope.token = '';
        $scope.error = {};

        /**
         * ****************************
         * Get add category form data
         * from server
         * ****************************
         */
        categoryRes.add().$promise.then(
            function (res) {
                $scope.token = res.meta.token;
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
            var data = {category: $scope.category, secure_token: $scope.token};
            categoryRes.save(data).$promise.then(
                //success
                function (res) {
                    if (res.category != undefined) {

                        toastr.success('Category successfully added');
                        $location.path('/categories');

                    } else {
                        toastr.error('Unknone error');
                    }
                },
                //error
                function (res) {
                    $scope.token = res.data.meta.token;
                    $scope.error = res.data.errors;
                    toastr.error(res.data.sErrorMessage);
                }
            );
        }
    }]);
})(window, window.angular);