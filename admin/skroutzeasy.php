<?php
/**
 * @package    SkroutzEasy component for Joomla 2.5.x and 3.x
 * @copyright  Copyright (c) 2012 Skroutz S.A. - http://www.skroutz.gr
 * @link       http://developers.skroutz.gr/oauth2
 * @license    MIT
 */

// No direct access
defined ('_JEXEC') or die ('Restricted access');

// Require the base controller
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname    = 'SkroutzEasyController'.$controller;
$controller   = new $classname();

// Perform the Request task
$controller->execute(JRequest::getWord('task'));

// Redirect if set by the controller
$controller->redirect();
