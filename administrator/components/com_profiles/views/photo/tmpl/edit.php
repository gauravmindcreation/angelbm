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

jimport('joomla.filesystem.file');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$uid = JRequest::getVar('uid');

$options = array(
	'startOffset' => 2,
	'onBackground' => 'function(title, description)
	{
		if(title.hasClass("family_tab_2"))
		{
			window.location="index.php?option=com_profiles&view=family&layout=edit&id='.$uid.'";
		}
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
    }',
    'useCookie' => 'true',
);
echo JHtml::_('tabs.start', 'family_tabs', $options);
echo JHtml::_('tabs.panel', JText::_('COM_PROFILES_LEGEND_FAMILY'), 'family_tab_1');

echo 'When you click the tab, you should be automatically directed to a new page to manage the profile. If you are not, please <a href="index.php?option=com_profiles&view=family&layout=edit&id='.JRequest::getVar('uid').'">click here</a>.';

echo JHtml::_('tabs.panel', JText::_('COM_PROFILES_LEGEND_PHOTO'), 'family_tab_2');
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
	            if ($uid) {
					echo '<input type="hidden" value="'.$uid.'" name="jform[family_id]" id="jform_family_id_id">'.
						 '<input type="hidden" value="true" name="uid_set">';
				} else {
					echo $this->form->getLabel('family_id').$this->form->getInput('family_id');
				}
				?>
				</li>
	            
				<li><?php echo $this->form->getLabel('path'); ?>
				<?php echo $this->form->getInput('path');
				
				$img = '/uploads/profiles/'.$this->form->getValue('family_id').'/thumbnail_'.$this->form->getValue('path');
				if (JFile::exists(JPATH_SITE.$img)) {
					echo '<img src="'.$img.'" />';
				}
				
				?></li>
            </ul>
            <div style="display:none">
	            <?php echo $this->form->getInput('state'), $this->form->getInput('checked_out'), $this->form->getInput('checked_out_time'); ?>
	        </div>
		</fieldset>
		<fieldset class="adminform">
			<legend>Gallery</legend>
			<ul class="adminformlist" id="sortable">
			<?php foreach($this->getPhotos() as $photo) {
				$img = '/uploads/profiles/'.JRequest::getVar('uid').'/150_'.$photo->path;
				if (is_file(JPATH_SITE.$img)) {
					echo '
					<li data-pk="'.$photo->id.'" id="deleteImage'.$photo->id.'">
						<span title="Delete this image." onclick="delImage('.$photo->id.')">
							Delete
						</span>
						<img src="'.$img.'" style="height:150px" />
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
<?php
$this->document
	->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
	->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js')
	->addScriptDeclaration('
// <![CDATA[
Joomla.submitbutton = function(task)
{
	if (task == "photo.cancel" || document.formvalidator.isValid(document.id("photo-form"))) {
		Joomla.submitform(task, document.getElementById("photo-form"));
	}
	else {
		alert("'.JText::_('JGLOBAL_VALIDATION_FORM_FAILED').'");
	}
}
jQuery.noConflict();
jQuery(document).ready(function($){
	var sortablePics = $("#sortable");
	sortablePics.sortable({
		stop:	function(event, ui)
		{
			var order		= [];
			sortablePics.find("li").each(function(){
				var base	= $(this);
				var pk		= base.attr("data-pk");
				order.push(pk);
			});
			var xhr = new Request({
				url: "index.php?option=com_profiles&tmpl=component&task=photo.reorderphotos",
				method: "get"
			});
			xhr.send("new_order=" + order.join(","));
		}
	});
});
	
function delImage(photo_id)
{
	if(confirm("Are you sure you want to delete this image?"))
	{
		var xhr = new Request({
			url: "index.php?option=com_profiles&tmpl=component&task=photo.delete",
			method: "get",
			onSuccess: function()
			{
				el = document.getElementById("deleteImage" + photo_id);
				el.parentNode.removeChild(el);
			},
			onFailure: function()
			{
				alert("There was an error deleting your image, please refresh the page and try again.");
			}
		});
		xhr.send("id=" + photo_id);
	}
}
// ]]>
');
echo JHtml::_('tabs.end');