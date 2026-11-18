<?php
/**
 * Class Comment_Free_Zone_Test
 *
 * @package Comment_Free_Zone
 */

/**
 * Tests that comments, trackbacks and related features are disabled.
 *
 * The plugin is loaded by the test bootstrap and hooks itself into `init`,
 * so by the time these tests run all its filters are in place.
 */
class Comment_Free_Zone_Test extends WP_UnitTestCase {

	/**
	 * A post that has comments and pings explicitly opened.
	 *
	 * @var int
	 */
	private static $post_id;

	/**
	 * Create a post with open comments and pings, and a comment on it.
	 *
	 * @param WP_UnitTest_Factory $factory The factory.
	 *
	 * @return void
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$post_id = $factory->post->create(
			[
				'comment_status' => 'open',
				'ping_status'    => 'open',
			]
		);
		$factory->comment->create( [ 'comment_post_ID' => self::$post_id ] );
	}

	/**
	 * The request URI as it was before the test.
	 *
	 * @var string
	 */
	private $request_uri;

	/**
	 * Remember the request URI, as some tests change it.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->request_uri = $_SERVER['REQUEST_URI']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Restored as is in tear_down.
	}

	/**
	 * Reset the REST server and the request URI between tests.
	 *
	 * @return void
	 */
	public function tear_down() {
		global $wp_rest_server;
		$wp_rest_server = null;

		$_SERVER['REQUEST_URI'] = $this->request_uri;

		parent::tear_down();
	}

	/**
	 * Comments are closed, even on a post that has them open in the database.
	 *
	 * @return void
	 */
	public function test_comments_are_closed() {
		$this->assertSame( 'open', get_post( self::$post_id )->comment_status );
		$this->assertFalse( comments_open( self::$post_id ) );
	}

	/**
	 * Pings are closed, even on a post that has them open in the database.
	 *
	 * @return void
	 */
	public function test_pings_are_closed() {
		$this->assertSame( 'open', get_post( self::$post_id )->ping_status );
		$this->assertFalse( pings_open( self::$post_id ) );
	}

	/**
	 * The default comment and ping status is closed, whatever the options say.
	 *
	 * @return void
	 */
	public function test_default_comment_status_is_closed() {
		update_option( 'default_comment_status', 'open' );
		update_option( 'default_ping_status', 'open' );

		$this->assertSame( 'closed', get_default_comment_status( 'post' ) );
		$this->assertSame( 'closed', get_default_comment_status( 'post', 'pingback' ) );
	}

	/**
	 * No post type supports comments or trackbacks.
	 *
	 * @return void
	 */
	public function test_post_types_do_not_support_comments_or_trackbacks() {
		foreach ( [ 'post', 'page', 'attachment' ] as $post_type ) {
			$this->assertFalse( post_type_supports( $post_type, 'comments' ), "$post_type supports comments" );
			$this->assertFalse( post_type_supports( $post_type, 'trackbacks' ), "$post_type supports trackbacks" );
		}
	}

	/**
	 * The comment count is zero, even when a post has a comment.
	 *
	 * @return void
	 */
	public function test_comments_number_is_zero() {
		$this->assertCount( 1, get_comments( [ 'post_id' => self::$post_id ] ) );
		$this->assertSame( 0, (int) get_comments_number( self::$post_id ) );
	}

