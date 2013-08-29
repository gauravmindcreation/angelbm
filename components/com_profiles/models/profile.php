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

jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_profiles/tables');

/**
 * Model
 */
class ProfilesModelProfile extends JModelLegacy
{

	public static $instance;

	public static function getInstance($type, $prefix = '', $config = array())
	{
		if (empty(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	protected $_item;
	protected $_gallery;

	/**
	 * Get the data for a banner
	 */
	function &getItem()
	{
		if (!isset($this->_item))
		{
			$cache = JFactory::getCache('com_profiles', '');

			$id = $this->getState('profiles.id');

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
	
	public function addHit()
	{
		$item	= $this->getItem();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$db->setQuery($query->insert('#__profiles_stats')->columns('family_id')->values($item->id))->execute();
	}
	
	function &getGallery()
	{
		if (!isset($this->_gallery))
		{
			$cache = JFactory::getCache('com_profiles', '');

			$id = $this->getState('profiles.id');

			$this->_gallery =  $cache->get($id.'_gallery');
			
			if ($this->_gallery === false) {
			
				$db		= $this->getDbo();
				$query	= $db->getQuery(true);
				$query->select(
					'a.*'
					);
				$query->from('#__profiles_photos AS a');
				$query->leftJoin('#__profiles_families AS b ON a.family_id = b.id');
				$query->where('b.id = '. (int) $id);
				$query->order('ordering ASC');
				//var_dump((string)$query);die();
				$db->setQuery((string)$query);
				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}

				$this->_gallery = $db->loadObjectList();
			}
		}
		return $this->_gallery;
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

		/*$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_contact')) &&  (!$user->authorise('core.edit', 'com_contact'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}*/
	}
	
	function getUsernameFromId($id)
	{
        // redirect to banner url
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)->select('a.username')->from('#__profiles_families as a')->where('a.id = '. (int) $id);
		
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$idObj = $db->loadObject();
		return $idObj->username;
	}
	
	function getIdFromUsername($username)
	{
		$username = str_replace(':', '-', $username);
        // redirect to banner url
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)->select('a.id')->from('#__profiles_families as a')->where("a.username = '{$username}'");
		
		$db->setQuery((string)$query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$idObj = $db->loadObject();

		return $idObj->id;
	}

}

