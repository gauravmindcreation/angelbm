<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

/**
 * @param	array	A named array
 * @return	array
 */

JLoader::import('components.com_profiles.helpers.profiles', JPATH_SITE);

 
function ProfilesBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['task'])) {
		//$segments[] = $query['task'];
		unset($query['task']);
	}
	if (isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['id'])) {
		$segments[] = ProfilesHelper::getUsernameFromId($query['id']);
		unset($query['id']);
	}

	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/banners/task/id/Itemid
 *
 * index.php?/banners/id/Itemid
 */
function ProfilesParseRoute($segments)
{
	$vars = array();

	// view is always the first element of the array
	$count = count($segments);
	
	//$vars['task'] = $segments[0];
	$vars['view'] = $segments[0];
	$vars['id'] = ProfilesHelper::getIdFromUsername($segments[1]);

	return $vars;
}
