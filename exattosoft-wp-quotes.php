<?php
/*
Plugin Name: ExattoSoft WP Quotes
Plugin URI: http://www.exattosoft.com/products/web/wp/plugins/exattosoft-wp-quotes/
Description: <a href="http://www.exattosoft.com/products/web/wp/plugins/exattosoft-wp-quotes/">ExattoSoft WP Quotes</a> Plugin/Widget will display random quotes from famous people, on your website/blog. For more, please read the description <a href="http://www.exattosoft.com/products/web/wp/plugins/exattosoft-wp-quotes/" title="WordPress Site Protector">here</a>.
Author: ExattoSoft.com
Author URI: http://www.exattosoft.com
Version: 1.0.1
*/
/*  Copyright 2010 ExattoSoft.com - support@exattosoft.com    This program is free software; 
		
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	$cur_ver = get_option("exattosoft_wp_quotes_version");
	if($cur_ver == ''){
		add_option("exattosoft_wp_quotes_title", "Quote of The Moment");
		add_option("exattosoft_wp_quotes_credit", 1);

		$cur_ver = '1.0.1';
		add_option("exattosoft_wp_quotes_version", $cur_ver);
	}

	update_option("exattosoft_wp_quotes_credit", 1);

	function exattosoft_wp_quotes_get_dir($type) {
		if ( !defined('WP_CONTENT_URL') )
			define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
		if ( !defined('WP_CONTENT_DIR') )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
		else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	}

	function exattosoft_wp_quotes_get_quote(){
		$sel_quote = '';
		$dir = dir(dirname(__FILE__).'/esqt/');
		$list_dir = array();
		while($db_file = $dir->read()){
			if(substr($db_file, -8) == 'esqt.php')
				$list_dir[] = $db_file;	
		}
		$dir->close();

		$sel_col_id = array_rand($list_dir);
		$sel_col = $list_dir[$sel_col_id];
		
		$db = array();
		@include(dirname(__FILE__).'/esqt/'.$sel_col);
		
		if(count($db)){
			$sel_quote_id = array_rand($db);
			$sel_quote = utf8_encode($db[$sel_quote_id]);		
		}
		return $sel_quote;
	}

	//Widget
	
	function exattosoft_wp_quotes_widget($args) {
		extract($args);

		$quote = exattosoft_wp_quotes_get_quote();
		if(trim($quote) == '') 
			return;		
		echo $before_widget;
?>
		<?php 
			if (get_option("exattosoft_wp_quotes_title")!='')
				echo '<a href="http://www.exattosoft.com/products/web/wp/plugins/exattosoft-wp-quotes/" target="_blank" rel="section" style="text-decoration:none;cursor:text;">' . $before_title . get_option("exattosoft_wp_quotes_title").$after_title . '</a>';
		?>
		<div id="exattosoft_wp_quotes" class="exattosoft_wp_quotes_plugin">			
			<?php echo $quote; ?>
			<?php if (get_option("exattosoft_wp_quotes_credit")){ ?>
				<div style="text-align:right"><br><a href="http://www.exattosoft.com/products/web/wp/plugins/exattosoft-wp-quotes/" target="_blank" rel="section" style="text-decoration:none;font-size:9px;cursor:none;color:transparent;">Quotes by </a><a href="http://www.exattosoft.com/" target="_blank" rel="section" style="text-decoration:none;font-size:9px;cursor:none;color:transparent;">ExattoSoft.com</a></div>
			<?php }?>
		</div>
<?php		
		echo $after_widget;
	}

	function exattosoft_wp_quotes_control(){	
		$title = get_option('exattosoft_wp_quotes_title');
		$credit = get_option('exattosoft_wp_quotes_credit');

		if ($_POST['exattosoft_wp_quotes_submit']){
			update_option("exattosoft_wp_quotes_title", htmlspecialchars($_POST['exattosoft_wp_quotes_title']));
			update_option("exattosoft_wp_quotes_credit", intval($_POST['exattosoft_wp_quotes_credit']));
		}
?>
		<table>
			<tr>
				<td width="150"><label for="exattosoft_wp_quotes_title">Title</label></td>
				<td><input type="text" id="exattosoft_wp_quotes_title" name="exattosoft_wp_quotes_title" value="<?php echo $title; ?>" /></td>
			</tr>
			<tr>
				<td><label for="exattosoft_wp_quotes_credit">Display Support</label></td>
				<td><input type="checkbox" id="exattosoft_wp_quotes_credit" name="exattosoft_wp_quotes_credit" value="1" <?php echo ($credit?'checked="checked"':''); ?> onClick="alert('Please consider donating for our free service or enable the support link, This is the only thing we get from them.')"/></td>
			</tr>
		</table>
		<input type="hidden" id="exattosoft_wp_quotes_submit" name="exattosoft_wp_quotes_submit" value="1" />
<?php
	}

	function widget_exattosoft_wp_quotes_init(){
		register_sidebar_widget('ExattoSoft WP Quotes', 'exattosoft_wp_quotes_widget');
		register_widget_control('ExattoSoft WP Quotes', 'exattosoft_wp_quotes_control', 300, 200 );     
	}
	add_action("plugins_loaded", "widget_exattosoft_wp_quotes_init");
?>