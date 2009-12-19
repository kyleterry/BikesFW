<?php
/**
 *
 * Shortens the directory separator constant. 
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 *
 * Defines the project name.
 */
define('PROJECT', basename(dirname(dirname(__FILE__))));

/**
 *
 * Defines the path to the current project.
 */
define('PROJECT_PATH', dirname(dirname(__FILE__)));

/**
 *
 * Defines the path to the core library.
 */
define('LIB_PATH', dirname(dirname(dirname(dirname(__FILE__)))));

/**
 * Requires the bikes_Bootstrap class.
 */
require_once LIB_PATH . DS . 'bikes' . DS . 'Bootstrap.php';

/**
 * This will start the chain reaction to give you your precious fucking app.
 */

$bootstrap = new bikes_Bootstrap;
