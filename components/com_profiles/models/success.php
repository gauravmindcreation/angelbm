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
jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_profiles/tables');

/**
 * Model
 */
class ProfilesModelSuccess extends JModelList
{
	protected $_items;
	protected $_title = null;
	protected $_pagination = null;
	
	protected $_limit;
	protected $_start;
	
	public function __construct()
	{
		parent::__construct();
		
		$app = JFactory::getApplication();
		
		// Have to set limit manually below, because Joomla doesn't know how to fucking setState properly.
		// $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', 10, 'int');
		
		$start = JRequest::getVar('start', 0, '', 'int');
		
		$this->_limit = 5;
		$this->_start = ($this->_limit != 0 ? (floor($start / $this->_limit) * $this->_limit) : 0);
		
		$this->setState('list.limit', $this->_limit);
		$this->setState('list.start', $this->_start);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		
		// Initialise variables.
		$app = JFactory::getApplication();

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_profiles');
		$this->setState('params', $params);
		
		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		/*if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}*/

		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, $this->_start, $this->_limit);

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   11.1
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		/*if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}*/

		// Create the pagination object.
		jimport('joomla.html.pagination');
		$page = new ProfilePagination($this->getTotal(), $this->_start, $this->_limit);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from('`#__profiles_successes` AS a');
        $query->where('a.state = 1');
        $query->order("ordering ASC");


		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('( a.success_story LIKE '.$search.' )');
			}
		}

		return $query;
	}

}