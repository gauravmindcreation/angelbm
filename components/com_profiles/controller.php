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

jimport('joomla.application.component.controller');

class ProfilesController extends JControllerLegacy
{
	public function send_contact()
	{
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_profiles');
		
		// Get data from POST
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		
		if(empty($data) || $data['sanity_check'] != $data['check'])
		{
			return false;
		}
		
		$model = $this->getModel('contact');
		
		$family = $model->getItem(JRequest::getInt('id'));
		
		$body = "Name: {$data['name']}" . PHP_EOL.PHP_EOL;
		$body .= "Email: {$data['email']}" . PHP_EOL.PHP_EOL;
		$body .= "Phone: {$data['phone']}" . PHP_EOL.PHP_EOL;
		$body .= "Race of Baby: {$data['baby_race']}" . PHP_EOL.PHP_EOL;
		$body .= "Due Date: {$data['due_date']}" . PHP_EOL.PHP_EOL;
		$body .= "Contacted Family: {$family->first_name} ";
		if($family->spouse_name)
		{
			$body .= '& '.$family->spouse_name;
		}
		$body .= PHP_EOL.PHP_EOL;
		$body .= "Message: {$data['message']}" . PHP_EOL.PHP_EOL;
		
		$mail = JFactory::getMailer();
		$mail->addRecipient('don@electriceasel.com');
		$mail->setSender($data['email']);
		$mail->setSubject('Family Contact Form: '.$data['name']);
		$mail->setBody($body);
		$sent = $mail->Send();

		if ($sent) {
			if (JRequest::getVar('mobile', null) === null) {
				$this->setRedirect(JRoute::_('index.php?option=com_profiles&tmpl=component&view=contact&request_sent=true&Itemid=138&id='.JRequest::getInt('id')));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_profiles&view=profile&request_sent=true&Itemid=138&id='.JRequest::getInt('id')));
			}
		}
	}
	
	public function upload()
	{
		$app = JFactory::getApplication();
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/image.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/image/driver.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/image/gd.php');
		
    	if(!empty($_FILES['ajaxUpload']))
    	{
	    	
    		jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
    		
    		$dir = '/uploads/apps/';
    		$full_dir = JPATH_SITE.$dir;

			$file = new stdClass;
			foreach($_FILES['ajaxUpload'] as $key => $value)
			{
    			$file->$key = $value;
			}
			
			if($file->error == 0)
			{
    			$parts = explode('.', $file->name);
    			$file->ext = strtolower(array_pop($parts));
    			$allowed_ext = explode(',', 'jpg,jpeg,png,gif');
    			if(in_array($file->ext, $allowed_ext))
    			{
    				$file->ok = true;
    			}
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
	    				$prepend = time() . '_';
	    				$image = Image::load($full_dir.$file->name);
	    				$image->resize(500)->save_pa($prepend);
	    				$image->crop_resize(200)->save_pa('thumb_'.$prepend);
	    				JFile::delete($full_dir.$file->name);
	    			}
					echo $this->encode_response('Success!', $dir.'thumb_'.$prepend.$file->name);
	    		}
	    		else
	    		{
	    			echo $this->encode_response('There was an unknown error uploading your file. 1');
    			}
    		}
    		else
    		{
    			echo $this->encode_response('There was an unknown error uploading your file. 2');
    		}
    	}
    	else
    	{
    		echo $this->encode_response('No file was uploaded.');
    	}
		
		$app->close();
	}
	
	private function encode_response($msg, $file = null)
	{
		$success = true;
		if($file === null)
		{
			$success = false;
		}
		return json_encode(array(
			'success'	=> $success,
			'msg'		=> $msg,
			'filepath'	=> $file,
		));
	}
}