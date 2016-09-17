<?php

use Symfony\Component\Translation\TranslatorInterface;

return [
    'singleton' => [
        [[Core\Library\Setting\Setting::class => 'config']],
        [[Core\Contract\Request\IRequest::class => 'Request'], Core\Library\Request\Request::class],
        [[Core\Library\Util\Url::class => 'Url']],
        [[Core\Library\Theme\Theme::class => 'Theme']],
        [[Core\Library\View\Html\Extension\Asset::class => 'Asset']],
        [[Core\Library\Token\Token::class => 'Token']],
        [[Core\Library\Session\Session::class => 'Session']],
        [[Core\Library\Dispatch\Dispatch::class => 'Dispatch']],
        [[Core\Library\View\Html\Extension\Block::class => 'Block']],
        [[Core\Library\Router\Router::class => 'Router']],
        [[Core\Library\View\Html\Extension\URI::class => 'Uri']],
        [[Core\Library\View\Html\Extension\File::class => 'File']],
        [\Core\Contract\View\IViewFactory::class, Core\Library\View\Factory::class],
        [[Core\Library\Error\Error::class => 'Error']],
        [[\Core\Library\Event\Dispatcher::class => 'events']],
        [[TranslatorInterface::class => 'translator'], \Core\Library\Language\Translator::class],
        [[Core\Library\Database\DatabaseManager::class => 'db']],
    ],
    'serviceProvider' => [
        \Illuminate\Validation\ValidationServiceProvider::class,
    ],
    'eventListen' => [
        [\Core\Library\View\Events::HTML_CREATED, '\Core\Library\View\Events@loadExt', 1000000000],
    ],
];
