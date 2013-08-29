<?php

function family_image($path = null, $id = null, $size = '150_150_', $lightbox = true)
{	
	$img = '/uploads/profiles/'.$id.'/'.$size.$path;
	if(true)
	{
		if($lightbox)
		{
			echo '<a href="http://www.angeladoptioninc.com/uploads/profiles/'.$id.'/'.$path.'" class="lightbox"><span></span>';
		}
		echo '<img src="http://www.angeladoptioninc.com'.$img.'" alt="" />';
		if($lightbox)
		{
			echo '</a>';
		}
	}
}

function me_or_us($is_single = null)
{
	if($is_single === null)
	{
		global $is_single;
	}
	return $is_single ? 'Me' : 'Us';
}

function my_or_our($is_single = null)
{
	if($is_single === null)
	{
		global $is_single;
	}
	return $is_single ? 'My' : 'Our';
}

function detect_url($text)
{
	return preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'To learn more about us, <a href=\"$1\" target=\"_blank\">click here</a>.$4'", $text);
}

function format_profile_text($text)
{
	return stripslashes(nl2br(detect_url($text)));
}