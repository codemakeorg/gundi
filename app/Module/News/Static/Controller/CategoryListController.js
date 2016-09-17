(function (windows, angular) {
    'use strict';
    angular.module('gundi.news.module').controller('CategoryListController', ['$scope', 'toastr', 'news.category', '$location', 'filterParams', '$sce',
        function ($scope, toastr, categoryRes, $location, filterParams, $sce) {

        $scope.scrollDisable = false;
        $scope.sortType = 'id';
        $scope.sortReverse = true;
        $scope.search = {};
        $scope.categories = [];

        var currentPage = 1;
        var token = '';

        /**
         * Escape html
         * @param html_code
         * @returns {*}
         */
        $scope.renderHtml = function(html_code)
        {
            return $sce.trustAsHtml(html_code);
        };

        /**
         * **********************************
         * Lazy load pagination
         * **********************************
         */
        $scope.load = function () {

            if ($scope.scrollDisable) return;
            $scope.scrollDisable = true;
            var params = filterParams.transform($scope.search);
            params.page = currentPage;
            //get list form backend
            categoryRes
                .list(params)
                .$promise.then(function (response) {

                var categories = response.categories;
                token = response.meta.token;
                $scope.scrollDisable = categories.last_page == currentPage;
                currentPage = categories.current_page + 1;
                $scope.categories = categories.data;
            });
        };

        /**
         * ************************************
         * Get list categories by filter
         * ************************************
         */
        $scope.filter = function () {
            currentPage = 1;

            categoryRes
                .list(filterParams.transform($scope.search))
                .$promise.then(function (response) {

                var categories = response.categories;
                token = response.meta.token;
                $scope.categories = categories.data;

            });

        };


        /**
         * **********************************
         * Reset Filter
         * **********************************
         */
        $scope.reset = function () {
            $scope.search = {};
            currentPage = 1;
            $scope.scrollDisable = false;
            $scope.categories = [];
            $scope.load();
        };

        /**
         * ***********************************
         * Delete Category
         * ***********************************
         * @param id
         */
        $scope.delete = function (id) {
            categoryRes.delete(
                {id: id},
                function (res) {
                    angular.element('#category_' + id).remove();
                    toastr.success(res.message, 'Category successfully deleted');
                }, function (res) {
                    toastr.error(res.statusText, 'Can`t delete');
                    token = response.data.meta.token;
                });
        };

        $scope.load();
    }]);
})(window, window.angular);