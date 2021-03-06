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

$videorel = '';
if(strpos($family->video, 'yout'))
{
	$videorel = 'youtube';
}
elseif(strpos($family->video, 'vimeo'))
{
	$videorel = 'vimeo';
}

$doc
	->addScriptDeclaration('function ljs(){var d=document;var e=d.createElement("script");e.src="/components/com_profiles/assets/jquery.fancybox.js";e.type="text/javascript";d.body.appendChild(e);var f=d.createElement("script");f.src="/components/com_profiles/assets/js/profiles.js";f.type="text/javascript";d.body.appendChild(f)}if(window.addEventListener)window.addEventListener("load",ljs,false);else if(window.attachEvent)window.attachEvent("onload",ljs);else window.onload=ljs;')
    ->addStyleSheet('/components/com_profiles/assets/jquery.fancybox.css');
if($this->gallery)
{
	$count = count($this->gallery);
	$i = 1;
	$photos = '';
	foreach($this->gallery as $photo)
	{
		$showing = "Image {$i} of {$count}";
		$photos .= PHP_EOL . "\t\t\t\t" . "{";
		$photos .= PHP_EOL . "\t\t\t\t\t" . "'href' : 'http://www.angeladoptioninc.com/uploads/profiles/".$family->id."/".$photo->path."',";
		$photos .= PHP_EOL . "\t\t\t\t\t" . "'title': '<span class=\"name\">".$fullname." </span><span class=\"showing\">".$showing."</span>'";
		$photos .= PHP_EOL . "\t\t\t\t" . "},";
		$i++;
	}
	unset($i);
	$photos = rtrim($photos, ',');
	$doc->addScriptDeclaration("
	jQuery(document).ready(function($){
	    $('#ourgallery').click(function() {
			$.fancybox([".$photos."
				], {
				'padding'		: '0',
				'margin'		: '0',
				'titlePosition'	: 'inside',
				'type'			: 'image',
				'changeFade'	: 0
			});
		});
	});
	");
}
?>
<div id="user_profile">
	<?php if(JRequest::getVar('tmpl') !== 'component'): ?>
	<a href="<?php echo JRoute::_('index.php?option=com_profiles'); ?>">&laquo; back to waiting families</a>
	<br />
	<br />
	<?php endif; ?>
	<div class="row">
		<div class="span6">
			<img class="main-photo" alt="<?php echo $fullname; ?>" src="http://www.angeladoptioninc.com<?php echo $img_path.$main_image; ?>" />
		</div>
		<div class="span6">
			<h2><?php echo $fullname; ?></h2>
			
			<?php
			if (isset($family->adopt_race))
			{
				$races = implode(', ', json_decode($family->adopt_race, true));
				echo '<h5>RACE OF CHILD INTERESTED IN ADOPTING:</h5>';
				echo '<p>'.$races.'</p>';
			}
			if (isset($family->adopt_gender))
			{
				echo '<h5>PREFERRED GENDER:</h5>';
				echo '<p>'.$family->adopt_gender.'</p>';
			}
			?>
			<div class="buttons">
				<a href="#contact-form" class="btn btn-primary contact">contact us</a>
				<?php echo $this->gallery ? '<a class="gallery btn btn-primary" id="ourgallery" href="javascript:void(0);">'. my_or_our() .' gallery</a>' : ''; ?>
				<?php echo $family->video ? '<a rel="'.$videorel.'" class="video btn btn-primary" href="'.$family->video.'">our video</a>' : ''; ?>
			</div>
		</div>
	</div>
	<hr />
	<div id="sections">
		<?php if($family->dear_birthmother): ?>
		<div id="dear_birthmother">
			<h3>Dear Birthmother,</h3>
			<?php
			echo '<p>'.format_profile_text($family->dear_birthmother).'</p>';
			?>
		</div>
		<?php endif; ?>
		<?php if($family->about_us): ?>
		<hr />
		<div class="section" id="about_us">
			<div class="row-fluid">
				<div class="span4">
				<?php echo family_image($family->about_us_image, $family->id); ?>
				</div>
				<div class="span8">
					<h3>About <?php echo me_or_us(); ?></h3>
					<?php
					echo '<p>'.format_profile_text($family->about_us).'</p>';
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>
	    
		<?php if($family->our_home): ?>
		<hr />
		<div class="section" id="our_home">
			<div class="row-fluid">
				<div class="span4">
				<?php echo family_image($family->our_home_image, $family->id); ?>
				</div>
				<div class="span8">
					<h3><?php echo my_or_our(); ?> Home</h3>
					<?php
					echo '<p>'.format_profile_text($family->our_home).'</p>';
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>
	    
		<?php if($family->ext_family): ?>
		<hr />
		<div class="section" id="ext_family">
			<div class="row-fluid">
				<div class="span4">
				<?php echo family_image($family->ext_family_image, $family->id);
				echo family_image($family->ext_family_image_spouse, $family->id); ?>
				</div>
				<div class="span8">
					<h3><?php echo my_or_our(); ?> Extended Family</h3>
					<?php
					echo '<p>'.format_profile_text($family->ext_family).'</p>';
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php if($family->family_traditions): ?>
		<hr />
		<div class="section" id="family_traditions">
			<div class="row-fluid">
				<div class="span4">
				<?php echo family_image($family->family_traditions_image, $family->id); ?>
				</div>
				<div class="span8">
					<h3><?php echo my_or_our(); ?> Family Traditions</h3>
					<?php
					echo '<p>'.format_profile_text($family->family_traditions).'</p>';
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if($family->adoption_story): ?>
		<hr />
		<div class="section" id="adoption_story">
			<div class="row-fluid">
				<div class="span4">
				<?php echo family_image($family->adoption_story_image, $family->id); ?>
				</div>
				<div class="span8">
					<h3>What Led <?php echo me_or_us(); ?> To Adoption</h3>
					<?php
					echo '<p>'.format_profile_text($family->adoption_story).'</p>';
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<hr />
		<div class="section" id="favorites">
			<div class="row-fluid">
				<div class="span<?php echo ($is_single ? "12 single" : "6 myself"); ?>">
					<h3>Facts About <?php echo $family->first_name?></h3>
					<ul class="unstyled">
						<?php if ($family->my_occupation) :?>
							<li><span class="lbl">Occupation:</span> <span><?php echo htmlspecialchars($family->my_occupation);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_religion) :?>
							<li><span class="lbl">Religion:</span> <span><?php echo htmlspecialchars($family->my_religion);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_education) :?>
							<li><span class="lbl">Education:</span> <span><?php echo htmlspecialchars($family->my_education);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_food) :?>
							<li><span class="lbl">Favorite Food:</span> <span><?php echo htmlspecialchars($family->my_food);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_hobby) :?>
							<li><span class="lbl">Favorite Hobby:</span> <span><?php echo htmlspecialchars($family->my_hobby);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_movie) :?>
							<li><span class="lbl">Favorite Movie:</span> <span><?php echo htmlspecialchars($family->my_movie);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_sport) :?>
							<li><span class="lbl">Favorite Sport:</span> <span><?php echo htmlspecialchars($family->my_sport);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_holiday) :?>
							<li><span class="lbl">Favorite Holiday:</span> <span><?php echo htmlspecialchars($family->my_holiday);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_music_group) :?>
							<li><span class="lbl">Favorite Music Group:</span> <span><?php echo htmlspecialchars($family->my_music_group);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_tv_show) :?>
							<li><span class="lbl">Favorite TV Show:</span> <span><?php echo htmlspecialchars($family->my_tv_show);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_book) :?>
							<li><span class="lbl">Favorite Book:</span> <span><?php echo htmlspecialchars($family->my_book);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_subject_in_school) :?>
							<li><span class="lbl">Favorite Subject in School:</span> <span><?php echo htmlspecialchars($family->my_subject_in_school);?></span></li>
						<?php endif; ?>
						<?php if ($family->my_vacation_spot) :?>
							<li><span class="lbl">Favorite Vacation Spot:</span> <span><?php echo htmlspecialchars($family->my_vacation_spot);?></span></li>
						<?php endif; ?>
					</ul>
				</div>
				<?php if(!$is_single) : ?>
				<div class="span6 spouse">
					<h3>Facts About <?php echo $family->spouse_name?></h3>
					<ul class="unstyled">
						<?php if ($family->spouse_occupation) :?>
							<li><span class="lbl">Occupation:</span> <span><?php echo htmlspecialchars($family->spouse_occupation);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_religion) :?>
							<li><span class="lbl">Religion:</span> <span><?php echo htmlspecialchars($family->spouse_religion);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_education) :?>
							<li><span class="lbl">Education:</span> <span><?php echo htmlspecialchars($family->spouse_education);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_food) :?>
							<li><span class="lbl">Favorite Food:</span> <span><?php echo htmlspecialchars($family->spouse_food);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_hobby) :?>
							<li><span class="lbl">Favorite Hobby:</span> <span><?php echo htmlspecialchars($family->spouse_hobby);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_movie) :?>
							<li><span class="lbl">Favorite Movie:</span> <span><?php echo htmlspecialchars($family->spouse_movie);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_sport) :?>
							<li><span class="lbl">Favorite Sport:</span> <span><?php echo htmlspecialchars($family->spouse_sport);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_holiday) :?>
							<li><span class="lbl">Favorite Holiday:</span> <span><?php echo htmlspecialchars($family->spouse_holiday);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_music_group) :?>
							<li><span class="lbl">Favorite Music Group:</span> <span><?php echo htmlspecialchars($family->spouse_music_group);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_tv_show) :?>
							<li><span class="lbl">Favorite TV Show:</span> <span><?php echo htmlspecialchars($family->spouse_tv_show);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_book) :?>
							<li><span class="lbl">Favorite Book:</span> <span><?php echo htmlspecialchars($family->spouse_book);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_subject_in_school) :?>
							<li><span class="lbl">Favorite Subject in School:</span> <span><?php echo htmlspecialchars($family->spouse_subject_in_school);?></span></li>
						<?php endif; ?>
						<?php if ($family->spouse_vacation_spot) :?>
							<li><span class="lbl">Favorite Vacation Spot:</span> <span><?php echo htmlspecialchars($family->spouse_vacation_spot);?></span></li>
						<?php endif; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<br />
		<div id="contact-form" class="section">
			<div class="form-container">
				<h3>Contact <?php echo $fullname; ?></h3>
				<?php
				$app = JFactory::getApplication();
	
				$app->setUserState('family.name', $fullname);
	
				echo EEHelper::getRsForm(31, true);
				?>
			</div>
		</div>
	</div>
</div>
