<?php
/**
 * @package 	Cloud Panel Component for Joomla!
 * @author 		CloudAccess.net LCC
 * @copyright 	(C) 2010 - CloudAccess.net LCC
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

//!no direct access
defined ('_JEXEC') or die ('Restricted access');

register_shutdown_function('shutdownFunction');

//transform fatal error in json response for stripe
function shutDownFunction() { 
    $error = error_get_last();
    // Catch php errors: E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR and E_RECOVERABLE_ERROR
    // http://www.php.net/manual/en/errorfunc.constants.php
    if ($error['type'] == 1) {
    	$response['code'] = 66;
    	$response['stats'] = 'failure';

    	$response['message'] = 'Fatal Error: '.$error['message'].' at '.$error['file'].' line '.$error['line'];

    	$ok = (!empty($_GET['cacheckstate']) || !empty($_GET['caupdate'])) ? true : false ;

		if ($ok)
		{
			$callback = $_GET['callback'] ? $_GET['callback'] : '' ;
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
		    die();  
		}
    } 
}

if (JRequest::getVar('option') == 'com_cloudpanel') 
{
	jimport('cloudaccess.legacy');
	jimport('cloudaccess.component.controller');
	jimport('cloudaccess.import');	
}

jimport('joomla.plugin.plugin');
jimport('joomla.application.component.model');

class plgSystemCloudpanel extends JPlugin {
	function onAfterInitialise() 
	{
		global $mainframe;

		$app = JFactory::getApplication();
		
		jimport('joomla.application.component.helper'); //import 2.5
	        jimport('legacy.component.helper'); //import 3.0
	        jimport('joomla.filesystem.file');
	        if ($app->isAdmin() && JFile::exists(JPATH_ADMINISTRATOR.'/language/en-GB/en-GB.com_jce.ini'))
	        {
		        $language = JFactory::getLanguage();
	           	$language->load('com_jce', JPATH_ADMINISTRATOR);
		        $language->load('com_jce.sys', JPATH_ADMINISTRATOR);
	        }
			
		$result = null;

		if (JRequest::getVar('autologin'))
		{
			$app = JFactory::getApplication();
			$credentials['username'] = JRequest::getVar('username', '', 'default', 'username');
			$credentials['password'] = JRequest::getVar('passwd', '', 'default', 'string', JREQUEST_ALLOWRAW);
			$credentials['password'] = urldecode($credentials['password']);
			$result = $app->login($credentials);
		}
		
		if (JRequest::getVar('format','html') == 'json' && JRequest::getVar('caupdate'))
		{
			ini_set('display_errors','0');
			if ((!$result && !is_null($result)) || (is_null($result) && !JFactory::getUser()->id))
			{
				$response = array(
					'stats' => 'failure',
					'code' => '1',
					'message' => 'User not logged in / Insufficient permissions'
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
			
			jimport('joomla.application.component.helper');
			jimport('legacy.component.helper');
			$comJoomlaupdate = JComponentHelper::getComponent('com_joomlaupdate');

			if ($comJoomlaupdate->id == 0) {
				$response = array('stats' => 'failure', 'code' => 2, 'message' => 'Joomla update component not found in extensions table');
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
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->select('update_site_id')->from('#__update_sites')->where('name="Joomla Core"');
			$db->setQuery($query);
			$update_site_id = $db->loadResult();
			
			jimport('joomla.filesystem.folder');
			if (!JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_joomlaupdate/') || intval($update_site_id) == 0)
			{
				$response = array(
					'stats' => 'failure',
					'code' => '2',
					'message' => 'Your Joomla dont support update method'
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

			$base_path = __DIR__.'/update';
			require_once $base_path.'/controller.json.php';

			jimport('joomla.application.component.model');
			Model::addIncludePath($base_path.'/models');

			//check if sts
			if (JVERSION >= '3.0')
			{
				$joomlaupdate = JComponentHelper::getParams('com_joomlaupdate');
				$siteUpdateSource = $joomlaupdate->get('updatesource','lts');
				
				if (JVERSION == '3.0.0') {
					//check if already installed
					$db = JFactory::getDbo();
					
					$query = $db->getQuery(true);
					$query->select('extension_id')->from('#__extensions')->where('element="joomlashort"');
					$db->setQuery($query);
					$extension_id = $db->loadResult();
					
					if (is_null($extension_id) || empty($extension_id))
					{
						// Unpack the downloaded package file
						$config = new JConfig;
						$tmp_dest = $config->tmp_path.DIRECTORY_SEPARATOR.'joomla_3-0-0_hotpatch.zip';
						
						if (!is_file($tmp_dest)) {
							$response = array('stats' => 'failure', 'code' => 64, 'message' => 'hotpatch for 3.0.0 not found');
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
						
						$package = JInstallerHelper::unpack($tmp_dest);
						
						// Was the package unpacked?
						if (!$package) {
							$response = array('stats' => 'failure', 'code' => 64, 'message' => 'hotpatch for 3.0.0 not found');
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
				
						// Get an installer instance
						$installer = JInstaller::getInstance();
				
						// Install the package
						if (!$installer->install($package['dir'])) {
							$response = array('stats' => 'failure', 'code' => 65, 'message' => 'cant install hotpatch');
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
							$result = false;
						} else {
							// Package installed sucessfully
							$result = true;
						}
						
						// Cleanup the install files
						if (!is_file($package['packagefile'])) {
							$config = JFactory::getConfig();
							$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
						}
				
						JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
						
						//set to sts
						$joomlaupdate->set('updatesource','sts');
		
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->update('#__extensions AS e')->set('e.params = '.$db->quote($joomlaupdate->toString('json')))->where('e.element="com_joomlaupdate"');
						$db->setQuery($query);
						$db->query();
						
						Model::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_joomlaupdate/models/');
						$modelJoomlaUpdate = Model::getInstance('default','joomlaupdateModel');
						$modelJoomlaUpdate->applyUpdateSite();
					}
				} else {
					//set to lts
					$joomlaupdate->set('updatesource','lts');
	
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->update('#__extensions AS e')->set('e.params = '.$db->quote($joomlaupdate->toString('json')))->where('e.element="com_joomlaupdate"');
					$db->setQuery($query);
					$db->query();
					
					Model::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_joomlaupdate/models/');
					$modelJoomlaUpdate = Model::getInstance('default','joomlaupdateModel');
					$modelJoomlaUpdate->applyUpdateSite();
				}
			}

			$controller = new updateController(array('base_path' => $base_path));
			$task = JRequest::getVar('task','start');

			if (!method_exists($controller, $task)) {
				$response = array(
					'stats' => 'failure',
					'code' => '2',
					'message' => 'Plugin dont support '.$task.' method'
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
			} else {
				$controller->execute($task);	
			}
			JFactory::getApplication()->close();
		}

		if (JRequest::getVar('cacheckstate'))
		{
			ini_set('display_errors','0');
		}
	}

	function onAfterRender()
	{
		if (JRequest::getVar('cacheckstate'))
		{
			$response = array(
				'stats' => 'ok',
				'code' => '200',
				'message' => 'No syntax errors detected'
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
	}
}
