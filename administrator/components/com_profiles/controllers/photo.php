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

/**
 * Photo controller class.
 */
class ProfilesControllerPhoto extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'photos';
        parent::__construct();
    }
    
    // for the ajax delete function in the gallery manager
	public function delete()
	{
		$app	= JFactory::getApplication();
		$pk		= JRequest::getInt('id');
		$model	= $this->getModel();
		$db		= $model->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('*')
			->from('#__profiles_photos AS a')
			->where('a.id = '.$pk);
			
		$db->setQuery((string)$query);
		$obj	= $db->loadObject();
		
		if($model->delete($pk))
		{
    		$dir = '/uploads/profiles/'.$obj->id.'/';
    		$full_dir = JPATH_SITE.$dir;
    		$name = $obj->path;
    		
    		$to_delete = array($full_dir.$name);
    		foreach($this->sizes as $w => $h)
    		{
    			$to_delete[] = $full_dir.$w.'_'.$h.'_'.$name;
    		}
    		
			JFile::delete($to_delete);
			$result = 'success';
		}
		else
		{
			$result = 'fail';
		}
		
		echo json_encode(array('result' => $result));
		$app->close();
    }
	
	// Handles the ajax reordering of the photos
	public function reorderphotos()
	{
		$app	= JFactory::getApplication();
		$model = $this->getModel('photo');
		
		$order = explode(',', JRequest::getVar('new_order'));
		$pks = array_values($order);
		$new_order = array_keys($order);
		$status = $model->saveOrder($pks, $new_order);
			
		if($status->getError())
		{
			$result = 'fail';
		}
		else
		{
			$result = 'success';
		}
		 
		echo json_encode(array('result' => $result));
		$app->close();
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   11.1
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl	= JRequest::getCmd('tmpl');
		$layout	= JRequest::getCmd('layout', 'edit');
		$uid	= JRequest::getInt('uid');
		$append = '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout) {
			$append .= '&layout=' . $layout;
		}

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		if ($uid) {
			$append .= '&uid=' . $uid;
		}

		return $append;
	}
	
    
    public function save($key = null, $urlVar = null)
    {
    	ini_set('memory_limit', '256M');
		$data  = JRequest::getVar('jform', array(), 'post', 'array');
		
    	if (!empty($_FILES['jform'])) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$query->select('a.id');
			$query->from('#__profiles_families as a');
			$query->where('a.id = '.(int) $data['family_id']);

			$db->setQuery((string)$query);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
			$result = $db->loadObject();
    		
    		$this->dir = '/uploads/profiles/'.$result->id.'/';
    		$this->full_dir = JPATH_SITE.$this->dir;
		
			if (!is_dir($this->full_dir)) {
				JFolder::create($this->full_dir, 0755);
			}
			
	    	ProfilesHelper::saveImages($this->full_dir, $_FILES['jform'], $data);
	    }
    	
    	JRequest::setVar('jform', $data, 'post', true);
    	
    	parent::save($key, $urlVar);
    }
}
