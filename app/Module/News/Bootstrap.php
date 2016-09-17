<?php
return [
    'routes' => [
        'resource' => [ //REST FULL routes
            ['/categories', Module\News\Component\Controller\Category::class],
            ['/news', Module\News\Component\Controller\News::class],
        ],
        'get' => [ //get routes
            ['', '\Module\News\Component\Controller\Category@index'],
            ['/news/(:id)/publish', '\Module\News\Component\Controller\News@publish'],
            ['/news/(:id)/hide', '\Module\News\Component\Controller\News@hide'],
        ]
    ],
    'blocks' => [
        ['news_top_menu', '*', 'Module\News\Component\Block\Menu@topMenu'] //top menu block
    ],
];
