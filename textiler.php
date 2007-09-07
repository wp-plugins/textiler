<?php
/*
Plugin Name: Textiler
Plugin URI: http://ideathinking.com/wiki/index.php/WordPress:Textiler
Description: This plugin supports Textile syntax for posts.
Version: 1.0
Author: Wongoo Lee
Author URI: http://ideathinking.com/

Copyright 2006, 2007  Wongoo Lee  (email : iwongu_at_gmail_dot_com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once('classTextile.php');

function ideathinking_textiler_callback($matches) {
	$textile = new Textile();
	$content = trim($matches[1]);
	return $textile->TextileThis($content);
}

function ideathinking_textiler($content) {
    $pattern = '/{{{((\\n|.)*)}}}/U';
    $content = preg_replace_callback($pattern, 'ideathinking_textiler_callback', $content);
    return $content;
}

remove_filter('the_content', 'wptexturize');
remove_filter('the_content', 'convert_chars');

add_filter('the_content', 'ideathinking_textiler', 5);

?>
