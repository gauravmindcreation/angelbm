<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Families list controller class.
 */
class ProfilesControllerFamilies extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since 1.6
	 */
	public function &getModel($name = 'family', $prefix = 'ProfilesModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function randomize()
	{
		$model = $this->getModel('family');
		$count = $model->getCount();

		$order = array();
		$times = 0;
		foreach ($model->getFamilyIDs() as $item) {
			$rand = mt_rand(1, $count);
			while (in_array($rand, $order)) {
				$rand = mt_rand(1, $count);
			}

			$order[$item->id] = $rand;
		}
		$pks = array_keys($order);
		$new_order = array_values($order);
		$status = $model->saveOrder($pks, $new_order);

		if ($status->getError()) {
			$msg = JText::_('Error: One or more records could not be updated.');
			$type = 'error';
		}
		else {
			$msg = JText::_('Success: Family Ordering Updated');
			$type = 'message';
		}

		$this->setRedirect('index.php?option=com_profiles&view=families', $msg, $type);
	}

	public function import()
	{
		$model = $this->getModel();

		if ($i = $model->importFamilies()) {
			$msg = "{$i} Families Imported";
			$type = 'message';
		} else {
			$msg = 'Error importing families.';
			$type = 'error';
		}
		
		$this->setRedirect('index.php?option=com_profiles&view=families', $msg, $type);
	}
	
	public function fixFields()
	{
		$model = $this->getModel();
		
		if ($model->fixFields()) {
			$msg = 'Fields have been repaired.';
			$type = 'message';
		} else {
			$msg = 'You fucked up.';
			$type = 'error';
		}
		
		$this->setRedirect('index.php?option=com_profiles&view=families', $msg, $type);
	}
	
	public function resetHits()
	{
		$this->getModel()->resetHits();
		
		$this->setRedirect('index.php?option=com_profiles&view=families', 'Profile View Stats Reset');
	}
}
