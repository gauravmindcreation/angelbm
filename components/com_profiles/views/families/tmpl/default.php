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

$js = <<<JS

jQuery(document).ready(function($){
	var profileList = $('#profiles-list'),
		isDesktop = ($(window).width() >= 980),
		notDesktop = !isDesktop,
		isPhone = ($(window).width() <= 767),
		notPhone = !isPhone
		isTablet = (notPhone && notDesktop),
		notTable = !isTablet,
		startUrlParam = $.url().param('start'),
		startingPage = 1;

	if (profileList.length > 0) {

		if (typeof startUrlParam != "undefined") {
			startingPage = (parseInt(startUrlParam) / 10) + 1;
		}

		if (notPhone) {
			profileList.imagesLoaded(function(){
				profileList.masonry({
					itemSelector: '.item',
					isAnimated: true,
					appendCallback: false
				});
			});
		}

		profileList.infinitescroll({
			appendCallback: false,
			navSelector: '.pagination',
			nextSelector: '#main-nav [title="Next"]',
			itemSelector: '#profiles-list .item',
			state: {
				currPage: startingPage
			},
			loading: {
				msgText: "<em>Loading the next set of families...</em>",
				finishedMsg: "<em>No more families found!</em>"
			},
			
			path: function (currPage) {
				var start = (currPage - 1) * 10;
				console.log(start);
				return "/waiting-families?start=" + start;
			}
		},
		function (elements, opts) {
			var newElements = $(elements);

			newElements.imagesLoaded(function () {
				setTimeout(function(){
					clearInterval(window.loadingInterval);
					$('#loadMore').html('Load More');

					if (notPhone) {
						profileList.append(newElements)
							.masonry('appended', newElements)
							.masonry();
					} else {
						profileList.append(newElements);
					}
				}, 1000);
			});
		});

		if (isPhone) {
			$(window).unbind('.infscr');

			$('#loadMore').click(function (e) {
				e.preventDefault();
				var base = $(this),
					origText = base.html(),
					i = 0;

				window.loadingInterval = setInterval(function() {
						i = ++i % 4;
						base.html('Loading' + Array(i+1).join('.'));
					}, 150);

				profileList.infinitescroll('retrieve');
				return false;
			});
		}
	}
});

JS;

$css = <<<CSS

#profiles-list .item img {
	width:94%;
	padding:3%;
	background:#fff;
	border-radius:0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
}
#profiles-list h3 a {
	font-weight:normal;
	text-transform: uppercase;
}
#profiles-list .item .item-block {
    background-color: #fdf7fc;
    border: 1px solid #E3E3E3;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
    margin-bottom: 20px;
    min-height: 20px;
    padding: 15px;
}
#profiles-list .text-center > span {
	color:#63174D;
}

CSS;

JHtml::_('bootstrap.framework');
JFactory::getDocument()
	->addScript('/media/com_profiles/js/masonry.min.js')
	->addScript('/media/com_profiles/js/jquery.infinitescroll.min.js')
	->addScript('/media/com_profiles/js/imagesloaded.min.js')
	->addScript('/media/com_profiles/js/purl.js')
	->addScriptDeclaration($js)
	->addStyleDeclaration($css);

if ($this->error) echo '<p class="error">' . $this->error . '</p>'; ?>
<div id="profiles-list" class="row">
<?php
foreach($this->families as $family) :

	$fullname = $family->first_name;
	if($family->spouse_name)
	{
		ProfilesHelper::$single = false;
		$fullname .= ' &amp; '.$family->spouse_name;
	}

	$main_image = $family->about_us_image;
	if (!$main_image) {
		$gallery = $this->getGalleryByFamilyID($family->id);
		$main_image = $gallery->path;
	}
	$link = JRoute::_('index.php?option=com_profiles&view=profile&id='.$family->id);
	?>
	<div class="span4 item">
		<div class="item-block">
			<?php
			echo '<a href="'.$link.'">';
			echo '<img class="thumbnail" src="http://www.angeladoptioninc.com/uploads/profiles/'.$family->id.'/'.$main_image.'" alt="'.$family->last_name.' Family" />';
			echo '</a>';
			?>
			<h3 class="text-center">
				<a href="<?php echo $link; ?>"><?php echo $fullname; ?></a>
			</h3>
			<div class="text-center">
				<span>FAMILY TYPE: <span class="muted"><?php echo ($family->spouse_name ? 'Married' : 'Single Parent');?></span></span>
			</div>
			<br />
			<div class="text-center">
				<a href="<?php echo $link; ?>" class="btn btn-primary">View Profile</a>
			</div>
		</div>
	</div>	
<?php endforeach; ?>
</div>
<div id="main-nav" class="pagination hidden-phone"><?php echo $this->pagination->getPagesLinks(); ?></div>
<div class="clearfix visible-phone" style="margin-bottom:10px"><?php echo $this->pagination->loadMoreBtn(); ?></div>

