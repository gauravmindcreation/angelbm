<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_profiles')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Set the custom DB Object
$oldDbo = JFactory::getDbo();

$conf = JComponentHelper::getParams('com_profiles');

$options = array(
	'driver' => $conf->get('dbtype'),
	'host' => $conf->get('host'),
	'user' => $conf->get('user'),
	'password' => $conf->get('password'),
	'database' => $conf->get('db'),
	'prefix' => $conf->get('dbprefix')
);

try
{
	JFactory::$database = JDatabaseDriver::getInstance($options);
}
catch (RuntimeException $e)
{
	if (!headers_sent())
	{
		header('HTTP/1.1 500 Internal Server Error');
	}

	jexit('Database Error: ' . $e->getMessage());
}

JLoader::import('helpers.helpers', JPATH_COMPONENT);
JLoader::import('helpers.profiles', JPATH_COMPONENT);

require_once('libs/image.php');
require_once('libs/image/driver.php');
require_once('libs/image/gd.php');

$controller	= JControllerLegacy::getInstance('Profiles');
$controller->execute(JRequest::getCmd('task'));

// Reset to previous connection
JFactory::$database = $oldDbo;

$controller->redirect();
