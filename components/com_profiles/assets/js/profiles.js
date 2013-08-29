function preloadFullImages(selector)
{
	jQuery('body').append('<div style="display:none" id="preloadFullImages"></div>');
	jQuery(selector).find('img').each(function(){
		var src = jQuery(this).attr('src');
		var src = src.replace(/thumbnail_/, '307_254_');
		jQuery('#preloadFullImages').append('<img src="' + src + '" />');
	});
}
jQuery(document).ready(function($){
	preloadFullImages('#profile-photos .thumbs_container');
	$('a[rel="external"]').each(function(){
		var base = $(this);
		base.attr('target', '_blank');
	});
	$('#profile-photos .thumbs img').click(function() {
		var src = jQuery(this).attr('src');
		var src = src.replace(/thumbnail_/, '307_254_');
		$('#profile-photos .main-photo').fadeOut(200, function() {
			$(this).attr('src', src);
		}).fadeIn(300);
	});
	if( $('#profile-photos .thumbs .thumbs_container').width() > '325' ) {
		$('#profile-photos .prev, #profile-photos .next').fadeIn();
	}
	$('#profile-photos .next').click(function(){
		var base = $(this).siblings('.thumbs_container');
		var width = base.width();
		var oldpos = base.css('left');
		var pos = oldpos.replace(/-/, '');
		var newpos = parseInt(pos) - 82;
		if(newpos >= width) {
		} else {
			base.animate({left: '-' + newpos + 'px'});
		}
	});
	$('#profile-photos .prev').click(function(){
		var base = $(this).siblings('.thumbs_container');
		var width = base.width();
		var oldpos = base.css('left');
		var pos = oldpos;
		var newpos = parseInt(pos) + 82;
		if(newpos > 0) {
		} else {
			base.animate({left: newpos + 'px'});
		}
	});
	$('#user_profile .lightbox').each(function(){
		var base = $(this);
		base.click(function(e){
			e.preventDefault();
		});
		var rand = Math.floor(Math.random()*1001);
		base.attr('rel', 'group-' + rand);
		base.fancybox();
	});
	$("#user_profile").find("a.video").click(function(e) {
		e.preventDefault();
		var base = $(this);
		var type = base.attr('rel');
		if(type === 'youtube')
		{
			var videohref = base.attr('href').replace(new RegExp("watch\\?v=", "i"), 'v/');
		}
		if(type === 'vimeo')
		{
			var videohref = base.attr('href').replace(new RegExp("([0-9])","i"),'moogaloop.swf?clip_id=$1');
		}
		$.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'title'			: this.title,
			'width'			: 640,
			'height'		: 385,
			'href'			: videohref,
			'type'			: 'swf',
			'swf'			: {
				'wmode'				: 'transparent',
				'allowfullscreen'	: 'true'
			}
		});
	});
	$("#user_profile").find("a.contact, a.bottom_contact").click(function(e) {
		e.preventDefault();
		$.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'title'			: this.title,
			'width'			: 750,
			'height'		: 740,
			'href'			: this.href,
			'type'			: 'iframe'
		});
	});
});