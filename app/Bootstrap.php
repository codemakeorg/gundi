<?php

use Core\Library\Gundi\Gundi;
use Core\Library\Module\Module;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Set error reporting enviroment
 */
error_reporting((GUNDI_DEBUG ? E_ALL | E_STRICT : 0));

/**
 * Turn on custom error handling.
 */

set_error_handler('Core\Library\Error\Error::errorHandler');

/**
 * Register services
 */
$oGundi = new Gundi();
$oGundi->singleton([Core\Contract\Gundi\IBootstrap::class => 'Bootstrap'], Core\Library\Gundi\Bootstrap::class);

$aAppSetting = include(GUNDI_DIR_SETTING . 'App.php');
$oGundi['Bootstrap']->boot($aAppSetting);


/**
 * Connect to DB
 */
$oCapsule = new Capsule($oGundi);
$oCapsule->addConnection($oGundi['config']['database.connections'][GUNDI_DB_DRiVER]);

$oCapsule->setAsGlobal();
$oCapsule->setEventDispatcher($oGundi['events']);
$oCapsule->getConnection()->setEventDispatcher($oGundi['events']);
$oCapsule->bootEloquent();
/**
 * Set time zone of server
 */
date_default_timezone_set($oGundi->config->getParam('core.default_time_zone_offset'));

/**
 * Start sessions.
 */
$oGundi->Session->start();

/**
 * check spoofing session
 */
function generateSessionSecKey()
{
    return md5(Gundi()->config->getParam('core.session_prefix') . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
}

$sSessionSecurityKey = $oGundi->Session->get('secKey');
$sRightSessionSecurityKey = generateSessionSecKey();
if (!empty($sSessionSecurityKey) && $sSessionSecurityKey != $sRightSessionSecurityKey) {
    die('ACCESS DENY!');
} else {
    $oGundi->Session->set('secKey', $sRightSessionSecurityKey);
}

$oGundi->Router->setBasePath($oGundi->config->getParam('core.folder') . GUNDI_INDEX_FILE);

/**
 * check token if is post
 */
if ($oGundi->Request->isPost()) {
    if (!$oGundi->Token->isValid()) {
        die('The tokens do not match');
    }
}

Module::loadCoreModules();

/**
 * run handler
 */
Gundi()->Theme->setLayout('index');
Gundi()->Dispatch->dispatch();

