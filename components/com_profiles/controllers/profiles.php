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

jimport('joomla.application.controller');

class ProfilesControllerProfiles extends JController
{
	public static function addFavorite()
	{
		$app = JFactory::getApplication();
		$sess = JFactory::getSession();
		$id = JRequest::getInt('id');
		$favs = (array) $sess->get('favorites', array(), 'profiles');

		$response = array(
			'type' => 'fail',
			'html' => null
		);

		if (!isset($favs[$id]))
		{
			$favs[$id]	= (object) array('id' => $id);

			$sess->set('favorites', $favs, 'profiles');

			$response['type'] = 'success';
			$response['html'] = self::buildHtml($id);
		}

		$app->close(json_encode($response));
	}
	
	public static function delFavorite()
	{
		$app = JFactory::getApplication();
		$sess = JFactory::getSession();
		$id = JRequest::getInt('id');
		$favs = (array) $sess->get('favorites', array(), 'profiles');

		$response = array(
			'type' => 'success',
			'id' => $id
		);

		if(isset($favs[$id])) unset($favs[$id]);

		$sess->set('favorites', $favs, 'profiles');

		$app->close(json_encode($response));
	}

	public static function buildHtml($id = null)
	{
		if ($id === null)
		{
			$id	= JRequest::getInt('id');
		}

		$db = JFactory::getDbo();
		$q = $db->getQuery(true)
			->select('a.id, a.first_name, a.spouse_name')
			->from('#__profiles_families AS a')
			->where('a.id = ' . $id);

		$item = $db->setQuery($q)->loadObject();
		$html = '';

		if (!empty($item))
		{
			$name = $item->first_name;

			if ($item->spouse_name)
			{
				$name .= ' and ' . $item->spouse_name;
			}

			$link = JRoute::_('index.php?option=com_profiles&view=profile&id=' . $item->id . '&Itemid=138');

			$html .= '<div class="favorite">';
			$html .= "	<a href=\"{$link}\">";
			$html .= "		<span class=\"fav_title\">{$name}</span>";
			$html .= '	</a>';
			$html .= "	<a class=\"rm_fav\" href=\"javascript:void(0);\" id=\"remove-{$item->id}\">delete</a>";
			$html .= '</div>';
		}

		return $html;
	}

}
