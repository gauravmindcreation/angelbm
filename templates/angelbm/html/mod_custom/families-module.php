<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

defined('_JEXEC') or die;

$profilesPath = JPATH_ROOT . '/components/com_profiles';
include_once $profilesPath . '/models/families.php';
include_once $profilesPath . '/helpers/profiles.php';

$oldDatabase = JFactory::getDbo();

JFactory::$database = ProfilesHelper::getDbo();

$config = array(
	'dbo' => JFactory::getDbo(),
	'ignore_request' => true
);
$model = new ProfilesModelFamilies($config);
$model->setState('list.realLimit', 6);

$families = $model->getItems();

JFactory::$database = $oldDatabase;
?>

<div class="container-fluid recent-families">
	<h3 class="clearfix">Families Waiting to Adopt <a class="pull-right" href="/waiting-families">view all &gt;</a></h3>
	<div class="row-fluid">
		<?php foreach ($families as $profile) : ?>
		<div class="span2">
		<?php $aboutImage = str_replace(' ', '%20', $profile->about_us_image)?>
			<div class="img">
				<a href="<?php echo JRoute::_('index.php?option=com_profiles&view=profile&Itemid=115&id=' . $profile->id); ?>">
					<img src="http://www.angeladoptioninc.com/uploads/profiles/<?php echo $profile->id .'/'.$aboutImage; ?>" alt=""/>
				</a>
			</div>
			<a class="name" href="<?php echo JRoute::_('index.php?option=com_profiles&view=profile&Itemid=115&id=' . $profile->id); ?>">
				<?php echo $profile->first_name . ($profile->spouse_name ? ' &amp; ' . $profile->spouse_name : ''); ?>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
</div>