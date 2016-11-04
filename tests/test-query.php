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

		$query = "{ post(id: {$post_id}) { content, title } }";
		$expected = array(
			'data' => array(
				'post' => array(
					'content' => 'Hi!',
					'title' => 'Hello!',
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
}
