<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
<ul class="waiting-families">
<?php foreach($this->families as $family) : ?>
	<li>
		<a name="profile-<?php echo $family->id; ?>"></a>
		<?php
		
		if(is_file(JPATH_SITE.'/uploads/successes/'.$family->id.'/165_120_'.$family->success_story_image))
		{
			echo '<img src="/uploads/successes/'.$family->id.'/165_120_'.$family->success_story_image.'" alt="'.$family->last_name.' Family" />';
		}
		else
		{
			echo '<img src="/images/comingsoon.jpg" alt="Adoption Profile Photo Coming Soon" />';
		}
		?>
		<h3><?php
	
			echo $family->first_name;
			if($family->spouse_name)
			{
				echo ' &amp; '.$family->spouse_name;
			}
		
		?></h3>
		<div class="success-story"><p><?php echo nl2br($family->success_story) ?></p></div>
		<div class="clear"></div>
	</li>	
<?php endforeach; ?>
</ul>
<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>