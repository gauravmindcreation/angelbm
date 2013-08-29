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
class ProfilesControllerSuccess extends JControllerForm
{
	public $sizes = array(
		199 => 142,
		150 => 150,
		64 => 64,
	);

    function __construct() {
        $this->view_list = 'successes';
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
    		$dir = '/uploads/successes/'.$entry->id.'/';
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
    
    public function postSaveHook($model, $validData)
    {
    	//var_dump($validData); die();
    	if(!empty($_FILES['jform']))
    	{
	    	$id = $validData['id'];
    		
    		$dir = '/uploads/successes/'.$id.'/';
    		$full_dir = JPATH_SITE.$dir;
    	
    		$fields = array('success_story_image');
    		foreach($fields as $field)
    		{
    			$file = new stdClass;
    			foreach($_FILES['jform'] as $key => $values)
    			{
	    			$file->$key		= $values[$field];
    			}
    			
    			if(!$file->error)
    			{
	    			$parts = explode('.', $file->name);
	    			$file->ext = strtolower(array_pop($parts));
	    			$allowed_ext = explode(',', 'jpg,jpeg,png,gif');
	    			if(in_array($file->ext, $allowed_ext))
	    			{
	    				$file->ok = true;
	    			}
			    	
			    	$file->name = JFile::makeSafe(strtolower($file->name));
			    	
	    			$file->tmp_info = getimagesize($file->tmp_name);
	    			if(is_int($file->tmp_info[0]) && is_int($file->tmp_info[1]) || preg_match("/image/i", $file->tmp_info['mime']))
	    			{
		    			$file->name = preg_replace('[^a-z0-9.]', '', strtolower($file->name));
		    			if(!is_dir($full_dir))
		    			{
			    			JFolder::create($full_dir, 0755);
		    			}
		    			if(is_file($full_dir.$file->name))
		    			{
			    			JFile::delete($full_dir.$file->name);
		    			}
		    			if(JFile::upload($file->tmp_name, $full_dir.$file->name))
		    			{
		    				$image = Image::load($full_dir.$file->name);
							// Check if it's wider than 600 pixels
							if($file->tmp_info[0] > 600)
							{
								$image->resize(600)->save_pa('tmp_');
								JFile::delete($full_dir.$file->name);
								JFile::move('tmp_'.$file->name, $file->name, $full_dir);
								$image = Image::load($full_dir.$file->name);
							}
							foreach($this->sizes as $w => $h)
							{
								$image->crop_resize($w, $h)->save_pa("{$w}_{$h}_");
							}
		    				$_POST['jform'][$field] = $file->name;
		    			}
		    		}
	    		}
    		}
    	}
    	parent::postSaveHook($model, $validData);
    }
}