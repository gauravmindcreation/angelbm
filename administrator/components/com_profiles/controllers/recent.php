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
class ProfilesControllerRecent extends JControllerForm
{
    public function __construct() {
        $this->view_list = 'recents';
        parent::__construct();
        
        if(!empty($_FILES['jform']))
        {
        	foreach($_FILES['jform']['name'] as $field => $filename)
        	{
        		if(!empty($filename))
        		{
        			$_POST['jform'][$field] = JFile::makeSafe(strtolower($filename));
        		}
        	}
        } 
    }
    
    // photo ajax delete function
	public function deletephoto()
	{
		$app	= JFactory::getApplication();
		$pk		= JRequest::getInt('id');
		$field	= JRequest::getString('field');
		
		$model	= $this->getModel();
		$entry	= $model->getItem($pk);
		
		$newdata = array(
			'id'	=> $entry->id,
			$field	=> ''
		);
		
		if($model->save($newdata))
		{
    		$dir = '/uploads/recents/'.$entry->id.'/';
    		$full_dir = JPATH_SITE.$dir;    		
    		$name = $entry->$field;
    		
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
    
    public function save($key = null, $urlVar = null)
    {
    	ini_set('memory_limit', '256M');
		$data  = JRequest::getVar('jform', array(), 'post', 'array');
		
    	if (!empty($_FILES['jform'])) {
    		if ($data['id'] === '0') {
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true);
				$query->select('a.id');
				$query->from('#__profiles_recents AS a');
				$query->order('a.id DESC');

				$db->setQuery($query);
				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
				$result = $db->loadObject();
				$data['id'] = (int)$result->id;
				$data['id']++;
			}
    		
    		$this->dir = '/uploads/recents/'.$data['id'].'/';
    		$this->full_dir = JPATH_SITE.$this->dir;
		
			if (!is_dir($this->full_dir)) {
				JFolder::create($this->full_dir, 0755);
			}
			
	    	ProfilesHelper::saveImages(&$this->full_dir, &$_FILES['jform'], &$data);
	    }
    	
    	JRequest::setVar('jform', $data, 'post', true);
    	
    	parent::save($key, $urlVar);
    }
}