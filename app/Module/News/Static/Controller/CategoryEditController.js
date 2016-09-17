(function (windows, angular) {
    'use strict';
    angular.module('gundi.news.module').controller('CategoryEditController', ['$scope', 'toastr', 'news.category', '$location', '$stateParams', function ($scope, toastr, categoryRes, $location, $stateParams) {

        $scope.category = {};
        $scope.goto = 'here';
        $scope.token = '';
        $scope.error = {};

        $scope.options = {
            allowedContent: true,
            entities: false
        };

        /**
         * *****************************
         * Get Category
         * *****************************
         */
        categoryRes.get({id: $stateParams.id}).$promise.then(
            function (res) {
                console.log(res);
                $scope.token = res.meta.token;
                $scope.category = res.category;
            },
            function (res) {
                toastr.error(res.statusText);
            }
        );

        /**
         * ******************************
         * Update category
         * ******************************
         */
        $scope.save = function () {
            var data = {category: $scope.category, secure_token: $scope.token};
            data.id = $scope.category.id;

            categoryRes.update(data).$promise.then(
                //success callback
                function (res) {
                    if (res.category != undefined) {
                        toastr.success('Category successfully saved');
                        if ($scope.goto == 'back') {
                            $location.path('/categories');
                        }
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
        }
    }]);
})(window, window.angular);