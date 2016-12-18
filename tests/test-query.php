<?php
/**
 * Class SampleTest
 *
 * @package Wp_Graphql
 */

use BEForever\WPGraphQL;
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
	 * This function is run before each method
	 */
	public function setUp() {
		parent::setUp();

		$this->admin = $this->factory->user->create( array(
			'role' => 'admin',
		) );
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
	public function test_hello_query() {
		$query = '{hello}';
		$expected = array(
			'data' => array(
				'hello' => 'Welcome to WP GraphQL, I hope that you will enjoy this adventure!',
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the dynamic generation of post types.
	 *
	 * This test type is dynamically generated.
	 */
	public function test_page_query() {
		$page_args = array(
			'post_status'  => 'publish',
			'post_content' => 'Hi!',
			'post_title'   => 'Hello!',
			'post_type'    => 'page',
			'post_author'  => $this->admin,
		);

		$page_id = $this->factory->post->create( $page_args );

		$query = "{ page(id: {$page_id}) { content, title, author{ id } } }";
		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'page' => array(
					'content' => 'Hi!',
					'title'   => 'Hello!',
					'author'  => array(
						'id'  => $this->admin,
					),
				),
			),
		);

		$this->assertEquals( $expected, $actual );

		// Test for null if the post types do not match.
		$post_args = array(
			'post_status'  => 'publish',
			'post_content' => 'Hi!',
			'post_title'   => 'Hello!',
			'post_status'  => 'post',
			'post_author'  => $this->admin,
		);

		$post_id = $this->factory->post->create( $post_args );

		$query = "{ page(id: {$post_id}) { content, title, author{ id } } }";
		$actual = $this->get_graphql_response( $query );

		$expected = array(
			'data' => array(
				'page' => null,
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for post.
	 */
	public function test_post_query() {
		$post_args = array(
			'post_status' => 'publish',
			'post_content' => 'Hi!',
			'post_title' => 'Hello!',
			'post_author' => $this->admin,
		);

		$post_id = $this->factory->post->create( $post_args );

		$query = "{ post(id: {$post_id}) { content, title, author{ id } } }";
		$expected = array(
			'data' => array(
				'post' => array(
					'content' => 'Hi!',
					'title' => 'Hello!',
					'author' => array(
						'id' => $this->admin,
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for post comments.
	 */
	public function test_post_comments() {
		$post_args = array(
			'post_status'  => 'publish',
			'post_content' => 'Hi!',
			'post_title'   => 'Hello!',
			'post_author'  => $this->admin,
		);

		$post_id = $this->factory->post->create( $post_args );

		$comment_args = array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Hi Testing!',
			'comment_approved' => '1',
		);

		$comment_id = $this->factory->comment->create( $comment_args );

		$query = "{ post(id: {$post_id}) { title, comments { content } } }";
		$expected = array(
			'data' => array(
				'post' => array(
					'title' => 'Hello!',
					'comments' => array(
						array(
							'content' => 'Hi Testing!',
						),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for post fields.
	 */
	public function test_post_introspection_fields() {
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
						array( 'name' => 'comments' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for comment.
	 */
	public function test_comment_query() {
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

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for comment's post.
	 */
	public function test_comment_post_query() {
		$post_args = array(
			'post_title' => 'toaster',
		);

		$post_id = $this->factory->post->create( $post_args );

		$comment_args = array(
			'comment_approved' => '1',
			'comment_content'  => 'Hi!',
			'comment_post_ID'  => $post_id,
		);

		$comment_id = $this->factory->comment->create( $comment_args );

		$query = "{ comment(id: {$comment_id}) { post { title } } }";

		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'comment' => array(
					'post' => array(
						'title' => 'toaster',
					),
				),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for comment.
	 */
	public function test_comment_parent_query() {
		$comment_args = array(
			'comment_approved' => '1',
			'comment_content' => 'Hi!',
		);

		$comment_parent = $this->factory->comment->create( $comment_args );

		$comment_args = array(
			'comment_parent' => $comment_parent,
			'comment_content' => 'Hello!',
		);

		$comment_child = $this->factory->comment->create( $comment_args );

		$query = "{ comment(id: {$comment_child}) { content, parent { content } } }";
		$expected = array(
			'data' => array(
				'comment' => array(
					'content' => 'Hello!',
					'parent' => array(
						'content' => 'Hi!',
					),
				),
			),
		);

		// Test for no parent.
		$this->check_graphql_response( $query, $expected );

		$query = "{ comment(id: {$comment_parent}) { content, parent { content } } }";
		$expected = array(
			'data' => array(
				'comment' => array(
					'content' => 'Hi!',
					'parent' => null,
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for comment.
	 */
	public function test_comment_children_query() {
		$comment_args = array(
			'comment_approved' => '1',
			'comment_content' => 'Hi!',
		);

		$comment_parent = $this->factory->comment->create( $comment_args );

		$comment_args = array(
			'comment_parent' => $comment_parent,
			'comment_content' => 'Hello!',
		);

		$comment_child = $this->factory->comment->create( $comment_args );

		// Test for empty children.
		$query = "{ comment(id: {$comment_child}) { content, children { content } } }";
		$expected = array(
			'data' => array(
				'comment' => array(
					'content' => 'Hello!',
					'children' => null,
				),
			),
		);

		// Test for one child comment.
		$this->check_graphql_response( $query, $expected );

		$query = "{ comment(id: {$comment_parent}) { content, children { content } } }";
		$expected = array(
			'data' => array(
				'comment' => array(
					'content' => 'Hi!',
					'children' => array(
						array(
							'content' => 'Hello!',
						),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the fields schema for comments.
	 */
	public function test_comment_introspection_fields() {
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
						array( 'name' => 'children' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for user.
	 */
	public function test_user_query() {
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

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for user.
	 */
	public function test_user_avatar_query() {
		$user_args = array(
			'role'       => 'editor',
			'user_email' => 'graphqliscool@withwp.luv',
		);

		$user_id = $this->factory->user->create( $user_args );

		$query = "{ user(id: {$user_id}) { avatar(size:96){ found_avatar } } }";
		$expected = array(
			'data' => array(
				'user' => array(
					'avatar' => array(
						'found_avatar' => true,
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the posts query for user.
	 */
	public function test_user_posts_query() {
		$user_args = array(
			'role'       => 'editor',
			'user_email' => 'graphqliscool@withwp.luv',
		);

		$user_id = $this->factory->user->create( $user_args );

		$post_args = array(
			'post_author' => $user_id,
			'post_title'  => 'I love tacos.'
		);

		$post_id = $this->factory->post->create( $post_args );

		$query = "{ user(id: {$user_id}) { email, posts(first: 10) { title } } }";
		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'user' => array(
					'email' => 'graphqliscool@withwp.luv',
					'posts' => array(
						array(
							'title' => 'I love tacos.',
						),
					),
				),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for user fields.
	 */
	public function test_user_introspection_fields() {
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
						array( 'name' => 'avatar' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for term.
	 */
	public function test_term_query() {
		$term_args = array(
			'taxonomy' => 'category',
			'name'     => 'Test',
		);

		$term_id = $this->factory->term->create( $term_args );

		$query = "{ term(id: {$term_id}) { taxonomy, name } }";
		$expected = array(
			'data' => array(
				'term' => array(
					'taxonomy' => 'category',
					'name'     => 'Test',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for term.
	 */
	public function test_term_children_query() {
		$term_args = array(
			'taxonomy' => 'category',
			'name'     => 'Test',
		);

		$term_id = $this->factory->term->create( $term_args );

		$child_args = array(
			'taxonomy' => 'category',
			'name'     => 'Child',
			'parent'   => $term_id,
		);

		$child_id = $this->factory->term->create( $child_args );

		$query = "{ term(id: {$term_id}) { children { name } } }";

		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'term' => array(
					'children' => array(
						array(
							'name' => 'Child',
						),
					),
				),
			),
		);

		$this->assertEquals( $expected, $actual );

		// Test empty children.
		$query = "{ term(id: {$child_id}) { children { name } } }";

		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'term' => array(
					'children' => array(),
				),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for term.
	 */
	public function test_term_parent_query() {
		$term_args = array(
			'taxonomy' => 'category',
			'name'     => 'Test',
		);

		$term_id = $this->factory->term->create( $term_args );

		$child_args = array(
			'taxonomy' => 'category',
			'name'     => 'Child',
			'parent'   => $term_id,
		);

		$child_id = $this->factory->term->create( $child_args );

		$query = "{ term(id: {$child_id}) { name, parent { name } } }";

		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'term' => array(
					'name' => 'Child',
					'parent' => array(
						'name' => 'Test',
					),
				),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for term.
	 */
	public function test_term_objects_query() {
		$term_args = array(
			'taxonomy' => 'category',
			'name'     => 'Test',
		);

		$term_id = $this->factory->term->create( $term_args );

		$object_1_args = array(
			'post_title'     => 'Object 1',
		);

		$object_1 = $this->factory->post->create( $object_1_args );

		$object_2_args = array(
			'post_title'     => 'Object 2',
		);

		$object_2 = $this->factory->post->create( $object_2_args );

		$query = "{ term(id: {$term_id}) { name, objects { title } } }";

		wp_set_object_terms( $object_1, $term_id, 'category' );
		wp_set_object_terms( $object_2, $term_id, 'category' );

		$actual = $this->get_graphql_response( $query );
		$expected = array(
			'data' => array(
				'term' => array(
					'name' => 'Test',
					'objects' => array(
						array(
							'title' => 'Object 1',
						),
						array(
							'title' => 'Object 2',
						),
					),
				),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for term fields.
	 */
	public function test_term_introspection_fields() {
		$query = '{__type(name: "Term") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'group' ),
						array( 'name' => 'taxonomy_id' ),
						array( 'name' => 'taxonomy' ),
						array( 'name' => 'description' ),
						array( 'name' => 'parent' ),
						array( 'name' => 'count' ),
						array( 'name' => 'children' ),
						array( 'name' => 'objects' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu item.
	 */
	public function test_menu_item_query() {
		$menu_item_args = array(
			'post_type' => 'nav_menu_item',
			'post_title' => 'Let\'s hope this works!',
		);

		$post_args = array(
			'post_title' => 'Let\'s hope this works!',
		);

		// Nav menu items for whatever reason are posts.
		$menu_item_id = $this->factory->post->create( $menu_item_args );

		$post_id = $this->factory->post->create( $post_args );

		// Match the nav menu Item to a post.
		update_post_meta( $menu_item_id, '_menu_item_object_id', $post_id );

		$query = "{ menu_item(id: {$menu_item_id}) { title, object_id } }";
		$expected = array(
			'data' => array(
				'menu_item' => array(
					'title' => 'Let\'s hope this works!',
					'object_id' => "{$post_id}",
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu item fields.
	 */
	public function test_menu_item_introspection_fields() {
		$query = '{__type(name: "MenuItem") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'title' ),
						array( 'name' => 'type' ),
						array( 'name' => 'object_id' ),
						array( 'name' => 'object' ),
						array( 'name' => 'target' ),
						array( 'name' => 'xfn' ),
						array( 'name' => 'url' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu.
	 */
	public function test_menu_query() {
		$menu_args = array(
			'taxonomy' => 'nav_menu',
			'name' => 'Test Menu',
			'slug' => 'test-menu',
		);

		// Nav menu items for whatever reason are posts.
		$menu_id = $this->factory->term->create( $menu_args );

		$query = "{ menu(id: {$menu_id}) { id, name, slug, group } }";
		$expected = array(
			'data' => array(
				'menu' => array(
					'id' => "{$menu_id}",
					'name' => 'Test Menu',
					'slug' => 'test-menu',
					'group' => '0',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu's items field.
	 */
	public function test_items_field_of_menu_query() {
		$menu_args = array(
			'taxonomy' => 'nav_menu',
			'name' => 'Test Menu',
			'slug' => 'test-menu',
		);

		// Nav menu items for whatever reason are posts.
		$menu_id = $this->factory->term->create( $menu_args );

		$menu_item_args = array(
			'post_type' => 'nav_menu_item',
			'post_title' => 'Let\'s hope this works!',
		);

		$menu_item = $this->factory->post->create( $menu_item_args );

		wp_set_object_terms( $menu_item, $menu_id, 'nav_menu' );

		$query = "{ menu(id: {$menu_id}) { items { id, title } } }";
		$expected = array(
			'data' => array(
				'menu' => array(
					'items' => array(
						array(
							'id'    => $menu_item,
							'title' => 'Let\'s hope this works!',
						),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu fields.
	 */
	public function test_menu_introspection_fields() {
		$query = '{__type(name: "Menu") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'group' ),
						array( 'name' => 'items' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu location.
	 */
	public function test_menu_location_query() {
		global $_wp_registered_nav_menus;
		// @codingStandardsIgnoreStart
		$_wp_registered_nav_menus = array( 'top', 'Top' );
		// @codingStandardsIgnoreEnd

		$registered_menus = get_registered_nav_menus();
		$slug = key( $registered_menus );
		$name = current( $registered_menus );

		$query = "{ menu_location(slug: \"{$slug}\") { name, slug } }";
		$expected = array(
			'data' => array(
				'menu_location' => array(
					'name' => $name,
					'slug' => $slug,
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu location.
	 */
	public function test_menu_locations_query() {
		global $_wp_registered_nav_menus;
		// @codingStandardsIgnoreStart
		$_wp_registered_nav_menus = array( 'top', 'Top' );
		// @codingStandardsIgnoreEnd

		$registered_menus = get_registered_nav_menus();
		$menus = array();

		foreach ( $registered_menus as $slug => $name ) {
			$menus[] = array(
				'slug' => $slug,
				'name' => $name,
			);
		}

		$query = '{ menu_locations{ name, slug } }';
		$expected = array(
			'data' => array(
				'menu_locations' => $menus,
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu location fields.
	 */
	public function test_menu_location_introspection_fields() {
		$query = '{__type(name: "MenuLocation") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'active_menu' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for theme.
	 */
	public function test_theme_query() {
		$slug = 'twentyfifteen';
		$theme = wp_get_theme( $slug );

		$query = "{ theme(slug: \"{$slug}\") { name, author, slug } }";
		$expected = array(
			'data' => array(
				'theme' => array(
					'name'   => $theme->get( 'Name' ),
					'author' => $theme->get( 'Author' ),
					'slug'   => $theme->get_stylesheet(),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for theme fields.
	 */
	public function test_theme_introspection_fields() {
		$query = '{__type(name: "Theme") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'slug' ),
						array( 'name' => 'name' ),
						array( 'name' => 'screenshot' ),
						array( 'name' => 'theme_uri' ),
						array( 'name' => 'description' ),
						array( 'name' => 'author' ),
						array( 'name' => 'author_uri' ),
						array( 'name' => 'tags' ),
						array( 'name' => 'version' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for plugin.
	 */
	public function test_plugin_query() {
		$slug = 'Akismet';
		$plugin = $this->get_plugin( $slug );

		$query = "{ plugin(slug: \"{$slug}\") { name, author, description } }";
		$expected = array(
			'data' => array(
				'plugin' => array(
					'name'   => $plugin['Name'],
					'author' => $plugin['Author'],
					'description' => $plugin['Description'],
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for plugin fields.
	 */
	public function test_plugin_introspection_fields() {
		$query = '{__type(name: "Plugin") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'name' ),
						array( 'name' => 'plugin_uri' ),
						array( 'name' => 'description' ),
						array( 'name' => 'author' ),
						array( 'name' => 'author_uri' ),
						array( 'name' => 'version' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for taxonomy.
	 */
	public function test_taxonomy_query() {
		$name = 'category';
		$taxonomy = get_taxonomy( $name );

		$query = "{ taxonomy(name: \"{$name}\") { name, slug } }";
		$expected = array(
			'data' => array(
				'taxonomy' => array(
					'name' => 'Categories',
					'slug' => 'category',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for taxonomy fields.
	 */
	public function test_taxonomy_introspection_fields() {
		$query = '{__type(name: "Taxonomy") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'description' ),
						array( 'name' => 'show_cloud' ),
						array( 'name' => 'hierarchical' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for post types.
	 */
	public function test_post_types_query() {
		$query = '{ post_types { name } }';
		$response = $this->get_graphql_response( $query );

		$expected = array(
			'name' => 'post',
		);
		$actual = $response['data']['post_types'][0];

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for post type.
	 */
	public function test_post_type_query() {
		$name = 'post';

		$query = "{ post_type(name: \"{$name}\") { name } }";
		$expected = array(
			'data' => array(
				'post_type' => array(
					'name' => 'post',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for post type fields.
	 */
	public function test_post_type_introspection_fields() {
		$query = '{__type(name: "PostType") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'name' ),
						array( 'name' => 'label' ),
						array( 'name' => 'description' ),
						array( 'name' => 'public' ),
						array( 'name' => 'hierarchical' ),
						array( 'name' => 'exclude_from_search' ),
						array( 'name' => 'publicly_queryable' ),
						array( 'name' => 'show_ui' ),
						array( 'name' => 'show_in_menu' ),
						array( 'name' => 'show_in_nav_menus' ),
						array( 'name' => 'show_in_admin_bar' ),
						array( 'name' => 'menu_position' ),
						array( 'name' => 'menu_icon' ),
						array( 'name' => 'taxonomies' ),
						array( 'name' => 'has_archive' ),
						array( 'name' => 'can_export' ),
						array( 'name' => 'delete_with_user' ),
						array( 'name' => 'show_in_rest' ),
						array( 'name' => 'rest_base' ),
						array( 'name' => 'rest_controller_class' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for users.
	 */
	public function test_users_query() {
		$query = '{ users(first: 1) { id } }';
		$expected = array(
			'data' => array(
				'users' => array(
					array(
						'id' => 1,
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for posts.
	 */
	public function test_graphql_filter_post_types() {
		$post_type = new stdClass();
		$post_type->show_in_graphql = true;

		$expected = true;
		$actual = \BEForever\WPGraphQL\graphql_filter_post_types( $post_type );

		$this->assertEquals( $expected, $actual );

		// Test for failure.
		$post_type->show_in_graphql = false;

		$expected = false;
		$actual = \BEForever\WPGraphQL\graphql_filter_post_types( $post_type );

		$this->assertEquals( $expected, $actual );

		// Test for bizarre failure.
		$post_type->show_in_graphql = 'taco salad';

		$expected = false;
		$actual = \BEForever\WPGraphQL\graphql_filter_post_types( $post_type );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the fettching of filtered post types.
	 */
	public function test_graphql_get_post_types() {
		$post_types = \BEForever\WPGraphQL\graphql_get_post_types();

		global $wp_post_types;

		$this->assertEquals( $post_types['post'], $wp_post_types['post'] );
		$this->assertEquals( $post_types['page'], $wp_post_types['page'] );

		$expected = array(
			'post',
			'page',
		);
		$actual = array_keys( $post_types );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for posts.
	 */
	public function test_graphql_build_post_type() {
		$post_types = \BEForever\WPGraphQL\graphql_get_post_types();

		$post = $post_types['post'];

		$expected = array(
			'name'            => 'post',
			'singular_type'   => 'Post',
			'plural_type'     => 'Posts',
			'plural_name'     => 'posts',
			'registered_name' => 'post',
		);
		$actual   = \BEForever\WPGraphQL\graphql_build_post_type( $post );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for posts.
	 */
	public function test_graphql_build_post_types() {
		$post_types = \BEForever\WPGraphQL\graphql_get_post_types();

		$expected = array(
			'post' => array(
				'name'            => 'post',
				'singular_type'   => 'Post',
				'plural_type'     => 'Posts',
				'plural_name'     => 'posts',
				'registered_name' => 'post',
			),
			'page' => array(
				'name'            => 'page',
				'singular_type'   => 'Page',
				'plural_type'     => 'Pages',
				'plural_name'     => 'pages',
				'registered_name' => 'page',
			),
		);
		$actual = \BEForever\WPGraphQL\graphql_build_post_types( $post_types );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for posts.
	 */
	public function test_graphql_build_wp_config() {
		$post_types = \BEForever\WPGraphQL\graphql_build_post_types();

		$expected = array(
			'post_types' => $post_types,
		);
		$actual = \BEForever\WPGraphQL\graphql_build_wp_config();

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for posts.
	 */
	public function test_posts_query() {
		$post_args = array(
			'post_status' => 'publish',
			'post_content' => 'Hi!',
			'post_title' => 'Hello!',
		);

		$post_id = $this->factory->post->create( $post_args );

		$query = '{ posts(first: 2) { id, title, content } }';
		$expected = array(
			'data' => array(
				'posts' => array(
					array(
						'id' => $post_id,
						'title' => 'Hello!',
						'content' => 'Hi!',
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for comments.
	 */
	public function test_comments_query() {
		$comment_args = array(
			'comment_approved' => '1',
			'comment_content' => 'Hi!',
			'user_id' => $this->admin,
		);

		$comment_id = $this->factory->comment->create( $comment_args );

		$query = '{ comments(first: 2) { id, content, author { id } } }';
		$expected = array(
			'data' => array(
				'comments' => array(
					array(
						'id' => $comment_id,
						'content' => 'Hi!',
						'author' => array(
							'id' => $this->admin,
						),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for terms.
	 */
	public function test_terms_query() {
		$term_args = array(
			'taxonomy' => 'post_tag',
			'name'     => 'Test',
		);

		$term_id = $this->factory->term->create( $term_args );

		$query = '{ terms{ name, taxonomy, count } }';
		$expected = array(
			'data' => array(
				'terms' => array(
					array(
						'name' => 'Test',
						'taxonomy' => 'post_tag',
						'count' => 0,
					),
					array(
						'name' => 'Uncategorized',
						'taxonomy' => 'category',
						'count' => 0,
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for terms.
	 */
	public function test_taxonomies_query() {
		$query = '{ taxonomies(first: 2) { name } }';
		$expected = array(
			'data' => array(
				'taxonomies' => array(
					array(
						'name' => 'Categories',
					),
					array(
						'name' => 'Tags',
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for themes.
	 */
	public function test_themes_query() {
		// Testing themes by name is unreliable instead we need to test that the name field is not null.
		$query = '{ themes(first: 2) { name } }';
		$response = $this->get_graphql_response( $query );

		$expected = true;
		$actual   = ! is_null( $response['data']['themes'][0]['name'] );

		$this->assertEquals( $expected, $actual );

		$expected = 2;
		$actual   = count( $response['data']['themes'] );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Tests the query for plugins.
	 */
	public function test_plugins_query() {
		$query = '{ plugins(first: 1) { name } }';
		$expected = array(
			'data' => array(
				'plugins' => array(
					array(
						'name' => 'Akismet',
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Executes GraphQL query.
	 *
	 * @param string $query    GraphQL query string.
	 * @return array Array of PHP data.
	 */
	private function get_graphql_response( $query ) {
		$wp_config = \BEForever\WPGraphQL\graphql_build_wp_config();

		// Build the complete type system.
		$type_system = new TypeSystem( $wp_config );

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

		return $result;
	}

	/**
	 * Tests expected results against response from GraphQL query.
	 *
	 * @param string $query    GraphQL query string.
	 * @param mixed  $expected Expected data to be returned in response.
	 */
	private function check_graphql_response( $query, $expected ) {
		$wp_config = \BEForever\WPGraphQL\graphql_build_wp_config();

		// Build the complete type system.
		$type_system = new TypeSystem( $wp_config );

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

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Displays a single plugin.
	 *
	 * This function is currently not ideal, as the best way to grab plugin data
	 * currently requires require a file from wp-admin, which hasn't loaded yet.
	 *
	 * @param string $name Name of the plugin.
	 * @return WP_Error|WP_REST_Response
	 */
	private function get_plugin( $name ) {
		// Puts input into a url friendly slug format.
		$slug = sanitize_title( $name );
		$plugin = null;

		// File has not loaded.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// This is missing must use and drop in plugins.
		$plugins = apply_filters( 'all_plugins', get_plugins() );

		foreach ( $plugins as $path => $plugin_data ) {
			if ( sanitize_title( $plugin_data['Name'] ) === $slug ) {
				$plugin         = $plugin_data;
				$plugin['path'] = $path;
				// Exit early when plugin is found.
				break;
			}
		}

		return $plugin;
	}
}
