<?php
/*
Plugin Name: Textiler
Plugin URI: http://ideathinking.com/wiki/index.php/WordPress:Textiler
Description: This plugin supports Textile syntax for posts.
Version: 1.1
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

define('TEXTILER_NAME', 'ideathinking_textiler');
define('TEXTILER_DESC', __('ideathinking_textiler_configuration_data'));

function ideathinking_textiler_getdefaultdata() {
	$applyopt = array(
		'it_post'	=>'0',
		'it_comment'=>'0'
	);
	return $applyopt;
}

function ideathinking_textiler_getdata() {
	$applyopt = get_option(TEXTILER_NAME);
	if ($applyopt == null) {
		$applyopt = ideathinking_textiler_getdefaultdata();
	}
	return $applyopt;
}

function ideathinking_textiler_callback($matches) {
	$textile = new Textile();
	$content = trim($matches[1]);
	return $textile->TextileThis($content);
}

function ideathinking_textiler($content, $opt) {
	if ($opt == '0') {
		return $content;
	} elseif ($opt == '2') {
		$textile = new Textile();
		$content = trim($content);
		return $textile->TextileThis($content);
	}

    $pattern = '/{{{((\\n|.)*)}}}/U';
    $content = preg_replace_callback($pattern, 'ideathinking_textiler_callback', $content);
    return $content;
}
function ideathinking_textiler_post($content) {
	$applyopt = ideathinking_textiler_getdata();
	return ideathinking_textiler($content, $applyopt['it_post']);
}

function ideathinking_textiler_comment($content) {
	$applyopt = ideathinking_textiler_getdata();
	return ideathinking_textiler($content, $applyopt['it_comment']);
}

remove_filter('the_content', 'convert_chars');
add_filter('the_content', 'ideathinking_textiler_post', 5);

remove_filter('comment_text', 'convert_chars');
add_filter('comment_text', 'ideathinking_textiler_comment', 5);


function ideathinking_textiler_config_page() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('plugins.php', 'Textiler', 'Textiler', 8, basename(__FILE__), 'ideathinking_textiler_subpanel');
	}
}
 
add_action('admin_menu', 'ideathinking_textiler_config_page');

function ideathinking_textiler_subpanel() {
	$isdefault = false;
	$applyopt = get_option(TEXTILER_NAME);
	if ($applyopt == null) {
		$applyopt = ideathinking_textiler_getdefaultdata();
		$isdefault = true;
	}
	
	$updated = false;
	if (isset($_POST['submit'])) {
		$applyopt['it_post'] = $_POST['it_post'];
		$applyopt['it_comment'] = $_POST['it_comment'];
		if ($isdefault == true) {
			add_option(TEXTILER_NAME, $applyopt, TEXTILER_DESC);			
			$isdefault = false;
		} else {
			update_option(TEXTILER_NAME, $applyopt);
		}
		
		if (isset($_POST['reset2default'])) {
			delete_option(TEXTILER_NAME);
			$applyopt = ideathinking_textiler_getdefaultdata();
			$isdefault = true;
		}
		
		$updated = true;
	}	

	if ($updated) {
		echo "<div id='message' class='updated fade'><p>";
		_e('Configuration updated.');
		echo "</p></div>";
	}
?>

<div class="wrap">    
<h2><?php _e('Textiler Configurations'); ?></h2>

<form action="" id="textilerform" method="post">
	<input type="hidden" name="submit" />
	<h3><?php _e('Textiler is applied to... (posts)'); ?></h3>
	<p>
		<input id="it_post_none" type="radio" name="it_post" value="0" <?php if ($applyopt['it_post'] == '0') echo 'checked="checked"';?> />
		<label for="it_post_none" accesskey="9" ><?php _e('None'); ?></label>
	</p>
	<p>
		<input id="it_post_between" type="radio" name="it_post" value="1" <?php if ($applyopt['it_post'] == '1') echo 'checked="checked"';?> />
		<label for="it_post_between" accesskey="9" ><?php _e('Between {{{ and }}}'); ?></label>
	</p>
	<p>
		<input id="it_post_all" type="radio" name="it_post" value="2" <?php if ($applyopt['it_post'] == '2') echo 'checked="checked"';?> />
		<label for="it_post_all" accesskey="9" ><?php _e('All posts'); ?></label>
	</p>

	<h3><?php _e('Textiler is applied to... (comments)'); ?></h3>
	<p>
		<input id="it_comment_none" type="radio" name="it_comment" value="0" <?php if ($applyopt['it_comment'] == '0') echo 'checked="checked"';?> />
		<label for="it_comment_none" accesskey="9" ><?php _e('None'); ?></label>
	</p>
	<p>
		<input id="it_comment_between" type="radio" name="it_comment" value="1" <?php if ($applyopt['it_comment'] == '1') echo 'checked="checked"';?> />
		<label for="it_comment_between" accesskey="9" ><?php _e('Between {{{ and }}}'); ?></label>
	</p>
	<p>
		<input id="it_comment_all" type="radio" name="it_comment" value="2" <?php if ($applyopt['it_comment'] == '2') echo 'checked="checked"';?> />
		<label for="it_comment_all" accesskey="9" ><?php _e('All comments'); ?></label>
	</p>
	<p>
		<input type="submit" value="<?php _e('Update &raquo;'); ?>" tabindex="4" />
	</p>
</form>

</div>

<?php
}

?>
