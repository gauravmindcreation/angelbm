<?php
/**
 * @package 	Cloud Panel Component for Joomla!
 * @author 		CloudAccess.net LCC
 * @copyright 	(C) 2010 - CloudAccess.net LCC
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

//!no direct access
defined ('_JEXEC') or die ('Restricted access');

jimport( 'joomla.application.component.controller' );

if (JVERSION >= '3.0')
{
	class_alias('JModelLegacy', 'Model');
	class_alias('JControllerLegacy', 'Controller');
}
else
{
	class_alias('JModel', 'Model');
	class_alias('JController', 'Controller');
}

class updateController extends Controller  
{
	public function vjce()
	{

		jimport('joomla.application.component.helper');
		jimport('legacy.component.helper');
		$jce = JComponentHelper::getComponent('com_jce');
		
		if ($jce->id == 0) {
			$response = array('stats' => 'failure', 'code' => 6, 'message' => 'JCE not found');
		}
		else {
			$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_jce/jce.xml');
			$jce_version = (string)$xml->version;
			$response = array('stats' => 'ok', 'version' => $jce_version);
		}

		$this->_showVersion($response);
	}

	public function vcms()
	{
		$this->_showVersion(array('stats' => 'ok', 'version' => JVERSION));
	}

	private function _showVersion($response)
	{
		$callback = JRequest::getVar('callback');

		$html = null;
		if ($callback)
		{
			$html = $callback.'(';
		}
		$html .= json_encode($response);
		if ($callback)
		{
			$html .= ')';
		}
		
		echo $html;
		JFactory::getApplication()->close();
	}

	public function jce()
	{
		$callback = JRequest::getVar('callback');
		
		jimport('joomla.application.component.helper');
		jimport('legacy.component.helper');
		$jce = JComponentHelper::getComponent('com_jce');
		
		if ($jce->id == 0) {
			$response = array('stats' => 'failure', 'code' => 6, 'message' => 'JCE not found');
		}
		else {
			//check if exists site updater
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->select('update_site_id');
			$query->from('#__update_sites_extensions');
			$query->where("extension_id={$jce->id}");
			
			$db->setQuery($query);
			$update_site_id = $db->loadResult();
			if (empty($update_site_id)) {
				//insert update site id for JCE
				$db->setQuery("INSERT INTO #__update_sites VALUES('0','JCE Editor Updates','extension','https://www.joomlacontenteditor.net/index.php?option=com_updates&view=update&format=xml&id=1','1','0')");
				$db->query();
				// update site extension
				$db->setQuery("INSERT INTO #__update_sites_extensions VALUES(LAST_INSERT_ID(),'{$jce->id}')");
				$db->query();
			}
			
			//set administration scope
			JFactory::$application = null;
			JFactory::getApplication('administrator');
			Model::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_installer/models/');
			
			$model = Model::getInstance('update','updateModel');
			
			$model->purge();
			$result = $model->findUpdates($jce->id, 3600);
			$model->setState('filter.extension_id', $eid);
			$updates = $model->getItems();
			
			if (empty($updates)) {
				$response = array('stats' => 'ok', 'code' => 4, 'message' => 'JCE is up-to-date');
			} else {
				$uid = intval($updates[0]->update_id);
				
				$model->update(array($uid));
				$updateResult = $model->getState('result');
				if ($updateResult) {
					$response = array('stats' => 'ok', 'code' => 100, 'message' => 'JCE update success');
				} else {
					$response = array('stats' => 'ok', 'code' => 8, 'message' => 'JCE update failed - error when try to update jce');	
				}
			}
		}
		
		$html = null;
		if ($callback)
		{
			$html = $callback.'(';
		}
		$html .= json_encode($response);
		if ($callback)
		{
			$html .= ')';
		}
		
		echo $html;
		JFactory::getApplication()->close();
	}
	
	public function start()
	{
		$this->_applyCredentials();
		
		$callback = JRequest::getVar('callback');

		$eid = 700;
		
		Model::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_installer/models/');
		$model = Model::getInstance('update','InstallerModel');
		Model::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_joomlaupdate/models/');
		$modelJoomlaUpdate = Model::getInstance('default','joomlaupdateModel');
		
		$db = JFactory::getDBO();
		// Note: TRUNCATE is a DDL operation
		// This may or may not mean depending on your database
		$db->setQuery('TRUNCATE TABLE #__updates');
		if ($db->Query()) {
			// Reset the last update check timestamp
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__update_sites'));
			$query->set($db->quoteName('last_check_timestamp').' = '.$db->q(0));
			$db->setQuery($query);
			$db->query();
		}
		$db->setQuery('UPDATE #__update_sites SET enabled = 1 WHERE enabled = 0');
		$db->query();
		
		if (method_exists($modelJoomlaUpdate,'purge'))
		{
			$modelJoomlaUpdate->purge();
		}
		if (method_exists($modelJoomlaUpdate,'getUpdateInformation'))
		{
			$modelJoomlaUpdate->getUpdateInformation();
		}
		
		$model->purge();
		$model->enableSites();
		$model->findUpdates($eid, 0);
		$result = $model->findUpdates($eid, 3600);
		$model->setState('filter.extension_id', $eid);
		$updates = $model->getItems();
		
		if (empty($updates))
		{
			$response = array('stats' => 'ok', 'code' => 4, 'message' => 'Joomla! is up-to-date');
			
			$html = null;
		
			if ($callback)
			{
				$html = $callback.'(';
			}
			$html .= json_encode($response);
			if ($callback)
			{
				$html .= ')';
			}
			
			echo $html;
			JFactory::getApplication()->close();
		}
		if ($updates[0]->version == JVERSION)
		{
			$response = array('stats' => 'ok', 'code' => 4, 'message' => 'Joomla! is up-to-date');
			
			$html = null;
		
			if ($callback)
			{
				$html = $callback.'(';
			}
			$html .= json_encode($response);
			if ($callback)
			{
				$html .= ')';
			}
			
			echo $html;
			JFactory::getApplication()->close();
		}
		
		$modelDefault = $this->getModel('joomlaupdate');
		
		$file = $modelDefault->download();
		
		$url = 'caupdate=1&task=install&format=json';
		
		if ($file === false)
		{
			$response = array('stats' => 'failure', 'code' => 5, 'message' => 'tmp folder dont have sufficient permissions');
			
			$html = null;
		
			if ($callback)
			{
				$html = $callback.'(';
			}
			$html .= json_encode($response);
			if ($callback)
			{
				$html .= ')';
			}
			
			echo $html;
			JFactory::getApplication()->close();
		}
		
		$html = null;
		$response = array(
			'stats' => 'ok',
			'ajax' => array(
				'options' => array(
					'data' => $url
				)
			),
			'message' => 'installing update'
		);
		if ($callback)
		{
			$html = $callback.'(';
		}
		$html .= json_encode($response);
		if ($callback)
		{
			$html .= ')';
		}
		
		echo $html;
		JFactory::getApplication()->close();
	}
	
	/**
	 * Start the installation of the new Joomla! version 
	 * 
	 * @return void
	 */
	public function install()
	{
		$this->_applyCredentials();

		$modelDefault = $this->getModel('joomlaupdate');
		
		$file = JFactory::getApplication()->getUserState('com_joomlaupdate.file', null);
		$restorationFile = $modelDefault->createRestorationFile($file);
		
		if ($restorationFile === false)
		{
			if (!is_writable(JPATH_ADMINSITRATOR.'/components/com_joomlaupdate'))
			{
				$response = array('stats' => 'failure', 'code' => 6, 'message' => 'cant write joomla restoration file');
				
				$html = null;
		
				if ($callback)
				{
					$html = $callback.'(';
				}
				$html .= json_encode($response);
				if ($callback)
				{
					$html .= ')';
				}
				
				echo $html;
				JFactory::getApplication()->close();
			}
		}
		
		$password = JFactory::getApplication()->getUserState('com_joomlaupdate.password', null);
		$filesize = JFactory::getApplication()->getUserState('com_joomlaupdate.filesize', null);
		
		$ajaxUrl = JURI::base();
		$ajaxUrl .= 'plugins/system/cloudpanel/update/restore.php';
		
		$returnUrl = JURI::base();
				
		$prefixPath = '';
		if (JFactory::getApplication()->isAdmin())
		{
			$prefixPath = '../';
		}
		
		$response = array(
			'stats' => 'ok',
			'vars' => array(
				"joomlaupdate_password = '$password'",
				"joomlaupdate_totalsize = '$filesize'",
				"joomlaupdate_ajax_url = '$ajaxUrl'",
				"joomlaupdate_return_url = '$returnUrl'",
			),
			'cbfunc' => 'pingUpdate()',
			'message' => 'starting update'
		);
		
		$callback = JRequest::getVar('callback');
		$html = null;
		if ($callback)
		{
			$html = $callback.'(';
		}
		$html .= json_encode($response);
		if ($callback)
		{
			$html .= ')';
		}
		
		echo $html;
		JFactory::getApplication()->close();
	}
	
	/**
	 * Finalise the upgrade by running the necessary scripts
	 * 
	 * @return void
	 */
	public function finalise()
	{
		$this->_applyCredentials();

		$modelDefault = $this->getModel('joomlaupdate');
		$modelDefault->finaliseUpgrade();

		$url = 'caupdate=1&task=cleanup&format=json';
		$response = array(
			'stats' => 'ok', 
			'ajax' => array(
				'options' => array(
					'data' => $url
				)
			),
			'message' => 'cleaning install'
		);
		$callback = JRequest::getVar('callback');
		$html = null;
		if ($callback)
		{
			$html = $callback.'(';
		}
		$html .= json_encode($response);
		if ($callback)
		{
			$html .= ')';
		}
		
		echo $html;
		JFactory::getApplication()->close();
	}
	
	/**
	 * Clean up after ourselves
	 * 
	 * @return void
	 *
	 * @since 2.5.4
	 */
	public function cleanup()
	{
		$this->_applyCredentials();

		$modelDefault = $this->getModel('joomlaupdate');
		$modelDefault->cleanUp();

		$response = array('stats' => 'ok', 'code' => 100, 'message' => 'update success');
		$callback = JRequest::getVar('callback');
		$html = null;
		if ($callback)
		{
			$html = $callback.'(';
		}
		$html .= json_encode($response);
		if ($callback)
		{
			$html .= ')';
		}
		
		echo $html;
		JFactory::getApplication()->close();
	}
	
	/**
	 * Applies FTP credentials to Joomla! itself, when required
	 * 
	 * @return void
	 */
	protected function _applyCredentials()
	{
		jimport('joomla.client.helper');
		
		if (!JClientHelper::hasCredentials('ftp'))
		{
			$user = JFactory::getApplication()->getUserStateFromRequest('com_joomlaupdate.ftp_user', 'ftp_user', null, 'raw');
			$pass = JFactory::getApplication()->getUserStateFromRequest('com_joomlaupdate.ftp_pass', 'ftp_pass', null, 'raw');

			if ($user != '' && $pass != '')
			{
				// Add credentials to the session
				if (JClientHelper::setCredentials('ftp', $user, $pass))
				{
					$return = false;
				}
				else
				{
					$return = JError::raiseWarning('SOME_ERROR_CODE', JText::_('JLIB_CLIENT_ERROR_HELPER_SETCREDENTIALSFROMREQUEST_FAILED'));
				}
			}
		}
	}
}
