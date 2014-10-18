<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @global string FILES - Relative path to the public files directory.
 */
define('FILES', WWW_ROOT . 'files' . DS);

/**
 * @global string FILES_URL - Relative URL to the public files directory.
 */
define('FILES_URL', 'files/');

/**
 * @var array System - Variable with information about the system.
 */
Configure::write('System.name', 'Epik');
Configure::write('System.acronym', 'Edutainment by Playing and Interacting with Knowledge');
Configure::write('System.author', 'Bruno Sampaio');
Configure::write('System.version', '1.0');
Configure::write('System.icon', '/img/icon.png');
Configure::write('System.email', 'noreply@epik.com');
Configure::write('System.sockets', 'http://localhost:3000');

/**
 * @var array Sections - Variable with information about dashboard and footer sections.
 */
// Dashboard Sections
Configure::write('Sections.dashboard.activities', __('Activities'));
Configure::write('Sections.dashboard.games', __('Games'));
Configure::write('Sections.dashboard.projects', __('Projects'));
Configure::write('Sections.dashboard.resources', __('Resources'));

// Footer Sections
Configure::write('Sections.footer.howtos.index', __('How to ... ?'));
Configure::write('Sections.footer.howtos.login', __('How to login from a LMS?'));
Configure::write('Sections.footer.howtos.import', __('How to import contents from a LMS?'));
Configure::write('Sections.footer.howtos.develop', __('How to develop an %s game?', Configure::read('System.name')));
Configure::write('Sections.footer.howtos.distribute', __('How to distribute games as LMS activities?'));
Configure::write('Sections.footer.help', __('Help'));
Configure::write('Sections.footer.about', __('About'));
Configure::write('Sections.footer.credits', __('Credits'));

/**
 * @var array Folders - Variable with the relative paths for the folders where the files and images for games, projects, resources, scenarios, templates, and users are stored.
 */
Configure::write('Folders.files.games', 'games');
Configure::write('Folders.files.projects', 'projects');
Configure::write('Folders.files.resources', 'resources');
Configure::write('Folders.files.scenarios', 'scenarios');
Configure::write('Folders.files.templates', 'templates');
Configure::write('Folders.img.templates', 'templates');
Configure::write('Folders.img.scenarios', 'scenarios');
Configure::write('Folders.img.users', 'users');


/**
 * @var array Files - Variable with information about file sizes and types allowed.
 */
Configure::write('Files.size.application', 2097152);
Configure::write('Files.size.audio', 15728640);
Configure::write('Files.size.image', 2097152);
Configure::write('Files.size.video', 15728640);
Configure::write('Files.types.application', array('pdf'));
Configure::write('Files.types.audio', array('mpeg', 'mpg', 'mp3', 'webm'));
Configure::write('Files.types.image', array('gif', 'jpg', 'jpeg', 'pjpeg', 'png'));
Configure::write('Files.types.video', array('mpeg', 'mp4', 'webm'));


/**
 * @var array Defaults - Variable with the relative path to folders where default files for games, templates and users are stored.
 */
Configure::write('Default.template.name', 'Blank');
Configure::write('Default.user.img', 'default/user.png');
Configure::write('Default.template.img', 'default/template.png');
Configure::write('Default.game.img', 'default/game');
Configure::write('Default.game.files', 'default/game');

/**
 * @var array Operations - Variable with the name for each type of operation that can be performed and that must be logged on the database.
 */
Configure::write('Operations.add', 'Add');
Configure::write('Operations.edit', 'Edit');
Configure::write('Operations.delete', 'Delete');
Configure::write('Operations.import', 'Import');
Configure::write('Operations.reload', 'Reload');

// Exceptions
Configure::write('Exception.renderer', 'AppExceptionRenderer');
App::uses('InvalidRequestException', 'Error/Exception');
App::uses('PermissionDeniedException', 'Error/Exception');
App::uses('SecurityBreachException', 'Error/Exception');

// Plugins
CakePlugin::loadAll();

/**
 * Cache Engine Configuration
 * Default settings provided below
 *
 * File storage engine.
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'File', //[required]
 *		'duration'=> 3600, //[optional]
 *		'probability'=> 100, //[optional]
 * 		'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
 * 		'prefix' => 'cake_', //[optional]  prefix every cache file with this string
 * 		'lock' => false, //[optional]  use file locking
 * 		'serialize' => true, // [optional]
 * 		'mask' => 0666, // [optional] permission mask to use when creating cache files
 *	));
 *
 * APC (http://pecl.php.net/package/APC)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Apc', //[required]
 *		'duration'=> 3600, //[optional]
 *		'probability'=> 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 *
 * Xcache (http://xcache.lighttpd.net/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Xcache', //[required]
 *		'duration'=> 3600, //[optional]
 *		'probability'=> 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
 *		'user' => 'user', //user from xcache.admin.user settings
 *		'password' => 'password', //plaintext password (xcache.admin.pass)
 *	));
 *
 * Memcache (http://memcached.org/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Memcache', //[required]
 *		'duration'=> 3600, //[optional]
 *		'probability'=> 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 * 		'servers' => array(
 * 			'127.0.0.1:11211' // localhost, default port 11211
 * 		), //[optional]
 * 		'persistent' => true, // [optional] set this to false for non-persistent connections
 * 		'compress' => false, // [optional] compress data in Memcache (slower, but uses less memory)
 *	));
 *
 *  Wincache (http://php.net/wincache)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Wincache', //[required]
 *		'duration'=> 3600, //[optional]
 *		'probability'=> 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 *
 * Redis (http://http://redis.io/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Redis', //[required]
 *		'duration'=> 3600, //[optional]
 *		'probability'=> 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *		'server' => '127.0.0.1' // localhost
 *		'port' => 6379 // default port 6379
 *		'timeout' => 0 // timeout in seconds, 0 = unlimited
 *		'persistent' => true, // [optional] set this to false for non-persistent connections
 *	));
 */
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */


/**
 * You can attach event listeners to the request lifecyle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));

CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));
