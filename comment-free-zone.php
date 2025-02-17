<?php
/**
 * A plugin to help you fight procrastination and get things done.
 *
 * @package Disable_Comments
 *
 * Plugin name:       Comment-free Zone
 * Plugin URI:        https://prpl.fyi/comment-free-zone
 * Description:       A plugin to fully disable comments, trackbacks and all related features on your WordPress site.
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Version:           1.0.0
 * Author:            Team Progress Planner
 * Author URI:        https://prpl.fyi/about
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       comment-free-zone
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable comments and trackbacks in post types.
 */
class Comment_Free_Zone {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'disable_comments' ] );
		add_action( 'admin_init', [ $this, 'disable_comments' ] );
	}

	/**
	 * Disable comments and trackbacks in post types.
	 *
	 * @return void
	 */
	public function disable_comments() {
		add_action(
			'admin_menu',
			function () {
				remove_submenu_page( 'options-general.php', 'options-discussion.php' ); // Comments settings.
				remove_menu_page( 'edit-comments.php' ); // Comments page.
			},
			999
		);
		add_action(
			'admin_bar_menu',
			function ( $wp_admin_bar ) {
				$wp_admin_bar->remove_node( 'comments' );
			},
			999
		);

		add_filter( 'manage_pages_columns', [ $this, 'remove_comments_column_from_pages' ] );
		add_filter( 'comments_open', '__return_false', 20 );
		add_filter( 'pings_open', '__return_false', 20 );

		// Disable comment feeds.
		add_action( 'do_feed_rss2', [ $this, 'disable_comment_feeds' ], 1 );
		add_action( 'do_feed_rss', [ $this, 'disable_comment_feeds' ], 1 );
		add_filter( 'feed_links_show_comments_feed', '__return_false' );

		// Disable comments on the frontend.
		add_filter(
			'comments_template',
			function () {
				return __DIR__ . '/templates/blank.php';
			},
			20
		);
		add_filter( 'comments_number', '__return_empty_string' );
		add_filter( 'get_comments_number', '__return_zero' );

		// Unregister the wp-block-comments block.
		$comment_blocks = [
			'comment',
			'comment-author-name',
			'comment-content',
			'comment-date',
			'comment-edit-link',
			'comment-reply-link',
			'comment-template',
			'comments-pagination',
			'comments-pagination-next',
			'comments-pagination-numbers',
			'comments-pagination-previous',
			'comments-title',
			'comments',
			'latest-comments',
			'post-comments-form',
		];

		foreach ( $comment_blocks as $block ) {
			unregister_block_type( 'core/' . $block );
			// Filter the output of the block to be empty.
			add_filter(
				'render_block_core/' . $block,
				'__return_empty_string'
			);
		}

		// Disable outgoing pings.
		add_action(
			'pre_ping',
			function ( &$links ) {
				$links = [];
			}
		);

		// Disable incoming pingbacks.
		add_filter(
			'xmlrpc_methods',
			function ( $methods ) {
				unset( $methods['pingback.ping'] );
				return $methods;
			}
		);

		// Disable support for comments and trackbacks in post types.
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}

	/**
	 * Disable comment feeds.
	 *
	 * @return void
	 */
	public function disable_comment_feeds() {
		if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'comments' ) !== false ) {
			wp_die( esc_html__( 'Comments are disabled.', 'comment-free-zone' ), '', [ 'response' => 403 ] );
		}
	}

	/**
	 * Remove the Comments column from the Pages list table.
	 *
	 * @param string[] $columns The columns of the Pages list table.
	 *
	 * @return string[] The modified columns.
	 */
	public function remove_comments_column_from_pages( $columns ) {
		unset( $columns['comments'] ); // Removes the Comments column.

		return $columns;
	}
}

new Comment_Free_Zone();
