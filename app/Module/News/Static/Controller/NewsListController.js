(function (windows, angular) {
    'use strict';
    angular.module('gundi.news.module').controller('NewsListController', ['$scope', 'toastr', 'news.news', '$location', 'filterParams', '$sce',
        function ($scope, toastr, newsRes, $location, filterParams, $sce) {

            $scope.scrollDisable = false;
            $scope.sortType = 'id';
            $scope.sortReverse = true;
            $scope.search = {};
            $scope.news = [];
            $scope.categories = [];

            var currentPage = 1;
            var token = '';

            /**
             * **********************************
             * Lazy load pagination action
             * **********************************
             */
            $scope.load = function () {

                if ($scope.scrollDisable) return;
                $scope.scrollDisable = true;
                var params = filterParams.transform($scope.search);
                params.page = currentPage;
                //get list form backend
                newsRes
                    .list(params)
                    .$promise.then(function (response) {

                    var news = response.news;
                    token = response.meta.token;
                    $scope.scrollDisable = news.last_page == currentPage;
                    currentPage = news.current_page + 1;
                    $scope.news = news.data;
                    $scope.categories = response.categories;
                });
            };

            /**
             * ************************************
             * Get list news by filter action
             * ************************************
             */
            $scope.filter = function () {

                currentPage = 1;
                var searchRequest = $scope.search;
                if ($scope.search.published__equal != undefined) {
                    searchRequest.published__equal = +$scope.search.published__equal; //boolean to integer
                }

                newsRes
                    .list(filterParams.transform($scope.search))
                    .$promise.then(function (response) {

                    var news = response.news;
                    token = response.meta.token;
                    $scope.news = news.data;
                    if ($scope.search.published__equal != undefined) {
                        searchRequest.published__equal = $scope.search.published__equal == 1; //integer to boolean
                    }

                });

            };


            /**
             * **********************************
             * Reset Filter action
             * **********************************
             */
            $scope.reset = function () {
                $scope.search = {};
                currentPage = 1;
                $scope.scrollDisable = false;
                $scope.news = [];
                $scope.load();
            };

            /**
             * ***********************************
             * Delete Category Action
             * ***********************************
             * @param id
             */
            $scope.delete = function (id) {
                newsRes.delete(
                    {id: id},
                    function (res) {
                        angular.element('#entry_' + id).remove();
                        toastr.success(res.message, 'News successfully deleted');
                    }, function (res) {
                        toastr.error(res.statusText, 'Can`t delete');
                        token = response.data.meta.token;
                    });
            };

            /**
             * ****************************************
             * Publish action
             * ****************************************
             * @param id
             */
            $scope.publish = function (id) {
                newsRes.publish(
                    {id: id},
                    function (res) {
                        toastr.success(res.message, 'News successfully published');
                        angular.forEach($scope.news, function (entry, key) {
                            if (entry.id == res.news.id) {
                                $scope.news[key] = res.news;
                            }
                        });
                    }, function (res) {
                        toastr.error(res.statusText, 'Can`t delete');
                        token = response.data.meta.token;
                    });
            };

            /**
             * ****************************************
             * unpublish action
             * ****************************************
             * @param id
             */
            $scope.hide = function (id) {
                newsRes.hide(
                    {id: id},
                    function (res) {
                        console.log(res);
                        toastr.success(res.message, 'News successfully unpublished');
                        angular.forEach($scope.news, function (entry, key) {
                            if (entry.id == res.news.id) {
                                $scope.news[key] = res.news;
                            }
                        });
                    }, function (res) {
                        toastr.error(res.statusText, 'Can`t delete');
                        token = response.data.meta.token;
                    });
            };

            $scope.load();
        }]);
})(window, window.angular);