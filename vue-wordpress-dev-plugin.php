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

add_action( 'admin_head', 'wpVueEnqueuePostsScripts' );

function wpVueEnqueuePostsScripts() {
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
	wp_localize_script(
		'wp-vue-script-app',
		'wpVue',
		[
			'posts' => [],
		]
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

add_action( 'admin_init', 'wpVueAddPostColumn', 1 );

function wpVueAddPostColumn() {
	add_filter( 'manage_posts_columns', 'wpVuePostColumn' );
	add_action( 'manage_posts_custom_column', 'wpVueRenderColumn', 10, 2 );
}

function wpVuePostColumn( $columns ) {
	$columns['wp-vue-test'] = 'Test Column';

	return $columns;
}

function wpVueRenderColumn( $columnName, $postId ) {
	$value = '';
	if ( ! current_user_can( 'edit_post', $postId ) ) {
		return;
	}

	$postType = get_post_type( $postId );

	if ( 'wp-vue-test' !== $columnName ) {
		return;
	}

	$value = get_post_meta( $postId, '_wp_vue_test', true );

	// Add this column/post to the localized array.
	global $wp_scripts;
	$data = $wp_scripts->get_data( 'wp-vue-script-app', 'data' );

	if ( ! is_array( $data ) ) {
		$data = json_decode( str_replace( 'var wpVue = ', '', substr( $data, 0, -1 ) ), true );
	}

	$nonce   = wp_create_nonce( "wp_vue_meta_{$postId}" );

	$data['posts'][] = [
		'id'         => $postId,
		'columnName' => $columnName,
		'nonce'      => $nonce,
		'value'      => $value
	];

	$wp_scripts->add_data( 'wp-vue-script-app', 'data', '' );
	wp_localize_script( 'wp-vue-script-app', 'wpVue', $data );

	echo '<div id="<?php echo $columnName; ?>-<?php echo $postId; ?>"></div>';
}

add_action( 'wp_ajax_wp_vue_ajax_save_post_meta', 'wpVueSavePostMeta' );

function wpVueSavePostMeta() {
	if ( ! current_user_can( 'edit_post', $postId ) ) {
		wp_send_json_error();
	}

	$body   = json_decode( file_get_contents( 'php://input' ) );
	$postId = ! empty( $body->postId ) ? intval( $body->postId ) : null;
	$value  = ! empty( $body->value ) ? sanitize_text_field( $body->value ) : null;
	$nonce  = ! empty( $body->_ajax_nonce ) ? $body->_ajax_nonce : null;

	$result = wp_verify_nonce( $nonce, "wp_vue_meta_{$postId}" );
	if ( empty( $result ) ) {
		wp_send_json_error();
	}

	update_post_meta( $postId, '_wp_vue_test', $value );

	wp_send_json_success();
}