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
$family = $this->family;

if($family->state !== '1')
{
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_profiles'), 'Not a valid profile.', 'error');
}

$this->addHit();

//var_dump($this->family);

$img_path = '/uploads/profiles/'.$family->id.'/';

$main_image = $this->gallery[0]->path;
if(!$main_image)
{
	$main_image = $family->about_us_image;
}

$doc = JFactory::getDocument();

$fullname = $family->first_name;

global $is_single;
$is_single = true;
if($family->spouse_name)
{
	$is_single = false;
	$fullname .= ' & '.$family->spouse_name;
}

$title = $fullname . ' - ' . $doc->title;
if($family->seo_title)
{
	$title = $family->seo_title;
}
$doc->setTitle($title);

$fullname = htmlspecialchars($fullname);

?>
				<div id="user_profile">
					<?php if(JRequest::getVar('tmpl') !== 'component'): ?>
	            	<a href="<?php echo JRoute::_('index.php?option=com_profiles'); ?>">&laquo; back to waiting families</a>
	            	<?php endif; ?>
	            	<div class="links">
	            		<?php
	            		
	            		$videorel = '';
	            		if(strpos($family->video, 'yout'))
	            		{
	            			$videorel = 'youtube';
	            		}
	            		elseif(strpos($family->video, 'vimeo'))
	            		{
	            			$videorel = 'vimeo';
	            		}
	            		
	            		echo $family->video ? '<a rel="'.$videorel.'" class="video" href="'.$family->video.'">our video</a>' : '';
	            		echo $family->pdf ? '<a class="pdf" rel="external" href="http://www.angeladoptioninc.com/uploads/profiles/'.$family->id.'/'.$family->pdf.'">our book</a>' : '';
	            		echo $this->gallery ? '<a class="gallery" id="ourgallery" href="javascript:void(0);">our gallery</a>' : '';
	            		
	            		?>
	            	</div>
	            	<br /><h2><?php echo $fullname; ?></h2>
	            	<div id="profile_top">
	                	<div id="profile-photos">
	                		<img class="main-photo" width="307px" height="254px" alt="<?php echo $fullname; ?>" src="http://www.angeladoptioninc.com<?php echo $img_path.'310_220_'.$main_image; ?>" />
                        	<?php if($this->gallery) : ?>
							<span class="next"></span>
							<span class="prev"></span>
	                        <div class="thumbs">
	                            <div class="thumbs_container" style="width:<?php echo ((int) count($this->gallery)*82)?>px">
	                            	<?php
									foreach($this->gallery as $photo)
									{
										echo family_image($photo->path, $family->id, '64_64_', false);
									}
									?>
	                            </div>
	                        </div>
	                        <div class="clear"></div>
	                        <?php endif; ?>
	                    </div>
	                    <div id="quick_info">
	                    	
	                    	<?php
	                    	if(isset($family->adopt_race))
	                    	{
	                    		$races = implode(', ', json_decode($family->adopt_race, true));
	                    		echo '<p><span>Race of child interested in adopting:</span>'.$races.'</p>';
	                    	}
	                    	if(isset($family->adopt_gender))
	                    	{
	                    		echo '<p><span>Gender of child interested in adopting:</span>'.$family->adopt_gender.'</p>';
	                    	}
							?>
	                    	<div class="buttons">
	                    		<a href="<?php echo JRoute::_('/index.php?option=com_profiles&amp;tmpl=component&amp;view=contact&amp;id='.$family->id); ?>" class="contact">contact this family</a>
	                    	</div>
	                    </div>
	                </div>
	                <div class="clear"></div>
	            	<div id="profile_tabs">
	                	<?php if($family->dear_birthmother): ?>
	                	<div class="section" id="dear_birthmother">
	                    	<h3>Dear Birthmother,</h3>
	    	            	<?php
							echo '<p>'.format_profile_text($family->dear_birthmother).'</p>';
							?>
						</div>
						<?php endif; ?>
	                    
	                	<?php if($family->about_us): ?>
	        			<hr />
	                	<div class="section" id="about_us">
	                    	<h3>About <?php echo me_or_us(); ?></h3>
	    	            	<?php 
							echo family_image($family->about_us_image, $family->id);
							echo '<p>'.format_profile_text($family->about_us).'</p>';
							?>
						</div>
						<?php endif; ?>
	                    
	                	<?php if($family->our_home): ?>
	        			<hr />
	        			<div class="section" id="our_home">
		                	<h3><?php echo my_or_our(); ?> Home</h3>
	                		<?php
							echo family_image($family->our_home_image, $family->id);
							echo '<p>'.format_profile_text($family->our_home).'</p>';
							?>
						</div>
						<?php endif; ?>
	                    
	                	<?php if($family->ext_family): ?>
	        			<hr />
	        			<div class="section" id="ext_family">
		                	<h3><?php echo my_or_our(); ?> Extended Family</h3>
	    	            	<?php
							echo family_image($family->ext_family_image, $family->id);
							echo family_image($family->ext_family_image_spouse, $family->id);
	                    	echo '<p>'.format_profile_text($family->ext_family).'</p>';
	                    	?>
	                    </div>
	                    <?php endif; ?>
	                    
	                	<?php if($family->family_traditions): ?>
	        			<hr />
	        			<div class="section" id="family_traditions">
		                	<h3><?php echo my_or_our(); ?> Family Traditions</h3>
	    	            	<?php
							echo family_image($family->family_traditions_image, $family->id);
							echo '<p>'.format_profile_text($family->family_traditions).'</p>';
							?>
						</div>
						<?php endif; ?>
	                    
	                	<?php if($family->adoption_story): ?>
	        			<hr />
	        			<div class="section" id="adoption_story">
		                	<h3>What Led <?php echo me_or_us(); ?> To Adoption</h3>
	    	            	<?php
							echo family_image($family->adoption_story_image, $family->id);
							echo '<p>'.format_profile_text($family->adoption_story).'</p>';
							?>
						</div>
						<?php endif; ?>
						
	            		<hr />
	                    <div class="row" id="favorites">
							
	                    	<div class="span6">
		                    	<h3>Facts About <?php echo $family->first_name?></h3>
		                    	<ul class="unstyled">
		                    		<li><span>Occupation:</span> <?php echo htmlspecialchars($family->my_occupation);?></li>
		                    		<li><span>Religion:</span> <?php echo htmlspecialchars($family->my_religion);?></li>
		                    		<li><span>Education:</span> <?php echo htmlspecialchars($family->my_education);?></li>
		                    		<li><span>Favorite Food:</span> <?php echo htmlspecialchars($family->my_food);?></li>
		                    		<li><span>Favorite Hobby:</span> <?php echo htmlspecialchars($family->my_hobby);?></li>
		                    		<li><span>Favorite Movie:</span> <?php echo htmlspecialchars($family->my_movie);?></li>
		                    		<li><span>Favorite Sport:</span> <?php echo htmlspecialchars($family->my_sport);?></li>
		                    		<li><span>Favorite Holiday:</span> <?php echo htmlspecialchars($family->my_holiday);?></li>
		                    		<li><span>Favorite Music Group:</span> <?php echo htmlspecialchars($family->my_music_group);?></li>
		                    		<li><span>Favorite TV Show:</span> <?php echo htmlspecialchars($family->my_tv_show);?></li>
		                    		<li><span>Favorite Book:</span> <?php echo htmlspecialchars($family->my_book);?></li>
		                    		<li><span>Favorite Subject in School:</span> <?php echo htmlspecialchars($family->my_subject_in_school);?></li>
		                    		<li><span>Favorite Vacation Spot:</span> <?php echo htmlspecialchars($family->my_vacation_spot);?></li>
		                    	</ul>
	                    	</div>
	                        <?php if(!$is_single) : ?>
	                    	<div class="span6">
		                    	<h3>Facts About <?php echo $family->spouse_name?></h3>
		                    	<ul class="unstyled">
		                    		<li><span>Occupation:</span> <?php echo htmlspecialchars($family->spouse_occupation);?></li>
		                    		<li><span>Religion:</span> <?php echo htmlspecialchars($family->spouse_religion);?></li>
		                    		<li><span>Education:</span> <?php echo htmlspecialchars($family->spouse_education);?></li>
		                    		<li><span>Favorite Food:</span> <?php echo htmlspecialchars($family->spouse_food);?></li>
		                    		<li><span>Favorite Hobby:</span> <?php echo htmlspecialchars($family->spouse_hobby);?></li>
		                    		<li><span>Favorite Movie:</span> <?php echo htmlspecialchars($family->spouse_movie);?></li>
		                    		<li><span>Favorite Sport:</span> <?php echo htmlspecialchars($family->spouse_sport);?></li>
		                    		<li><span>Favorite Holiday:</span> <?php echo htmlspecialchars($family->spouse_holiday);?></li>
		                    		<li><span>Favorite Music Group:</span> <?php echo htmlspecialchars($family->spouse_music_group);?></li>
		                    		<li><span>Favorite TV Show:</span> <?php echo htmlspecialchars($family->spouse_tv_show);?></li>
		                    		<li><span>Favorite Book:</span> <?php echo htmlspecialchars($family->spouse_book);?></li>
		                    		<li><span>Favorite Subject in School:</span> <?php echo htmlspecialchars($family->spouse_subject_in_school);?></li>
		                    		<li><span>Favorite Vacation Spot:</span> <?php echo htmlspecialchars($family->spouse_vacation_spot);?></li>
		                    	</ul>
	                    	</div>
	                        <?php endif; ?>
	                        <div class="clear"></div>
	                    </div>
	                </div>
					<a href="<?php echo JRoute::_('/index.php?option=com_profiles&amp;tmpl=component&amp;view=contact&amp;id='.$family->id); ?>" class="bottom_contact">contact this family</a>
				</div>