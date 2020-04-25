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
	echo '<div id="app">Hello Everyone!</div>';
}

function wpVueHook() {
	$connection = @fsockopen( 'localhost', '8080' );

	// Scripts.
	wp_enqueue_script(
		'wp-vue-script-app',
		$connection
			? 'http://localhost:8080/js/app.js'
			: plugins_url( 'dist/js/app.js', __FILE__ ),
		[], // No dependencies.
		'0.0.1',
		false
	);
	wp_enqueue_script(
		'wp-vue-script-about',
		$connection
			? 'http://localhost:8080/js/about.js'
			: plugins_url( 'dist/js/about.js', __FILE__ ),
		[], // No dependencies.
		'0.0.1',
		false
	);
	wp_enqueue_script(
		'wp-vue-script-chunk-vendors',
		$connection
			? 'http://localhost:8080/js/chunk-vendors.js'
			: plugins_url( 'dist/js/chunk-vendors.js', __FILE__ ),
		[], // No dependencies.
		'0.0.1',
		false
	);

	// Styles.
	wp_enqueue_style(
		'wp-vue-style',
		$connection
			? 'http://localhost:8080/css/app.css'
			: plugins_url( 'dist/css/app.css', __FILE__ ),
		[], // No dependencies.
		'0.0.1',
		'all'
	);
}