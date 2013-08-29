<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

jimport('joomla.image.image');

abstract class ProfilesHelper
{
	public static $single = true;
	public static $family = null;
	public static $database = null;

	public static function getDbo()
	{
		if (self::$database === null)
		{
			$conf = JComponentHelper::getParams('com_profiles');

			$options = array(
				'driver' => $conf->get('dbtype'),
				'host' => $conf->get('host'),
				'user' => $conf->get('user'),
				'password' => $conf->get('password'),
				'database' => $conf->get('db'),
				'prefix' => $conf->get('dbprefix')
			);

			try
			{
				self::$database = JDatabaseDriver::getInstance($options);
			}
			catch (RuntimeException $e)
			{
				if (!headers_sent())
				{
					header('HTTP/1.1 500 Internal Server Error');
				}

				jexit('Database Error: ' . $e->getMessage());
			}
		}

		return self::$database;
	}
	
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{

		JSubMenuHelper::addEntry(
			JText::_('COM_PROFILES_TITLE_FAMILIES'),
			'index.php?option=com_profiles&view=families',
			$vName == 'families'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_PROFILES_TITLE_RECENTS'),
			'index.php?option=com_profiles&view=recents',
			$vName == 'recents'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_PROFILES_TITLE_SUCCESSES'),
			'index.php?option=com_profiles&view=successes',
			$vName == 'successes'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_profiles';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public static function getUsernameFromId($id)
	{
		$db		= self::getDbo();
		$query	= $db->getQuery(true)->select('a.username')->from('#__profiles_families as a')->where('a.id = '. (int) $id);
		
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$result = $db->loadObject();
		
		return $result->username;
	}
	
	public static function getIdFromUsername($username)
	{
		$username = str_replace(':', '-', $username);
		
		$db		= self::getDbo();
		$query	= $db->getQuery(true)->select('a.id')->from('#__profiles_families as a')->where("a.username = '{$username}'");
		
		$db->setQuery((string)$query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$result = $db->loadObject();

		return $result->id;
	}
	
	public static function getImageSizes()
	{
		return array(
			'resize' => array(
				array(300, null),
				array(150, null),
			),
			'crop' => array(
				array(310, 220, 270, 200),
				array(125, 100, 270, 200),
				array(64, 64, 270, 200),
			),
		);
	}
	
	public static function resizeImage($full_dir, $name, $info = null)
	{
		ini_set('memory_limit', '256M');
		set_time_limit(300);
		
		if ($info === null) {
			$info = JImage::getImageFileProperties($full_dir.$name);
		}
		
		$image = new JImage;
		$image->loadFile($full_dir.$name);
		// Check if it's wider than 600 pixels
		if ($info->width > 600) {
			$image->resize(600, null)->toFile($full_dir.$name);
			$image = new JImage;
			$image->loadFile($full_dir.$name);
		}
		foreach (self::getImageSizes() as $type => $sizes) {
			if (empty($sizes)) continue;
			foreach ($sizes as $size) {
				if (empty($size)) continue;
				list($width, $height, $left, $top) = $size;
				if ($type = 'resize') {
					// fix the resize method arguments.
					// $left is actually $createNew and $top is scale method
					// check libraries/joomla/image/image.php
					$left = true;
					$top = 2;
				}
				$prefix = str_replace('__', '_', "{$width}_{$height}_");
				if (!JFile::exists($full_dir.$prefix.$name)) {
					$image->$type($width, $height, $left, $top)->toFile($full_dir.$prefix.$name);				
				}
			}
		}
		unset($image);
	}
	
	public static function saveImages(&$full_dir, &$files, &$data)
	{
		foreach($files['name'] as $field => $val) {
			if (empty($val)) {
				continue;
			}
			$file = new stdClass;
			foreach ($files as $key => $values) {
				$file->$key = $values[$field];
			}
			if (!$file->error) {
				$parts = explode('.', $file->name);
				$file->ext = strtolower(array_pop($parts));
				$allowed_ext = explode(',', 'jpg,jpeg,png,gif,pdf');
				if (in_array($file->ext, $allowed_ext)) {
					$file->ok = true;
				}
				
				$file->name = JFile::makeSafe(strtolower($file->name));
				
				if ($field === 'pdf') {
					if ($file->ok == true) {
						JFile::upload($file->tmp_name, $full_dir.$file->name);
						$data[$field] = $file->name;
					}
				} else {
					$file->tmp_info = JImage::getImageFileProperties($file->tmp_name);
					if (is_int($file->tmp_info->width) && is_int($file->tmp_info->height) || preg_match("/image/i", $file->tmp_info->mime)) {
						if (is_file($full_dir.$file->name)) {
							JFile::delete($full_dir.$file->name);
						}
						if (JFile::upload($file->tmp_name, $full_dir.$file->name)) {
							self::resizeImage($full_dir, $file->name, $file->tmp_info);
							$data[$field] = $file->name;
						}
					}
				}
			}
		}
	}

	public static function familyImage($path = null, $id = null, $size = '150_150_', $lightbox = true)
	{
		$img = '/uploads/profiles/' . $id . '/'.$size.$path;

		if (is_file(JPATH_SITE.$img))
		{
			if ($lightbox)
			{
				echo '<a href="/uploads/profiles/' . $id . '/' . $path . '" class="lightbox"><span></span>';
			}
			$info = getimagesize(JPATH_SITE.$img);
			echo '<img src="'.$img.'" alt="" />';

			if ($lightbox)
			{
				echo '</a>';
			}
		}
	}

	public static function iOrWe()
	{
		return self::$single ? 'I' : 'We';
	}

	public static function meOrUs()
	{
		return self::$single ? 'Me' : 'Us';
	}

	public static function myOrOur()
	{
		return self::$single ? 'My' : 'Our';
	}

	public static function detectUrl($text)
	{
		return preg_replace("#((http|https)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'To learn more about us, <a href=\"$1\" target=\"_blank\">click here</a>.$4'", $text);
	}

	public static function formatProfileText($text)
	{
		return stripslashes(nl2br(self::detectUrl($text)));
	}
	
	public static function favorites($person = 'my')
	{
		$fields = array(
			'occupation',
			'religion',
			'education',
			'food',
			'hobby',
			'movie',
			'sport',
			'holiday',
			'music_group',
			'tv_show',
			'book',
			'subject_in_school',
			'childhood_toy',
			'childhood_memory',
			'tradition',
			'family_activity',
			'memory_w_spouse',
			'vacation_spot',
			'animal'
		);
		
		$html .= '<ul>';
		
		foreach ($fields as $field)
		{
			$label = JText::_("COM_PROFILES_{$field}");
			$attr = "{$person}_{$field}";
			$content = self::$family->$attr;
			if ($content)
			{
				$content = htmlspecialchars(htmlspecialchars_decode($content));
				$html .= "<li><span>{$label}</span><span class=\"answer\">{$content}</span><span class=\"clear\" style=\"float:none;display:block;height:1px;width:100%\"></span></li>";
			}
			
		}
		$html .= '</ul>';
		
		return $html;
	}
}
