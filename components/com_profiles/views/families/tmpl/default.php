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
		notTable = !isTablet;

	if (profileList.length > 0) {

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
			debug:true,
			navSelector: '.pagination',
			nextSelector: '#main-nav [title="Next"]',
			itemSelector: '#profiles-list .item',
			path: function (currPage) {
				var start = (currPage - 1) * 10;
				console.log(start);
				return "/waiting-families?start=" + start;
			},
			loading: {
				msgText: "Loading more families...",
				finishedMsg: "<p>No more families found!</p>"
			}
		},
		function (elements) {
			var newElements = $(elements);

			newElements.imagesLoaded(function () {
				setTimeout(function(){
					clearInterval(window.loadingInterval);
					$('#loadMore').html('Load More');

					if (notPhone) {
						profileList.masonry('appended', newElements);
					}
				}, 1000);
			});
		});

		if (notDesktop) {
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
}
#profiles-list .text-center .icon {
	font-size:26px;
	color:#fc8d21;
	text-decoration: none;
}
#profiles-list h3 {
	background:#dff2f9;
	margin:0;
	padding:12px 0;
	text-indent:10px;
    border: 1px solid #E3E3E3;
    border-bottom:none;
    border-radius: 4px 4px 0 0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
}
#profiles-list h3 span {
	color:#6ccae2;
}
#profiles-list h3 a {
	font-weight:normal;
	color:#63174D;
}
#profiles-list .item .item-block {
    background-color: #FFF;
    border: 1px solid #E3E3E3;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
    margin-bottom: 20px;
    min-height: 20px;
    padding: 10px 19px 19px 19px;
    border-top:none;
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
		<h3><span class="icon icon-heart muted"></span>&nbsp;<a href="<?php echo $link; ?>"><?php echo $fullname; ?></a></h3>
		<div class="item-block">
			<?php
			echo '<a href="'.$link.'">';
			echo '<img class="thumbnail" src="http://www.angeladoptioninc.com/uploads/profiles/'.$family->id.'/'.$main_image.'" alt="'.$family->last_name.' Family" />';
			echo '</a>';
			?>
			<div class="text-center">
				<span>FAMILY TYPE: <span class="muted">Married</span></span><br />
				<span>LOCATION: <span class="muted">Illinois</span></span>
			</div>
			<br />
			<div class="text-center">
				<a href="javascript:void(0)" class="icon icon-facebook-3"></a>
				<a href="javascript:void(0)" class="icon icon-pinterest"></a>
				<a href="javascript:void(0)" class="icon icon-flickr-4"></a>
			</div>
			<br />
			<div class="text-center">
				<a href="<?php echo $link; ?>" class="btn btn-primary">Learn About <?php echo ProfilesHelper::meOrUs(); ?></a>
			</div>
		</div>
	</div>	
<?php endforeach; ?>
</div>
<div id="main-nav" class="pagination visible-desktop"><?php echo $this->pagination->getPagesLinks(); ?></div>
<div class="clearfix hidden-desktop" style="margin-bottom:10px"><?php echo $this->pagination->loadMoreBtn(); ?></div>

