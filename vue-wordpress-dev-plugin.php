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
	aioseo()->helpers->enqueueScript(
		'aioseo-posts-table',
		'js/posts-table.js'
	);
	aioseo()->helpers->enqueueScript(
		'aioseo-posts-table-vendors',
		'js/chunk-posts-table-vendors.js'
	);
	aioseo()->helpers->enqueueScript(
		'aioseo-common',
		'js/chunk-common.js'
	);
	wp_localize_script(
		'aioseo-posts-table',
		'aioseo',
		[
			'restUrl'      => rest_url(),
			'imgUrl'       => AIOSEOP_PLUGIN_IMAGES_URL, // @TODO: Maybe move this directory?
			'posts'        => [],
			'translations' => aioseo()->helpers->getJedLocaleData( 'all-in-one-seo-pack' ),
			'publicPath'   => AIOSEOP_PLUGIN_URL,
			'options'      => aioseo()->options->all(),
			'settings'     => aioseo()->settings->all(),
			'nonce'        => wp_create_nonce( 'wp_rest' )
		]
	);

	// Styles.
	aioseo()->helpers->enqueueStyle(
		'aioseo-common',
		'css/chunk-common.css'
	);
	aioseo()->helpers->enqueueStyle(
		'aioseo-posts-table-style',
		'css/posts-table.css'
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

	$data = $wp_scripts->get_data( 'aioseo-posts-table', 'data' );

	if ( ! is_array( $data ) ) {
		$data = json_decode( str_replace( 'var aioseo = ', '', substr( $data, 0, -1 ) ), true );
	}

	$nonce   = wp_create_nonce( "aioseo_meta_{$columnName}_{$postId}" );
	$posts   = $data['posts'];
	$posts[] = [
		'id'         => $postId,
		'columnName' => $columnName,
		'nonce'      => $nonce,
		'value'      => $value
	];

	$data['posts'] = $posts;

	$wp_scripts->add_data( 'aioseo-posts-table', 'data', '' );
	wp_localize_script( 'aioseo-posts-table', 'aioseo', $data );

	require( AIOSEOP_PLUGIN_DIR . 'app/Views/admin/posts/columns.php' );
}