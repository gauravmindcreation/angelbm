<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

defined('_JEXEC') or die;

// Set the custom DB Object
$oldDbo = JFactory::getDbo();

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
	JFactory::$database = JDatabaseDriver::getInstance($options);
}
catch (RuntimeException $e)
{
	if (!headers_sent())
	{
		header('HTTP/1.1 500 Internal Server Error');
	}

	jexit('Database Error: ' . $e->getMessage());
}
// Do custom db stuff here
include_once JPATH_ROOT . '/components/com_profiles/models/families.php';
$config = array(
	'dbo' => JFactory::getDbo(),
	'ignore_request' => true
);
$model = new ProfilesModelFamilies($config);
$model->setState('list.realLimit', 6);

$families = $model->getItems();

// Reset to previous connection
JFactory::$database = $oldDbo;
?>

<div class="container-fluid recent-families">
	<h3 class="clearfix">Families Waiting to Adopt <a class="pull-right" href="/waiting-families">view all &gt;</a></h3>
	<div class="row-fluid">
		<?php foreach ($families as $profile) : ?>
		<div class="span2">
		<?php //print_r($profile);die;?>
			<div class="img">
				<a href="<?php echo JRoute::_('index.php?option=com_profiles&view=profile&Itemid=115&id=' . $profile->id); ?>">
					<img src="http://www.angeladoptioninc.com/uploads/profiles/<?php echo $profile->id .'/'.$profile->about_us_image;?> " alt=""/>
				</a>
			</div>
			<a class="name" href="<?php echo JRoute::_('index.php?option=com_profiles&view=profile&Itemid=115&id=' . $profile->id); ?>">
				<?php echo $profile->first_name . ($profile->spouse_name ? ' &amp; ' . $profile->spouse_name : ''); ?>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
</div>