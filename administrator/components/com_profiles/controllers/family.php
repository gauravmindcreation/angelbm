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

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Family controller class.
 */

// TODO: Make use of http://docs.joomla.org/Secure_coding_guidelines#File_uploads
class ProfilesControllerFamily extends JControllerForm
{
	public $sizes = array(
		313 => 265,
		125 => 100,
		199 => 142,
		150 => 150,
		64 => 64,
	);

	function __construct()
	{
		$this->view_list = 'families';
		parent::__construct();
	}

	// photo ajax delete function
	public function deletephoto()
	{
		$app = JFactory::getApplication();
		$pk  = JRequest::getInt('id');
		$field = JRequest::getString('field');

		$model = $this->getModel();
		$entry = $model->getItem($pk);

		$newdata = array(
			'id' => $entry->id,
			$field => ''
		);

		if ($model->save($newdata)) {
			$dir = '/uploads/profiles/'.$entry->id.'/';
			$full_dir = JPATH_SITE.$dir;
			$name = $entry->$field;

			$to_delete = array($full_dir.$name);
			foreach ($this->sizes as $w => $h) {
				$to_delete[] = $full_dir.$w.'_'.$h.'_'.$name;
			}

			JFile::delete($to_delete);
			$result = 'success';
		} else {
			$result = 'fail';
		}

		echo json_encode(array('result' => $result));
		$app->close();
	}

	public function cancel()
	{
		$key = null;
		if (JRequest::getInt('uid')) {
			$key = 'uid';
		}
		parent::cancel($key);
	}

	public function save($key = null, $urlVar = null)
	{
		ini_set('memory_limit', '256M');
		$model = $this->getModel();
		$data  = JRequest::getVar('jform', array(), 'post', 'array');

		$this->dir = '/uploads/profiles/'.$data['id'].'/';
		$this->full_dir = JPATH_SITE.$this->dir;

		if (!is_dir($this->full_dir)) {
			JFolder::create($this->full_dir, 0755);
		}

		if (!empty($_FILES['jform'])) {
			$id = $data['id'];
			$od = $model->getState()->get('family.id');
			if ($od) {
				$id = $od;
			}
			ProfilesHelper::saveImages($this->full_dir, $_FILES['jform'], $data);
		}
		$new_id = $data['user_id'];
		$old_id = JRequest::getVar('old_id');
		if ($new_id != $old_id) {
			$db = $this->getModel()->getDbo();
			$db->setQuery('UPDATE #__profiles_photos SET family_id = '.(int) $validData['user_id'].' WHERE family_id = '.(int) $old_id);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}

		JRequest::setVar('jform', $data, 'post', true);

		parent::save($key, $urlVar);
	}
}
