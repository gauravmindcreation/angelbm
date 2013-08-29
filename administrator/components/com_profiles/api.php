<?php
/*
* @package		Profile Manager
* @copyright	2012 Electric Easel, Inc. www.electriceasel.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

class ProfilesAPI {

	private static $instances;
	
	private $model;

	public function getInstance($identifier = 1)
	{
		if(!(self::$instances[$identifier] instanceof ProfilesAPI))
		{
			self::$instances[$identifier] = new ProfilesAPI;
		}
		return self::$instances[$identifier];
	}
	
	public function __construct()
	{
		require_once(JPATH_ADMINISTRATOR.'/components/com_profiles/models/family.php');
		require_once(JPATH_ADMINISTRATOR.'/components/com_profiles/tables/family.php');
		$this->model = new ProfilesModelfamily;
	}
	
	public function createProfileFromJ($user)
	{
		$names = array();
		if(strpos($user['name'], ' & ') !== false)
		{
			$names = explode(' & ', $user['name']);
		}
		else
		{
			$names[0] = $user['name'];
			$names[1] = '';
		}
		$data = array(
			'user_id' => $user['id'],
			'first_name' => $names[0],
			'spouse_name' => $names[1],
			'state' => 0
		);
		return $this->model->save($data);
	}
	
	public function deleteProfileFromJ($user)
	{
		return;
		//TODO: trash the profile when the user is deleted set `state` to -2
		/*
		$data = array(
			'user_id' => $user['id'],
			'first_name' => $user['name'],
		);
		return $this->model->save($data);
		*/
	}
	
}