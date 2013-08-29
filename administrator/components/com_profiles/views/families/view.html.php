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
 * View class for a list of Profiles.
 */
class ProfilesViewFamilies extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'profiles.php';

		$state	= $this->get('State');
		$canDo	= ProfilesHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_PROFILES_TITLE_FAMILIES'), 'user.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'family';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('family.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit')) {
			    JToolBarHelper::editList('family.edit','JTOOLBAR_EDIT');
		    }

        }

	    JToolBarHelper::divider();
	    JToolBarHelper::custom('families.randomize', 'refresh.png', 'refresh_f2.png','Randomize', false);
	    JToolBarHelper::custom('families.import', 'download.png', 'download_f2.png','Import', false);
	    
		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('families.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('families.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'families.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('families.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('families.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
	    JToolBarHelper::divider();
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'families.delete','JTOOLBAR_EMPTY_TRASH');
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('families.trash','JTOOLBAR_TRASH');
		    }
        }
        
	    JToolBarHelper::divider();
	    JToolBarHelper::custom('families.resetHits', 'remove.png', 'remove.png','Reset Views', false);

		if ($canDo->get('core.admin')) {
	    	JToolBarHelper::divider();
			JToolBarHelper::preferences('com_profiles');
		}


	}
}
