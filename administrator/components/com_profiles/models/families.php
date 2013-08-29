<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Profiles records.
 */
class ProfilesModelfamilies extends JModelList
{
	protected $_count;
	protected $_family_ids;

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'user_id', 'a.user_id',
				'first_name', 'a.first_name',
				'spouse_name', 'a.spouse_name',
				'last_name', 'a.last_name',
				'video', 'a.video',
				'about_us', 'a.about_us',
				'about_us_image', 'a.about_us_image',
				'our_home', 'a.our_home',
				'our_home_image', 'a.our_home_image',
				'ext_family', 'a.ext_family',
				'ext_family_image', 'a.ext_family_image',
				'family_traditions', 'a.family_traditions',
				'family_traditions_image', 'a.family_traditions_image',
				'favorites', 'a.favorites',
				'dear_birthmother', 'a.dear_birthmother',
				'adopt_gender', 'a.adopt_gender',
				'adopt_race', 'a.adopt_race',
				'seo_title', 'a.seo_title',
				'seo_keywords', 'a.seo_keywords',
				'seo_description', 'a.seo_description',
				'view_count', 'view_count'
            );
        }

        parent::__construct($config);
    }


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		
		$profile_status = $app->getUserStateFromRequest($this->context.'.filter.filter_profile_status', 'filter_profile_status', '', 'string');
		$this->setState('filter.filter_profile_status', $profile_status);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_profiles');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__profiles_families` AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }
        
        // Filter by profile_status
        $filter_profile_status = $this->getState('filter.filter_profile_status');
        if (is_numeric($filter_profile_status)) {
	        $query->where('a.profile_status = '.(int) $filter_profile_status);
        }
                    

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('( a.first_name LIKE '.$search.'  OR  a.spouse_name LIKE '.$search.'  OR  a.last_name LIKE '.$search.'  OR  a.about_us LIKE '.$search.'  OR  a.our_home LIKE '.$search.'  OR  a.ext_family LIKE '.$search.'  OR  a.family_traditions LIKE '.$search.'  OR  a.dear_birthmother LIKE '.$search.'  OR  a.adopt_gender LIKE '.$search.'  OR  a.adopt_race LIKE '.$search.'  OR  a.seo_title LIKE '.$search.'  OR  a.seo_keywords LIKE '.$search.'  OR  a.seo_description LIKE '.$search.' )');
			}
		}
    
        $query->select('COUNT(b.id) AS view_count');
        $query->leftJoin('#__profiles_stats AS b ON a.id = b.family_id');
        $query->where('(b.time > DATE_SUB(NOW(), INTERVAL 30 DAY) OR b.time IS NULL)');
        $query->group('a.id');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
		    $query->order($db->getEscaped($orderCol.' '.$orderDirn));
        }

        //print_r($query->__toString());die;

		return $query;
	}
}
