<?php

defined('GUNDI_DS') or define('GUNDI_DS', DIRECTORY_SEPARATOR);
defined('GUNDI_ROOT') or define('GUNDI_ROOT', __DIR__ . '/../..' . GUNDI_DS);
defined('GUNDI_APP_DIR') or define('GUNDI_APP_DIR', GUNDI_ROOT . 'app' . GUNDI_DS);
defined('GUNDI_DIR_MODULE') or define('GUNDI_DIR_MODULE', GUNDI_APP_DIR . 'Module' . GUNDI_DS);
defined('GUNDI_TMP_EXT') or define('GUNDI_TMP_EXT', '.php');
defined('GUNDI_DIR_SETTING') or define('GUNDI_DIR_SETTING', GUNDI_APP_DIR . 'Setting'. GUNDI_DS);
defined('GUNDI_THEMES_DIR') or define('GUNDI_THEMES_DIR', GUNDI_APP_DIR . 'Template'. GUNDI_DS);

include_once GUNDI_DIR_SETTING . 'Env.php';
$_SERVER['REQUEST_METHOD'] = 'GET';