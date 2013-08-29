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
class ProfilesViewContact extends JViewLegacy
{
	protected $state;
	protected $item;

	function display($tpl = null)
	{
		if(!JRequest::getVar('request_sent'))
		{
			$app		= JFactory::getApplication();
			$params		= $app->getParams();

			// Get some data from the models
			$state		= $this->get('State');
			$item		= $this->get('Item');
		
			$this->family = $item;
		}
		else
		{
			$sent = JRequest::getVar('request_sent');
			$this->request_sent = $sent;
		}
        parent::display($tpl);

	}
}