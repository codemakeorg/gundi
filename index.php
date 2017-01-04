<?php
if (version_compare(phpversion(), '5', '<') === true) {
    exit('Gundi 0.0.1 or higher requires PHP 5.5 or newer.');
}
define('GUNDI', true);
define('GUNDI_DS', DIRECTORY_SEPARATOR);
define('GUNDI_TIME', time());
define('GUNDI_ROOT', dirname(__FILE__) . GUNDI_DS);
define('GUNDI_APP_DIR', GUNDI_ROOT . 'app' . GUNDI_DS);

if (file_exists(GUNDI_ROOT . 'vendor' . GUNDI_DS . 'autoload.php')) {
    require_once(GUNDI_ROOT . 'vendor' . GUNDI_DS . 'autoload.php');
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    die;
}

require_once(GUNDI_ROOT . 'app' . GUNDI_DS . 'Setting' . GUNDI_DS . 'Constant.php');
require_once(GUNDI_ROOT . 'app' . GUNDI_DS . 'Setting' . GUNDI_DS . 'Env.php');
require_once(GUNDI_ROOT . 'app' . GUNDI_DS . 'Setting' . GUNDI_DS . 'App.php');
require_once(GUNDI_ROOT . 'app' . GUNDI_DS . 'Bootstrap.php'); //start app
