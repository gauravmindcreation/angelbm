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
 * View to edit
 */
class ProfilesViewPhoto extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $allPhotos;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}
	
	public function getPhotos()
	{
		$id = $this->item->id;
		if (!$id) {
			$id = JRequest::getInt('uid');
		}
		if (!$this->allPhotos) {
			$this->getFamilyPhotos($id);
		}
		return $this->allPhotos;
	}
	
	protected function getFamilyPhotos($id)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__profiles_photos AS a');
		$query->leftJoin('#__profiles_families AS b ON a.family_id = b.id');
		$query->where('b.id = '. (int) $id);
		$query->order('ordering ASC');
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}
		$this->allPhotos = $db->loadObjectList();
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
		$canDo		= ProfilesHelper::getActions();

		JToolBarHelper::title(JText::_('COM_PROFILES_TITLE_PHOTOS'), 'mediamanager.png');
		
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('photo.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		JToolBarHelper::cancel('family.cancel', 'JTOOLBAR_CLOSE');
	}
}
