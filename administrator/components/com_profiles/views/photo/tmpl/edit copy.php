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
jimport ( 'joomla.html.pane');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'photo.cancel' || document.formvalidator.isValid(document.id('photo-form'))) {
			Joomla.submitform(task, document.getElementById('photo-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<?php
$tabs =& JPane::getInstance('tabs', array('startOffset'=>2));
echo $tabs->startPane('family_tabs');
echo $tabs->startPanel('<span onclick="window.location=\'index.php?option=com_profiles&view=family&layout=edit&id='.
JRequest::getVar('uid').'\'">'.JText::_('COM_PROFILES_LEGEND_FAMILY').'</span>', 'family_tab_1');

echo 'When you click the tab, you should be automatically directed to a new page to manage the profile. If you are not, please <a href="index.php?option=com_profiles&view=family&layout=edit&id='.JRequest::getVar('uid').'">click here</a>.';

echo $tabs->endPanel();
echo $tabs->startPanel( JText::_('COM_PROFILES_LEGEND_PHOTO'), 'family_tab_2');
?>
<form action="<?php echo JRoute::_('index.php?option=com_profiles&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="photo-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<legend>New Photo</legend>
			<ul class="adminformlist">

            
			<li style="display:none"><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>

            <li>
            <?php
            if(JRequest::getVar('uid'))
            {
				echo '<input type="hidden" value="'.$this->getUserId().'" name="jform[family_id]" id="jform_family_id_id">'.
					 '<input type="hidden" value="true" name="uid_set">';
			}
			else
			{
				echo $this->form->getLabel('family_id').$this->form->getInput('family_id');
			}
			?>
			</li>
            
			<li><?php echo $this->form->getLabel('path'); ?>
			<?php echo $this->form->getInput('path'); ?><?php
			
			$img = '/uploads/profiles/'.$this->form->getValue('family_id').'/thumbnail_'.$this->form->getValue('path');
			if(is_file(JPATH_SITE.$img))
			{
				echo '<img src="'.$img.'" />';
			}
			
			?></li>

            

            <li><?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?></li><li><?php echo $this->form->getLabel('checked_out'); ?>
                    <?php echo $this->form->getInput('checked_out'); ?></li><li><?php echo $this->form->getLabel('checked_out_time'); ?>
                    <?php echo $this->form->getInput('checked_out_time'); ?></li>

            </ul>
		</fieldset>
		<fieldset class="adminform">
			<legend>Gallery</legend>
			<ul class="adminformlist" id="sortable">
			<?php foreach($this->getPhotos() as $photo)
			{
				$img = '/uploads/profiles/'.JRequest::getVar('uid').'/150_150_'.$photo->path;
				if(is_file(JPATH_SITE.$img))
				{
					echo '
					<li data-pk="'.$photo->id.'" id="deleteImage'.$photo->id.'">
						<span title="Delete this image." onclick="delImage('.$photo->id.')">
							Delete
						</span>
						<img src="'.$img.'" />
					</li>';
				}
			} ?>
			</ul>
			<input type="hidden" name="new_ordering" value="" />
		</fieldset>
	</div>


	<input type="hidden" name="task" value="" />
	<input type="hidden" name="uid" value="<?php echo JRequest::getVar('uid'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
<style type="text/css">
#sortable li {
	position: relative;
	float:left;
}
#sortable li span {
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
<script type="text/javascript" src="/templates/beez_20/js/jquery-1.6.2.min.js"></script>
<script type="Text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script type="text/javascript">
// <![CDATA[
	jQuery.noConflict();
	jQuery(document).ready(function($){
		$('#sortable').sortable({
			stop:	function(event, ui)
			{
				var order		= [];
				$('#sortable').find('li').each(function(){
					var base		= $(this);
					var pk			= base.attr('data-pk');
					order.push(pk);
				});
				var xhr = getXhr();
				xhr.open("GET", 'index.php?option=com_profiles&tmpl=component&task=photo.reorderphotos&new_order=' + order.join(','));
				xhr.send(null);
				xhr.onreadystatechange = function()
				{
					if(xhr.readyState == 4)
					{
						var respObj = JSON.parse(xhr.response);
						/*
						if(respObj.result == 'success')
						{
							alert('The order has been saved.');
						}
						if(respObj.result == 'fail')
						{
							alert('The order could not be saved.');
						}
						*/
					}
				}
				
			}
		});
	});
	function getXhr() {
		var xhr = null;
		if (window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
		} else if (window.createRequest) {
			xhr = window.createRequest();
		} else if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject('Msxml2.XMLHTTP');
			} catch (e) {
				try {
					xhr = new ActiveXObject('Microsoft.XMLHTTP');
				} catch (e) {}
			}
		}
		return xhr;
	};
		
	function delImage(photo_id)
	{
		if(confirm('Are you sure you want to delete this image?'))
		{
			var xhr = getXhr();
			xhr.open("GET", 'index.php?option=com_profiles&tmpl=component&task=photo.delete&id=' + photo_id);
			xhr.send(null);
			xhr.onreadystatechange = function()
			{
				if(xhr.readyState == 4)
				{
					var respObj = JSON.parse(xhr.response);
					// console.log(respObj);
					if(respObj.result == 'success')
					{
						el = document.getElementById('deleteImage' + photo_id);
						el.parentNode.removeChild(el);
					}
					if(respObj.result == 'fail')
					{
						alert('There was an error deleting your image, please try again.');
					}
				}
			}
		}
	}
// ]]>
</script>
<?php
echo $tabs->endPanel();
echo $tabs->endPane();