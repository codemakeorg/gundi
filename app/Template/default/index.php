<!DOCTYPE html>
<html lang="<?=Gundi()->config->getParam('core.default_lang_code'); ?>" data-ng-app="gundi">
<head>
    <title>News Portal</title>
    <meta charset="utf-8">

    <script type="text/javascript">
        Gundi = {
            Setting: {}
        };
        Gundi.Setting['core.path'] = '<?= Gundi()->config->getParam('core.path'); ?>';
    </script>

    <?php

    $this->addStatic([
        'bootstrap.min.css' => 'app/Template/default/css/',
        'bootstrap-extend.min.css' => 'app/Template/default/css/',
        'style.css' => 'app/Template/default/css/',
        'web-icons.min.css' => 'app/Template/default/fonts/web-icons/',
    ]);

    $this->addStatic([
        'jquery/jquery.min.js' => 'static/',
        'bootstrap/bootstrap.min.js' => 'static/',

        'angular/angular.min.js' => 'static/',
        'angular-ui-router/angular-ui-router.min.js' => 'static/',
        'angular-infinite/ng-infinite-scroll.min.js' => 'static/',
        'angular-ui-bootstrap/ui-bootstrap.min.js' => 'static/',
        'angular-ui-bootstrap/ui-bootstrap-tpls.min.js' => 'static/',
        'https://npmcdn.com/angular-toastr/dist/angular-toastr.tpls.js',
        'https://npmcdn.com/angular-toastr/dist/angular-toastr.css',
        '//cdnjs.cloudflare.com/ajax/libs/angular-loading-bar/0.9.0/loading-bar.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/angular-loading-bar/0.9.0/loading-bar.min.js',
        'http://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.3/angular-resource.min.js',
        '//cdn.ckeditor.com/4.5.9/standard/ckeditor.js',
        'ng-ckeditor/ng-ckeditor-1.0.1.min.js' => 'static/',
    ]);

    //modules controllers
    $this->addStatic(
        [
            'Core/Static/app.js' => 'app/Module/',
            'gundi.news.module.js' => 'app/Module/News/Static/',
            'Controller/CategoryListController.js' => 'app/Module/News/Static/',
            'Controller/CategoryNewController.js' => 'app/Module/News/Static/',
            'Controller/CategoryEditController.js' => 'app/Module/News/Static/',
            'Controller/NewsListController.js' => 'app/Module/News/Static/',
            'Controller/NewsNewController.js' => 'app/Module/News/Static/',
            'Controller/NewsEditController.js' => 'app/Module/News/Static/',
            'Controller/NewsViewController.js' => 'app/Module/News/Static/',
        ]
    );
    ?>

    <?= $this->css(); ?>
</head>
<body>
    <?= $this->block('news_top_menu') ?>
    <div class="container">
        <div data-ui-view class="page"></div>
    </div>
    <?= $this->js(); ?>
</body>
</html>