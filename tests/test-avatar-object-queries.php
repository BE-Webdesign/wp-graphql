<?php
/**
 * WPGraphQL Test Avatar Object Queries
 * This tests avatar queries, which are only accessible via User edges.
 * @package WPGraphQL
 * @since 0.0.5
 */

class WP_GraphQL_Test_Avatar_Object_Queries extends WP_UnitTestCase {
	public $admin;

	/**
	 * This function is run before each method
	 * @since 0.0.5
	 */
	public function setUp() {
		parent::setUp();

		$this->admin = $this->factory->user->create( [
			'role' => 'admin',
		] );
	}

	/**
	 * Runs after each method.
	 * @since 0.0.5
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * testPostQuery
	 *
	 * This tests creating a single post with data and retrieving said post via a GraphQL query
	 *
	 * @since 0.0.5
	 */
	public function testAvatarQuery() {
		/**
		 * Create the global ID based on the post_type and the created $id
		 */
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'user', $this->admin );

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			user(id: \"{$global_id}\") {
				avatar(size: 48) {
					default,
					extraAttr,
					forceDefault,
					foundAvatar,
					height,
					rating,
					scheme,
					size,
					url,
					width
				}
			}
		}";

		/**
		 * Run the GraphQL query
		 */
		$actual = do_graphql_request( $query );

		/**
		 * Establish the expectation for the output of the query
		 */
		$expected = [
			'data' => [
				'user' => [
					'avatar' => [
						'default'      => 'mm',
						'extraAttr'    => null,
						'forceDefault' => null,
						'foundAvatar'  => null,
						'height'       => 96,
						'rating'       => 'g',
						'scheme'       => null,
						'size'         => 96,
						'url'          => 'http://0.gravatar.com/avatar/cd631944deab1f0589fedb77b3735377?s=96&d=mm&r=g',
						'width'        => 96,
					],
				],
			],
		];

		$this->assertEquals( $expected, $actual );
	}
}
