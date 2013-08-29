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
class ProfilesViewSuccess extends JViewLegacy
{
	protected $state;
	protected $item;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		
		$this->assignRef('families', $items);
		$this->assignRef('pagination', $pagination);
		
        parent::display($tpl);
	}
}