	/**
	 * The comments template is replaced with the plugin's blank template.
	 *
	 * @return void
	 */
	public function test_comments_template_is_blank() {
		$template = apply_filters( 'comments_template', '/some/theme/comments.php' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.

		$this->assertSame( dirname( __DIR__, 2 ) . '/templates/blank.php', $template );
		$this->assertFileExists( $template );
	}

	/**
	 * The comments feed link is not shown.
	 *
	 * @return void
	 */
	public function test_comments_feed_link_is_hidden() {
		$this->assertFalse( apply_filters( 'feed_links_show_comments_feed', true ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.
	}

	/**
	 * Requests for the comments feed are refused.
	 *
	 * @return void
	 */
	public function test_comments_feed_is_blocked() {
		$_SERVER['REQUEST_URI'] = '/comments/feed/';

		$this->expectException( WPDieException::class );
		$this->expectExceptionMessage( 'Comments are disabled.' );

		do_action( 'do_feed_rss2', true, 'rss2' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.
	}

	/**
	 * The plugin leaves the regular posts feed alone.
	 *
	 * @return void
	 */
	public function test_posts_feed_is_not_blocked() {
		$_SERVER['REQUEST_URI'] = '/feed/';

		( new Comment_Free_Zone() )->disable_comment_feeds();

		// No WPDieException was thrown.
		$this->addToAssertionCount( 1 );
	}

	/**
	 * The comments REST API endpoints are removed, other endpoints stay.
	 *
	 * @return void
	 */
	public function test_comments_rest_endpoints_are_removed() {
		$routes = rest_get_server()->get_routes();

		$this->assertArrayHasKey( '/wp/v2/posts', $routes );
		$this->assertArrayNotHasKey( '/wp/v2/comments', $routes );
		$this->assertArrayNotHasKey( '/wp/v2/comments/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Requesting the comments REST API endpoint returns a 404.
	 *
	 * @return void
	 */
	public function test_comments_rest_request_returns_404() {
		$response = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/comments' ) );

		$this->assertSame( 404, $response->get_status() );
	}

	/**
	 * The comment and ping status are removed from the REST API post schema.
	 *
	 * @return void
	 */
	public function test_rest_schema_has_no_comment_or_ping_status() {
		$response   = rest_do_request( new WP_REST_Request( 'OPTIONS', '/wp/v2/posts' ) );
		$properties = $response->get_data()['schema']['properties'];

		$this->assertArrayHasKey( 'title', $properties );
		$this->assertArrayNotHasKey( 'comment_status', $properties );
		$this->assertArrayNotHasKey( 'ping_status', $properties );
	}

	/**
	 * REST API responses for a post do not link to its replies.
	 *
	 * @return void
	 */
	public function test_rest_response_has_no_replies_link() {
		$response = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/posts/' . self::$post_id ) );

		$this->assertSame( 200, $response->get_status() );
		$this->assertArrayHasKey( 'self', $response->get_links() );
		$this->assertArrayNotHasKey( 'replies', $response->get_links() );
	}

	/**
	 * The comment blocks are unregistered, other blocks stay.
	 *
	 * @return void
	 */
	public function test_comment_blocks_are_unregistered() {
		$registry = WP_Block_Type_Registry::get_instance();

		$this->assertTrue( $registry->is_registered( 'core/paragraph' ) );
		foreach ( [ 'core/comments', 'core/latest-comments', 'core/post-comments-form', 'core/comment-template' ] as $block ) {
			$this->assertFalse( $registry->is_registered( $block ), "$block is still registered" );
		}
	}

	/**
	 * Comment blocks already in content render as nothing.
	 *
	 * @return void
	 */
	public function test_comment_blocks_render_empty() {
		$this->assertSame( '', trim( do_blocks( '<!-- wp:latest-comments /-->' ) ) );
	}

	/**
	 * Outgoing pings are disabled: the list of links to ping is emptied.
	 *
	 * @return void
	 */
	public function test_outgoing_pings_are_disabled() {
		$links = [ 'https://example.com/a-post/' ];
		$pung  = [];

		do_action_ref_array( 'pre_ping', [ &$links, &$pung, self::$post_id ] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.

		$this->assertSame( [], $links );
	}

	/**
	 * Incoming pingbacks are disabled: the XML-RPC method is removed.
	 *
	 * @return void
	 */
	public function test_incoming_pingbacks_are_disabled() {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.
		$methods = apply_filters(
			'xmlrpc_methods',
			[
				'pingback.ping' => 'this:pingback_ping',
				'demo.sayHello' => 'this:sayHello',
			]
		);

		$this->assertArrayNotHasKey( 'pingback.ping', $methods );
		$this->assertArrayHasKey( 'demo.sayHello', $methods );
	}

	/**
	 * The comments column is removed from the Pages list table.
	 *
	 * @return void
	 */
	public function test_comments_column_is_removed_from_pages() {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.
		$columns = apply_filters(
			'manage_pages_columns',
			[
				'title'    => 'Title',
				'comments' => 'Comments',
			]
		);

		$this->assertSame( [ 'title' => 'Title' ], $columns );
	}
}
