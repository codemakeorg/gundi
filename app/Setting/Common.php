<?php
//database configuration
$_CONF['database.driver'] = 'mysql';
$_CONF['database.fetch'] = PDO::FETCH_CLASS;
$_CONF['database.connections'] = [
    'mysql' => [
        'driver' => GUNDI_DB_DRiVER,
        'host' => GUNDI_DB_HOST,
        'database' => GUNDI_DB_NAME,
        'port' => GUNDI_DB_PORT,
        'username' => GUNDI_DB_USER,
        'password' => GUNDI_DB_PASS,
        'charset' => GUNDI_DB_CHARSET,
        'collation' => GUNDI_DB_COLLATION,
    ]
];

//framework conf
$_CONF['core.http'] = 'http://';
$_CONF['core.https'] = 'https://';
$_CONF['core.protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? $_CONF['core.https'] : $_CONF['core.http']);

$_CONF['core.host'] = GUNDI_HOST;
$_CONF['core.folder'] = GUNDI_FOLDER;


$_CONF['core.dir_module'] = GUNDI_DIR_MODULE;
$_CONF['core.tmp_ext'] = GUNDI_TMP_EXT;
$_CONF['core.themes_dir'] = GUNDI_THEMES_DIR;
$_CONF['core.app_dir'] = GUNDI_APP_DIR;

$_CONF['core.path'] = $_CONF['core.protocol'] . GUNDI_HOST . GUNDI_FOLDER;

$_CONF['core.prefix'] = 'GUNDI_';
$_CONF['core.session_prefix'] = 'GUNDI_';
$_CONF['core.default_session_container'] = 'GUNDI';
$_CONF['core.servers'] = [];
$_CONF['core.modules'] = ['Core', 'News'];