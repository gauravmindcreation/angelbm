<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$menu = JFactory::getApplication()->getMenu();
$active = $menu->getActive();
$menuname = $active->title;
$parentId = $active->tree[0];
$parentName = $menu->getItem($parentId)->title;

switch ($parentName) {
	case "FAQs":
		$parentName = "Frequently Asked Questions";
		break;
	case "Free Info":
		$parentName = "Free Adoption Information";
		break;
	case "Resources":
		$parentName = "Birthmother Resources";
		break;
}
?>


<div class="custom<?php echo $moduleclass_sfx ?>" <?php if ($params->get('backgroundimage')) : ?> style="background-image:url(<?php echo $params->get('backgroundimage');?>)"<?php endif;?> >
	<?php echo '<h1>'.$parentName.'</h1>'; ?>
</div>