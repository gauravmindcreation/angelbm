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

$options = array(
	'onBackground' => 'function(title, description)
	{
		if(title.hasClass("family_tab_1"))
		{
			if(confirm("Have changes to save? If so, click Cancel, and then Save. Otherwise, click OK."))
			{
				window.location="index.php?option=com_profiles&view=photo&layout=edit&uid='.$id.'";
			}
		}
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
    }',
    'useCookie' => 'true',
);
echo JHtml::_('tabs.start', 'family_tabs', $options);
echo JHtml::_('tabs.panel', JText::_('COM_PROFILES_LEGEND_FAMILY'), 'family_tab_1');
?>
<script type="text/javascript">
// <![CDATA[
Joomla.submitbutton = function(task) {
	var confirmation = true;
	if (task === 'family.cancel') {
		var confirmation = confirm('Have changes to save? Click Cancel, and then Save. Otherwise, click ok.');
	}
	if (confirmation) {
		if (task == 'family.cancel' || document.formvalidator.isValid(document.id('family-form'))) {
			Joomla.submitform(task, document.getElementById('family-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
};
// ]]>
</script>
<form action="<?php echo JRoute::_('index.php?option=com_profiles&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="family-form" class="form-validate" enctype="multipart/form-data">
	<input type="hidden" value="<?php echo $this->form->getValue('user_id'); ?>" name="old_id" />
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_LEGEND_FAMILY'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?>
				<?php
				// Create the front-end link to a family and open in a new window
				if ($id && $this->form->getValue('state') && $this->form->getValue('username')) {
					// TODO: create an SEF url to the frontend profile using JRoute::_()
					// $url = JRoute::_('/index.php?option=com_profiles&view=profile&Itemid=506&id='.$id);
					//$url  = '/profile/'.$this->form->getValue('username');
					$url = "/index.php?option=com_profiles&view=profile&id={$id}";
					echo ' <a style="padding-top:5px;font-size:13px;display:block;" target="_blank" href="'.$url.'">View This Family</a>';
				}
				
				?></li>
	            <li><?php echo $this->form->getLabel('state'), $this->form->getInput('state'); ?></li>
				<li><?php echo $this->form->getLabel('profile_status'), $this->form->getInput('profile_status'); ?></li>
				<li><?php echo $this->form->getLabel('username'), $this->form->getInput('username'); ?></li>
				<li><?php echo $this->form->getLabel('first_name'), $this->form->getInput('first_name'); ?></li>
				<li><?php echo $this->form->getLabel('spouse_name'), $this->form->getInput('spouse_name'); ?></li>
				<li><?php echo $this->form->getLabel('last_name'), $this->form->getInput('last_name'); ?></li>
				<li><?php echo $this->form->getLabel('video'), $this->form->getInput('video'); ?></li>
				<li style="display:none;"><?php
				
					echo $this->form->getLabel('pdf'), $this->form->getInput('pdf');
					
					$pdf = '/uploads/profiles/'.$id.'/'.$this->form->getValue('pdf');
					if (JFile::exists(JPATH_SITE.$pdf)) {
						echo '<br style="clear:both" /><a href="'.$pdf.'">Download Current PDF</a>';
					}
					
				?></li>
            </ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_FORM_LBL_FAMILY_DEAR_BIRTHMOTHER'); ?></legend>
			<ul class="adminformlist">
            
			<li><?php echo $this->form->getInput('dear_birthmother'); ?></li>

            </ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('Description'); ?></legend>
			<ul class="adminformlist">
            
			<li><?php echo $this->form->getInput('description'); ?></li>

            </ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_FORM_LBL_FAMILY_ABOUT_US'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getInput('about_us'); ?></li>
				<li><?php
				
					echo $this->form->getLabel('about_us_image'), $this->form->getInput('about_us_image');

					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('about_us_image');
					if (JFile::exists(JPATH_SITE.$img)) {
						$field = 'about_us_image';
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
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_FORM_LBL_FAMILY_OUR_HOME'); ?></legend>
			<ul class="adminformlist">
            	<li><?php echo $this->form->getInput('our_home'); ?></li>
            	<li><?php
            	
            		echo $this->form->getLabel('our_home_image'), $this->form->getInput('our_home_image');
			
					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('our_home_image');
					if (JFile::exists(JPATH_SITE.$img)) {
						$field = 'our_home_image';
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
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_FORM_LBL_FAMILY_EXT_FAMILY'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getInput('ext_family'); ?></li>
				<li><?php
				
					echo $this->form->getLabel('ext_family_image'), $this->form->getInput('ext_family_image');
					
					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('ext_family_image');
					if (JFile::exists(JPATH_SITE.$img)) {
						$field = 'ext_family_image';
						echo '
						<div class="photoholder" id="deleteImage-'.$field.'">
							<span title="Delete this image." onclick="delImage(\''.$field.'\')">
								Delete
							</span>
							<img src="'.$img.'" />
						</div>';
					}
				
					echo $this->form->getLabel('ext_family_image_spouse'), $this->form->getInput('ext_family_image_spouse');
					
					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('ext_family_image_spouse');
					if (JFile::exists(JPATH_SITE.$img)) {
						$field = 'ext_family_image_spouse';
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
		<?php /*
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_FORM_LBL_FAMILY_FAMILY_TRADITIONS'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getInput('family_traditions'); ?></li>
				<li><?php
				
					echo $this->form->getLabel('family_traditions_image'), $this->form->getInput('family_traditions_image');
								
					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('family_traditions_image');
					if (JFile::exists(JPATH_SITE.$img)) {
						$field = 'family_traditions_image';
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
		*/ ?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROFILES_FORM_LBL_FAMILY_ADOPTION_STORY'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('adoption_story_title'), $this->form->getInput('adoption_story_title'); ?></li>
				<li style="clear:both"></li>
				<li><?php echo $this->form->getInput('adoption_story'); ?></li>
				<li><?php
				
					echo $this->form->getLabel('adoption_story_image'), $this->form->getInput('adoption_story_image');
								
					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('adoption_story_image');
					if (JFile::exists(JPATH_SITE.$img)) {
						$field = 'adoption_story_image';
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
		<?php /*
		<fieldset class="adminform">
			<legend><?php echo JText::_('What we do for fun.'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getInput('do_for_fun'); ?></li>
				<li><?php
				
					echo $this->form->getLabel('do_for_fun_image'), $this->form->getInput('do_for_fun_image');

					$img = '/uploads/profiles/'.$id.'/150_'.$this->form->getValue('do_for_fun_image');
					if(is_file(JPATH_SITE.$img))
					{
						$field = 'do_for_fun';
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
		*/ ?>
		<?php echo $this->form->getInput('checked_out'), $this->form->getInput('checked_out_time'); ?>
	</div>

	<div class="width-40 fltrt">
		<?php echo  JHtml::_('sliders.start'); ?>
			
			<?php echo JHtml::_('sliders.panel', 'Adoption Preferences', ''); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">                        
					<li><?php echo $this->form->getLabel('adopt_gender'), $this->form->getInput('adopt_gender'); ?></li>
		
		            
					<li><?php echo $this->form->getLabel('adopt_race'), $this->form->getInput('adopt_race'); ?></li>
				</ul>
			</fieldset>
			
			<?php
			$favs = array(
				'race',
				'occupation',
				'religion',
				'education',
				'food',
				'hobby',
				'movie',
				'sport',
				'holiday',
				'music_group',
				'tv_show',
				'book',
				'subject_in_school',
				'childhood_toy',
				'childhood_memory',
				'tradition',
				'family_activity',
				'memory_w_spouse',
				'vacation_spot',
				'animal'
			);
			echo JHtml::_('sliders.panel', 'Facts About Me', '');
			?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach($favs as $fav) {
						$field = 'my_'.$fav;
						echo '<li>'.$this->form->getLabel($field).$this->form->getInput($field).'</li>';
					} ?>
				</ul>
			</fieldset>
			
			<?php echo JHtml::_('sliders.panel', 'Facts About Spouse', ''); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach($favs as $fav) {
						$field = 'spouse_'.$fav;
						echo '<li>'.$this->form->getLabel($field).$this->form->getInput($field).'</li>';
					} ?>
				</ul>
			</fieldset>
			
			<?php echo JHtml::_('sliders.panel', 'SEO Settings', ''); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">            
					<li><?php echo $this->form->getLabel('seo_title'), $this->form->getInput('seo_title'); ?></li>
		
		            
					<li><?php echo $this->form->getLabel('seo_keywords'), $this->form->getInput('seo_keywords'); ?></li>
		
		            
					<li><?php echo $this->form->getLabel('seo_description'), $this->form->getInput('seo_description'); ?></li>
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
	if (confirm('Are you sure you want to delete this image?')) {
		var xhr = new Request({
			url: 'index.php?option=com_profiles&tmpl=component&task=family.deletephoto&id=' + <?php echo $id; ?>,
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
<?php
if ($id) {
	echo JHtml::_('tabs.panel', JText::_('COM_PROFILES_LEGEND_PHOTO'), 'family_tab_2');
	echo 'When you click the tab, you should be automatically directed to a new page to upload photos. If you are not, please <a href="index.php?option=com_profiles&view=photo&layout=edit&uid='.$id.'">click here</a>.';
} else {
	echo JHtml::_('tabs.panel', JText::_('COM_PROFILES_LEGEND_PHOTO'), 'family_tab_2');
	echo 'Before you can upload photos, you must save the profile by clicking the orange check mark above, and then this tab will function.';
}

echo JHtml::_('tabs.end');