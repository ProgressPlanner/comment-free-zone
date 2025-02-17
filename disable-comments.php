<?php
/**
 * A plugin to help you fight procrastination and get things done.
 *
 * @package Disable_Comments
 *
 * Plugin name:       Disable Comments
 * Plugin URI:        https://prpl.fyi/disable-comments
 * Description:       A plugin to fully disable comments on your WordPress site.
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Version:           1.0.0
 * Author:            Team Progress Planner
 * Author URI:        https://prpl.fyi/about
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       disable-comments
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable comments and trackbacks in post types.
 */
class Disable_Comments {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'disable_comments' ] );
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
		add_filter( 'manage_pages_columns', [ $this, 'remove_comments_column_from_pages' ] );
		add_filter( 'comments_open', '__return_false', 20 );
		add_filter( 'pings_open', '__return_false', 20 );

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

new Disable_Comments();
