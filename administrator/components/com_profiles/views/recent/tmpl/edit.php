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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$id = $this->form->getValue('id');

?>
<script type="text/javascript">
// <![CDATA[
Joomla.submitbutton = function(task) {
	var confirmation = true;
	if(task === 'recent.cancel')
	{
		var confirmation = confirm('Have changes to save? Click Cancel, and then Save. Otherwise, click ok.');
	}
	if(confirmation)
	{
		if (task == 'recent.cancel' || document.formvalidator.isValid(document.id('recent-form'))) {
			Joomla.submitform(task, document.getElementById('recent-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
};
// ]]>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_profiles&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="recent-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_LEGEND_FAMILY'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				
	            <li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>
	
	            
				<li><?php echo $this->form->getLabel('first_name'); ?>
				<?php echo $this->form->getInput('first_name'); ?></li>
	
	            
				<li><?php echo $this->form->getLabel('spouse_name'); ?>
				<?php echo $this->form->getInput('spouse_name'); ?></li>
	
	            
				<li><?php echo $this->form->getLabel('last_name'); ?>
				<?php echo $this->form->getInput('last_name'); ?></li>
	
	            
				<li><?php echo $this->form->getLabel('rotator_top'); ?>
				<?php echo $this->form->getInput('rotator_top'); ?></li>
	
	            
				<li><?php echo $this->form->getLabel('rotator_bottom'); ?>
				<?php echo $this->form->getInput('rotator_bottom'); ?></li>
            </ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_TITLE_RECENTS'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getInput('recent_story'); ?></li>
				<li><?php
					echo $this->form->getLabel('recent_story_image');
					echo $this->form->getInput('recent_story_image');
					
					$img = '/uploads/recents/'.$id.'/150_'.$this->form->getValue('recent_story_image');
					if(is_file(JPATH_SITE.$img))
					{
						$field = 'recent_story_image';
						echo '
						<div class="photoholder" id="deleteImage-'.$field.'">
							<span title="Delete this image." onclick="delImage(\''.$field.'\')">
								Delete
							</span>
							<img src="'.$img.'" />
						</div>';
					}
					
					?></li>
            </ul>
		</fieldset>
		<?php echo $this->form->getInput('checked_out'); ?>
		<?php echo $this->form->getInput('checked_out_time'); ?>
	</div>

	<div class="width-40 fltrt">
		<?php echo  JHtml::_('sliders.start'); ?>
			
			<?php echo JHtml::_('sliders.panel', 'SEO Settings', ''); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">            
					<li><?php echo $this->form->getLabel('seo_title'); ?>
					<?php echo $this->form->getInput('seo_title'); ?></li>
		
		            
					<li><?php echo $this->form->getLabel('seo_keywords'); ?>
					<?php echo $this->form->getInput('seo_keywords'); ?></li>
		
		            
					<li><?php echo $this->form->getLabel('seo_description'); ?>
					<?php echo $this->form->getInput('seo_description'); ?></li>
				</ul>
			</fieldset>
			
		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
<style type="text/css">
.photoholder {
	position: relative;
	clear:both;
	display: block;
}
.photoholder span {
	position:absolute;
	top:5px;
	left:0;
	background:red;
	display:block;
	padding:2px 4px;
	cursor:pointer;
	color:#FFF;
}
</style>
<script type="text/javascript">
// <![CDATA[
function delImage(photo_field)
{
	if(confirm('Are you sure you want to delete this image?'))
	{
		var xhr = new Request({
			url: 'index.php?option=com_profiles&tmpl=component&task=recent.deletephoto&id=' + <?php echo $id; ?>,
			method: 'get',
			onSuccess: function()
			{
				el = document.getElementById('deleteImage-' + photo_field);
				el.parentNode.removeChild(el);
			},
			onFailure: function()
			{
				alert('There was an error deleting your image, please refresh the page and try again.');
			}
		});
		xhr.send('field=' + photo_field);
	}
}
// ]]>
</script>