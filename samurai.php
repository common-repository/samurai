<?php
/*
Plugin Name: SAMURAI
Plugin URI:  http://wordpress.nnn2.com/?p=369
Description: Extend the function of comments.
Version:     1.0.8
Author:      neginukide
Author URI:  http://wordpress.nnn2.com/?p=369
License:     GPLv2 or later
Text Domain: samurai
Domain Path: /languages/
*/

/* Copyright 2012-2021  neginukide

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!defined('ABSPATH')) exit;	// Exit if accessed directly

add_action( 'plugins_loaded', 'samurai_textdomain' );
function samurai_textdomain() {
    load_plugin_textdomain( 'samurai', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

// Instruction to be executed at the time of activation
function samurai_activation_function() {
	// The activation process
	$db_name = 'samurai';
	$db_get = get_option($db_name);
	if ( ! $db_get) {
		// init wp_option DB
		include('samurai_com.php');
		update_option($db_name, $db_get);
	}
}
// Plugin activation function
register_activation_hook(__FILE__, 'samurai_activation_function');

function samurai_admin() {
	$data = get_file_data( __FILE__ , array( 'version' => 'Version' ) );
	$Version = $data['version'];
	include('samurai_admin.php');
}
function samurai_admin_actions() {
	add_plugins_page('SAMURAI', 'SAMURAI', 8, __FILE__, 'samurai_admin');
}
add_action('admin_menu', 'samurai_admin_actions');

// Add Text Field with Comments Fileds
add_filter('comment_form','samurai_form');
function samurai_form() {
	$db_name = 'samurai';
	$db_get = get_option($db_name);
	if($db_get['SR_DISPLAY'] == '1') {
		//Show comments
		echo '<div class="wrap"><p>';
		$wk2 = $db_get[ 'SR_COMMENT' . $db_get['SR_CHECK'] ];
        echo stripslashes($wk2) ;
		echo '</p></div>';
	}
}
?>