<?php
/**
 * Plugin Name: Vue WordPress Dev
 * Plugin URI:  https://benjaminrojas.net
 * Description: Plugin for integrating vue with WordPress.
 * Author:      Benjamin Rojas
 * Author URI:  https://benjaminrojas.net/
 * Version:     0.0.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'wpVueAddMenu' );

function wpVueAddMenu() {
	$hook = add_menu_page(
		'WordPress Vue Dev',
		'WP Vue Dev',
		'edit_plugins',
		'wpvue',
		'wpVuePage',
		'dashicons-carrot'
	);

	add_action( "load-$hook", 'wpVueHook' );
}

function wpVuePage() {
	echo '<div id="wp-vue-app"></div>';
}

function wpVueHook() {
	wp_enqueue_script(
		'wp-vue-script',
		plugins_url( 'dist/js/app.js', __FILE__ ),
		[], // No dependencies.
		'0.0.1',
		true
	);
}