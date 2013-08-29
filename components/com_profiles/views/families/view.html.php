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

/**
 * HTML View class for the Profiles component
 */
class ProfilesViewFamilies extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $gallery;

	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Get some data from the models
		$this->state = $this->get('State');
		$this->families = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->error = $this->getModel()->getError();
		
		if(count($this->families) === 0 && JRequest::getVar('start'))
		{
			// $app->redirect(JRoute::_('index.php?option=com_profiles'));
		}

        parent::display($tpl);
	}
	
	public function getGalleryByFamilyID($id = null)
	{
		//!is_null($id) or die();
		if($id === null)
		{
			return;
		}
		
		$cache = JFactory::getCache('com_profiles', '');

		$gallery =  $cache->get($id . '_gallery');
		
		if ($gallery === false)
		{
			$db = JFactory::getDbo();
			$q = $db->getQuery(true)
				->select('a.*')
				->from('#__profiles_photos AS a')
				->leftJoin('#__profiles_families AS b ON a.family_id = b.id')
				->where('b.id = '. (int) $id)
				->order('ordering ASC');

			$db->setQuery($q);

			if (!$db->query())
			{
				JError::raiseError(500, $db->getErrorMsg());
			}

			$gallery = $db->loadObject();
		}

		return $gallery;
	}
}
