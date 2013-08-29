<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_profiles/tables');

/**
 * Model
 */
class ProfilesModelContact extends JModelLegacy
{	
	protected $_item;

	/**
	 * Get the data for a banner
	 */
	function getItem($id = null)
	{
		if (!isset($this->_item))
		{
			$cache = JFactory::getCache('com_profiles', '');

			if(!$id)
			{
				$id = $this->getState('profiles.id');
			}

			$this->_item =  $cache->get($id);

			if ($this->_item === false) {
				
                // redirect to banner url
				$db		= $this->getDbo();
				$query	= $db->getQuery(true);
				$query->select(
					'a.*'
					);
				$query->from('#__profiles_families AS a');
				$query->where('a.id = ' . (int) $id);

				$db->setQuery((string)$query);
				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}

				$this->_item = $db->loadObject();
				$cache->store($this->_item, $id);
			}
		}
		return $this->_item;
	}
	
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('profiles.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

}

