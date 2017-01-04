<?php
/**
 * Tests the WordPress schema objects.
 *
 * WordPress already has a non machinereadable schema, these tests test the
 * special schema objects for WP GraphQL. Schema objects are used to hold common
 * WordPress schema. i.e. WP_Query arguments.
 *
 * @package Wp_Graphql
 */

use \BEForever\WPGraphQL\Schema\WPQuery;
use \Beforever\WPGraphQL\TypeSystem;

/**
 * WP Schema Test cases.
 */
class WP_GraphQL_Schema_Test extends WP_UnitTestCase {
	/**
	 * This function is run before each method
	 */
	public function setUp() {
		parent::setUp();

		$this->admin = $this->factory->user->create( array(
			'role' => 'admin',
		) );

		$wp_config = \BEForever\WPGraphQL\graphql_build_wp_config();

		// Build the complete type system.
		$this->types = new TypeSystem( $wp_config );
		$this->wp_query_schema = new WPQuery( $this->types );
	}

	/**
	 * Runs after each method.
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Tests the query for hello.
	 */
	public function test_wp_query_args_schema() {
		$expected = array(
			// Author parameters.
			'author' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'Restricts a collection based on author ID.', 'wp-graphql' ),
			),
			'author_name' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Restricts a collection based on author slug. This field matches against the WP_User->user_nicename property.', 'wp-graphql' ),
			),
			'author__in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restricts a collection based on a list of author IDs', 'wp-graphql' ),
			),
			'author__not_in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Removes items from a collection based on a list of author IDs', 'wp-graphql' ),
			),
			// Category parameters.
			'category' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'Restricts a collection based on category ID.', 'wp-graphql' ),
			),
			'category_name' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Restricts a collection based on category slug.', 'wp-graphql' ),
			),
			'category_and' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restricts a collection based on posts that have each category ID.', 'wp-graphql' ),
			),
			'category__in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restricts a collection based on a list of category IDs', 'wp-graphql' ),
			),
			'category__not_in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Removes items from a collection based on a list of category IDs', 'wp-graphql' ),
			),
			// Tag parameters.
			'tag' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'Restricts a collection based on tag ID.', 'wp-graphql' ),
			),
			'tag_and' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restricts a collection based on tag slug.', 'wp-graphql' ),
			),
			'tag__in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restricts a collection based on a list of category IDs', 'wp-graphql' ),
			),
			'tag__not_in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Removes items from a collection based on a list of tag IDs', 'wp-graphql' ),
			),
			'tag_slug__and' => array(
				'type'         => $this->types->listOf( $this->types->string() ),
				'description'  => esc_html__( 'Restricts a collection based on posts that have each tag ID.', 'wp-graphql' ),
			),
			'tag_slug__in' => array(
				'type'         => $this->types->listOf( $this->types->string() ),
				'description'  => esc_html__( 'Restricts a collection based on tag slugs.', 'wp-graphql' ),
			),
			// Taxonomy query parameters. This must be composed of another type.
			// Search parameters.
			's' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Search phrase to use.', 'wp-graphql' ),
			),
			// Post parameters.
			'p' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'Restricts a collection based on post ID.', 'wp-graphql' ),
			),
			'name' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Restricts a collection based on post slug.', 'wp-graphql' ),
			),
			'page_id' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'Restricts a collection based on a page ID', 'wp-graphql' ),
			),
			'page_slug' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Restricts a collection based on a page slug', 'wp-graphql' ),
			),
			'post_parent' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'Restricts items from a collection based on post parent ID', 'wp-graphql' ),
			),
			'post_parent__in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restricts items from a collection based on a list of post parent IDs', 'wp-graphql' ),
			),
			'post_parent__not_in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Removes items from a collection based on a list of post parent IDs', 'wp-graphql' ),
			),
			'post__in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Restrics items from a collection based on a list of post IDs', 'wp-graphql' ),
			),
			'post__not_in' => array(
				'type'         => $this->types->listOf( $this->types->int() ),
				'description'  => esc_html__( 'Removes items from a collection based on a list of post IDs', 'wp-graphql' ),
			),
			'post_name__in' => array(
				'type'         => $this->types->listOf( $this->types->string() ),
				'description'  => esc_html__( 'Restricts items from a collection based on a list of post slugs', 'wp-graphql' ),
			),
			// Post type parameters. This should be changed to a union type at some point for consistency with WP_Query.
			'post_type' => array(
				'type' => $this->types->listOf( $this->types->string() ),
				'description'  => esc_html__( 'Restricts items from a collection based on a list of post types', 'wp-graphql' ),
			),
			// Post type parameters. This should be changed to a union type at some point for consistency with WP_Query.
			'post_status' => array(
				'type'        => $this->types->listOf( $this->types->string() ),
				'description' => esc_html__( 'Restricts items from a collection based on a list of post stati', 'wp-graphql' ),
			),
			// Pagination parameters will be handled by WP GraphQL differently to make collections Relay compliant.
			'first' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'The pagination limit. This is equivalent to posts_per_page for WP_Query.', 'wp-graphql' ),
				'defaultValue' => 10,
			),
			'after' => array(
				'type'         => $this->types->int(),
				'description'  => esc_html__( 'The pagination offset. This is equivalent to offset for WP_Query.', 'wp-graphql' ),
				'defaultValue' => 0,
			),
			'ignore_sticky_posts' => array(
				'type'         => $this->types->boolean(),
				'description'  => esc_html__( 'A boolean flag for whether to ignore sticky posts.', 'wp-graphql' ),
				'defaultValue' => false,
			),
			// Order/orderby params. Currently WP GraphQL will not support multiple orderby params as it will be difficult to support arbitrary key value multiple orderby params.
			'order' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Orders a collection either ascending or descending based on specified orderby.', 'wp-graphql' ),
				'defaultValue' => 'DESC',
			),
			'orderby' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Restricts a collection based on a page slug', 'wp-graphql' ),
				'defaultValue' => 'date',
			),
			// Date parameters not supported yet.
			// Meta query parameters not supported yet.
			// Permissions paramaters not supported yet.
			// Mime type params.
			'post_mime_type' => array(
				'type'         => $this->types->string(),
				'description'  => esc_html__( 'Restricts a collection of attachments based on mime_type', 'wp-graphql' ),
			),
			// Caching parameters are not supported, and probably will not be.
		);
		$actual = $this->wp_query_schema->args();

		$this->assertEquals( $expected, $actual );
	}
}
