jQuery(document).ready(function($){
    var userProfile = $('#user_profile');

	userProfile.find('.lightbox').each(function(){
		var base = $(this);
		base.click(function(e){
			e.preventDefault();
		});
		var rand = Math.floor(Math.random()*1001);
		base.attr('rel', 'group-' + rand);
		base.fancybox();
	});

	userProfile.find("a.video").click(function(e) {
		e.preventDefault();
		var base = $(this),
            type = base.attr('rel'),
            videohref = null;

		if(type === 'youtube') {
			videohref = base.attr('href').replace(new RegExp("watch\\?v=", "i"), 'v/');
		}

		if(type === 'vimeo') {
			videohref = base.attr('href').replace(new RegExp("([0-9])","i"),'moogaloop.swf?clip_id=$1');
		}
		$.fancybox({
			'padding'   : 0,
			'autoScale' : false,
			'title'     : this.title,
			'width'     : 640,
			'height'    : 385,
			'href'      : videohref,
			'type'      : 'swf',
			'swf'       : {
				'wmode'           : 'transparent',
				'allowfullscreen' : 'true'
			}
		});
	});
});