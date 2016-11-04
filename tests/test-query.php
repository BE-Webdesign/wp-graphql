<?php
/**
 * Class SampleTest
 *
 * @package Wp_Graphql
 */

use \BEForever\WPGraphQL;
use \BEForever\WPGraphQL\TypeSystem;
use \BEForever\WPGraphQL\AppContext;
use \BEForever\WPGraphQL\Data\DataSource;
use \GraphQL\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Type\Definition\Config;
use \GraphQL\Error\FormattedError;

/**
 * Sample test case.
 */
class Query_Test extends WP_UnitTestCase {

	/**
	 * Tests the query for hello.
	 */
	function test_hello_query() {
		$query = '{hello}';
		$expected = array(
			'data' => array(
				'hello' => 'Welcome to WP GraphQL, I hope that you will enjoy this adventure!',
			),
		);

		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		$data = array();
		$data['query'] = $query;
		$data['variables'] = null;

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		$this->assertEquals( $result, $expected );
	}

	/**
	 * Tests the query for post.
	 */
	function test_post_query() {
		$post_args = array(
			'post_status' => 'publish',
			'post_content' => 'Hi!',
			'post_title' => 'Hello!',
		);

		$post_id = $this->factory->post->create( $post_args );

		$query = "{ post(id: {$post_id}) { content, title, author } }";
		$expected = array(
			'data' => array(
				'post' => array(
					'content' => 'Hi!',
					'title' => 'Hello!',
					'author' => '0',
				),
			),
		);

		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		$data = array();
		$data['query'] = $query;
		$data['variables'] = null;

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		$this->assertEquals( $result, $expected );
	}

	/**
	 * Tests the query for post.
	 */
	function test_post_introspection_fields() {
		$query = '{__type(name: "Post") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'author' ),
						array( 'name' => 'date' ),
						array( 'name' => 'date_gmt' ),
						array( 'name' => 'content' ),
						array( 'name' => 'title' ),
						array( 'name' => 'excerpt' ),
						array( 'name' => 'post_status' ),
						array( 'name' => 'comment_status' ),
						array( 'name' => 'ping_status' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'to_ping' ),
						array( 'name' => 'pinged' ),
						array( 'name' => 'modified' ),
						array( 'name' => 'modified_gmt' ),
						array( 'name' => 'parent' ),
						array( 'name' => 'guid' ),
						array( 'name' => 'menu_order' ),
						array( 'name' => 'type' ),
						array( 'name' => 'mime_type' ),
						array( 'name' => 'comment_count' ),
					),
				),
			),
		);
	}

	/**
	 * Tests the query for post.
	 */
	function test_comment_query() {
		$comment_args = array(
			'comment_approved' => '1',
			'comment_content' => 'Hi!',
		);

		$comment_id = $this->factory->comment->create( $comment_args );

		$query = "{ comment(id: {$comment_id}) { content, approved } }";
		$expected = array(
			'data' => array(
				'comment' => array(
					'content' => 'Hi!',
					'approved' => '1',
				),
			),
		);

		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		$data = array();
		$data['query'] = $query;
		$data['variables'] = null;

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		$this->assertEquals( $result, $expected );
	}

	/**
	 * Tests the fields schema for comments.
	 */
	function test_comment_introspection_fields() {
		$query = '{__type(name: "Comment") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'post' ),
						array( 'name' => 'author' ),
						array( 'name' => 'author_ip' ),
						array( 'name' => 'date' ),
						array( 'name' => 'date_gmt' ),
						array( 'name' => 'content' ),
						array( 'name' => 'karma' ),
						array( 'name' => 'approved' ),
						array( 'name' => 'agent' ),
						array( 'name' => 'type' ),
						array( 'name' => 'parent' ),
						array( 'name' => 'user_id' ),
					),
				),
			),
		);
	}

	/**
	 * Tests the query for post.
	 */
	function test_user_query() {
		$user_args = array(
			'role'       => 'editor',
			'user_email' => 'graphqliscool@withwp.luv',
		);

		$user_id = $this->factory->user->create( $user_args );

		$query = "{ user(id: {$user_id}) { email } }";
		$expected = array(
			'data' => array(
				'user' => array(
					'email' => 'graphqliscool@withwp.luv',
				),
			),
		);

		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		$data = array();
		$data['query'] = $query;
		$data['variables'] = null;

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		$this->assertEquals( $result, $expected );
	}

	/**
	 * Tests the query for post.
	 */
	function test_user_introspection_fields() {
		$query = '{__type(name: "User") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'capabilities' ),
						array( 'name' => 'cap_key' ),
						array( 'name' => 'roles' ),
						array( 'name' => 'extra_capabilities' ),
						array( 'name' => 'email' ),
						array( 'name' => 'first_name' ),
						array( 'name' => 'last_name' ),
						array( 'name' => 'description' ),
						array( 'name' => 'username' ),
						array( 'name' => 'name' ),
						array( 'name' => 'registered_date' ),
						array( 'name' => 'nickname' ),
						array( 'name' => 'url' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'locale' ),
					),
				),
			),
		);

		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		$data = array();
		$data['query'] = $query;
		$data['variables'] = null;

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		$this->assertEquals( $result, $expected );
	}
}
