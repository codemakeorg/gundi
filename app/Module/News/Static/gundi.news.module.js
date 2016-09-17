(function (windows, angular) {
    'use strict';

    /**
     * ******************************
     * Register News Module
     * ******************************
     */
    var newsModule = angular.module(
        'gundi.news.module',
        ['ui.bootstrap', 'ui.router', 'infinite-scroll', 'ngResource', 'ckeditor']
    );

    newsModule.config(
        ['$stateProvider', 'MODULE_PATH', '$urlRouterProvider',
            function ($stateProvider, MODULE_PATH, $urlRouterProvider) {
                $urlRouterProvider.otherwise('/news');
                /**
                 * ******************************
                 * register routes of news module
                 * ******************************
                 */
                $stateProvider
                    .state('categories', {
                        url: '/categories',
                        templateUrl: MODULE_PATH + 'News/Static/View/category.list.html',
                        controller: 'CategoryListController'
                    })
                    .state('category_new', {
                        url: '/categories/new',
                        templateUrl: MODULE_PATH + 'News/Static/View/category.new.html',
                        controller: 'CategoryNewController'
                    })
                    .state('category_edit', {
                        url: '/categories/:id/edit',
                        templateUrl: MODULE_PATH + 'News/Static/View/category.edit.html',
                        controller: 'CategoryEditController'
                    })
                    .state('news', {
                        url: '/news',
                        templateUrl: MODULE_PATH + 'News/Static/View/news.list.html',
                        controller: 'NewsListController'
                    })
                    .state('news_new', {
                        url: '/news/new',
                        templateUrl: MODULE_PATH + 'News/Static/View/news.new.html',
                        controller: 'NewsNewController'
                    })
                    .state('news_edit', {
                        url: '/news/:id/edit',
                        templateUrl: MODULE_PATH + 'News/Static/View/news.edit.html',
                        controller: 'NewsEditController'
                    })
                    .state('news_view', {
                        url: '/news/:id',
                        templateUrl: MODULE_PATH + 'News/Static/View/news.view.html',
                        controller: 'NewsViewController'
                    });
            }
        ]
    );

    /**
     * ***********************************
     * The service filterParams transform
     * filter request for backend
     * ***********************************
     */
    newsModule.service('filterParams', function () {
        return {
            transform: function (filter) {
                var params = {};

                for (var name in filter) {
                    if (params['filter[' + name + ']'] == undefined) {
                        params['filter[' + name + ']'] = '';
                    }
                    params['filter[' + name + ']'] = filter[name];
                }

                return params;
            }
        };
    });

    /**
     * *************************************
     * REST FULL resource for category object
     * *************************************
     */
    newsModule.factory('news.category', ['$resource', 'BASE_PATH', function ($resource, BASE_PATH) {
        return $resource(BASE_PATH + 'categories/:id.json',
            {
                id: "@id"
            },
            {
                update: {
                    method: 'PUT',
                    url: BASE_PATH + 'categories/:id.json'

                },
                list: {
                    method: 'GET'
                },
                add: {
                    method: 'GET',
                    url: BASE_PATH + 'categories/new.json'
                }
            });
    }]);

    /**
     * *************************************
     * REST FULL resource for news object
     * *************************************
     */
    newsModule.factory('news.news', ['$resource', 'BASE_PATH', function ($resource, BASE_PATH) {
        return $resource(BASE_PATH + 'news/:id.json',
            {
                id: "@id"
            },
            {
                update: {
                    method: 'PUT',
                    url: BASE_PATH + 'news/:id.json'

                },
                list: {
                    method: 'GET'
                },
                add: {
                    method: 'GET',
                    url: BASE_PATH + 'news/new.json'
                },
                publish: {
                    method: 'GET',
                    url: BASE_PATH + 'news/:id/publish.json'
                },
                hide: {
                    method: 'GET',
                    url: BASE_PATH + 'news/:id/hide.json'
                }
            });
    }]);

    /**
     * *************************************
     * boolean transform to yes|no
     * *************************************
     */
    newsModule.filter('boolean', function() {
        return function(input) {
            return (input == 0 || input == '0') ?  'no' : 'yes';
        }
    });

})(window, window.angular);
