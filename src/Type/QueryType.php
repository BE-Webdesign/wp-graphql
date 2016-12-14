<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * Basic query class is an object type.
 */
class QueryType extends BaseType {
	/**
	 * Object constructor.
	 */
	public function __construct( TypeSystem $types ) {
		$fields = array(
			'posts' => [
				'type' => $types->listOf( $types->post() ),
				'description' => 'Returns posts based on collection args',
				'args' => [
					// First and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'user' => [
				'type' => $types->user(),
				'description' => 'Returns user by id',
				'args' => [
					'id' => $types->nonNull( $types->id() ),
				],
			],
			'users' => [
				'type' => $types->listOf( $types->user() ),
				'description' => 'Returns users based on collection args',
				'args' => [
					// Limit and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'comment' => [
				'type' => $types->comment(),
				'description' => 'Returns comment by id',
				'args' => [
					'id' => $types->nonNull( $types->id() ),
				],
			],
			'comments' => [
				'type' => $types->listOf( $types->comment() ),
				'description' => 'Returns comments based on collection args',
				'args' => [
					// Limit and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'term' => array(
				'type' => $types->term(),
				'description' => 'Returns term by id',
				'args' => [
					'id' => $types->nonNull( $types->id() ),
				],
			),
			'terms' => [
				'type' => $types->listOf( $types->term() ),
				'description' => 'Returns terms based on collection args',
				'args' => [
					// Limit and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'taxonomy' => array(
				'type' => $types->taxonomy(),
				'description' => 'Returns taxonomy by name',
				'args' => [
					'name' => $types->nonNull( $types->string() ),
				],
			),
			'taxonomies' => [
				'type' => $types->listOf( $types->taxonomy() ),
				'description' => 'Returns taxonomies based on collection args',
				'args' => [
					// Limit and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'menu_item' => array(
				'type' => $types->menu_item(),
				'description' => 'Returns menu_item by id',
				'args' => [
					'id' => $types->nonNull( $types->id() ),
				],
			),
			'menu' => array(
				'type' => $types->menu(),
				'description' => 'Returns menu by id',
				'args' => [
					'id' => $types->id(),
					'name' => $types->string(),
					'slug' => $types->string(),
				],
			),
			'menu_location' => array(
				'type' => $types->menu_location(),
				'description' => 'Returns menu location by name',
				'args' => [
					'slug' => $types->nonNull( $types->string() ),
				],
			),
			'menu_locations' => array(
				'type' => $types->listOf( $types->menu_location() ),
				'description' => 'Returns menu locations registered in global',
			),
			'theme' => array(
				'type' => $types->theme(),
				'description' => 'Returns theme by name',
				'args' => [
					'slug' => $types->nonNull( $types->string() ),
				],
			),
			'themes' => [
				'type' => $types->listOf( $types->theme() ),
				'description' => 'Returns themes based on collection args',
				'args' => [
					// Limit and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'plugin' => array(
				'type' => $types->plugin(),
				'description' => 'Returns plugin by name',
				'args' => [
					'slug' => $types->nonNull( $types->string() ),
				],
			),
			'plugins' => [
				'type' => $types->listOf( $types->plugin() ),
				'description' => 'Returns plugins based on collection args',
				'args' => [
					// Limit and after are equivalent to per_page and offset.
					'first' => $types->int(),
					'after' => $types->int(),
				],
			],
			'post_type' => [
				'type' => $types->post_type(),
				'description' => 'Returns registered post type',
				'args' => [
					'name' => $types->string(),
				],
			],
			'post_types' => [
				'type' => $types->listOf( $types->post_type() ),
				'description' => 'Returns registered post types',
			],
			'hello' => Type::string(),
		);

		/**
		 * This is the dynamic creation of custom post types.
		 */
		if ( isset( $types->wp_config['post_types'] ) && is_array( $types->wp_config['post_types'] ) ) {
			foreach ( $types->wp_config['post_types'] as $post_type ) {
				$fields[ $post_type ] = array(
					'type'        => $types->post_object( $post_type ),
					'description' => $post_type,
					'args'        => array(
						'id' => $types->nonNull( $types->id() ),
					),
					'resolve' => function( $value, $args, $context ) use ( $post_type ) {
						$post = get_post( $args['id'] );

						if ( isset( $post->post_type ) && $post_type === $post->post_type ) {
							return $post;
						}

						return null;
					},
				);
			}
		}

		$this->definition = new ObjectType([
			'name'         => 'Query',
			'fields'       => $fields,
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				return $this->{$info->fieldName}( $value, $args, $context, $info );
			},
		]);
	}

	/**
	 * Post field resolver.
	 *
	 * Note that post is a field within the query type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Post Post object.
	 */
	public function post( $value, $args, AppContext $context ) {
		return get_post( $args['id'] );
	}

	/**
	 * Posts field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array List of WP_Post objects.
	 */
	public function posts( $value, $args, AppContext $context ) {
		$query_args = array();

		if ( isset( $args['first'] ) ) {
			$query_args['posts_per_page'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$posts_query = new \WP_Query( $query_args );
		$posts = $posts_query->get_posts();
		return ! empty( $posts ) ? $posts : null;
	}

	/**
	 * Comment field resolver.
	 *
	 * Note that comment is a field within the query type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Comment Comment object.
	 */
	public function comment( $value, $args, AppContext $context ) {
		return get_comment( $args['id'] );
	}

	/**
	 * Comments field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array List of WP_Comment objects.
	 */
	public function comments( $value, $args, AppContext $context ) {
		$query_args = array();

		if ( isset( $args['first'] ) ) {
			$query_args['number'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$comments_query = new \WP_Comment_Query( $query_args );
		$comments = $comments_query->get_comments();
		return ! empty( $comments ) ? $comments : null;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_User User object.
	 */
	public function user( $value, $args, AppContext $context ) {
		return get_user_by( 'id', $args['id'] );
	}

	/**
	 * Users field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array List of WP_User objects.
	 */
	public function users( $value, $args, AppContext $context ) {
		$query_args = array(
			'orderby' => 'ID',
		);

		if ( isset( $args['first'] ) ) {
			$query_args['number'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$users_query = new \WP_User_Query( $query_args );
		$users_query->query();
		return $users_query->get_results();
	}

	/**
	 * Term field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Term Term object.
	 */
	public function term( $value, $args, AppContext $context ) {
		return get_term( $args['id'] );
	}

	/**
	 * Terms field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array List of WP_User objects.
	 */
	public function terms( $value, $args, AppContext $context ) {
		$query_args = array(
			'hide_empty' => false,
		);

		if ( isset( $args['first'] ) ) {
			$query_args['number'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$terms_query = new \WP_Term_Query();
		$terms = $terms_query->query( $query_args );

		return ! empty( $terms ) ? $terms : null;
	}

	/**
	 * Term field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Taxonomy Taxonomy object.
	 */
	public function taxonomy( $value, $args, AppContext $context ) {
		$taxonomy = get_taxonomy( $args['name'] );

		return false !== $taxonomy ? $taxonomy : null;
	}

	/**
	 * Term field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Taxonomy Taxonomy object.
	 */
	public function taxonomies( $value, $args, AppContext $context ) {
		$taxonomies = get_taxonomies( '', 'objects' );

		if ( isset( $args['first'] ) || isset( $args['after'] ) ) {
			$limit = isset( $args['first'] ) ? $args['first'] : count( $taxonomies );
			$offset = isset( $args['after'] ) ? $args['after'] : 0;

			$taxonomies = array_splice( $taxonomies, $offset, $limit );
		}

		return ! empty( $taxonomies ) ? $taxonomies : null;
	}

	/**
	 * Menu item field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Post Post object. Nav menu items are posts.
	 */
	public function menu_item( $value, $args, AppContext $context ) {
		$menu_item = get_post( $args['id'] );

		// If it is a nav menu item return it otherwise null.
		return 'nav_menu_item' === $menu_item->post_type ? $menu_item : null;
	}

	/**
	 * Menu field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Term Term object. Menus are terms.
	 */
	public function menu( $value, $args, AppContext $context ) {
		$menu = null;

		// Check if the request ID arg is set.
		if ( isset( $args['id'] ) ) {
			$menu = get_term( $args['id'] );
		} elseif ( isset( $args['name'] ) ) {
			$menu = wp_get_nav_menu_object( $args['name'] );
		} elseif ( isset( $args['slug'] ) ) {
			$menu = wp_get_nav_menu_object( $args['slug'] );
		}

		if ( isset( $menu->taxonomy ) ) {
			// If it is a nav menu item return it otherwise null.
			return 'nav_menu' === $menu->taxonomy ? $menu : null;
		}

		// If there was some sort of error return null.
		return null;
	}

	/**
	 * Menu location field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array Array of register nav menu data.
	 */
	public function menu_location( $value, $args, AppContext $context ) {
		$menus = get_registered_nav_menus();
		$name = $args['slug'];

		// If it is a nav menu item return it otherwise null.
		if ( isset( $menus[ $name ] ) ) {
			return array(
				'slug' => $name,
				'name' => $menus[ $name ],
			);
		}
	}

	/**
	 * Menu locations field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array Array of register nav menus data.
	 */
	public function menu_locations( $value, $args, AppContext $context ) {
		$menus = array();
		$registered_menus = get_registered_nav_menus();

		foreach ( $registered_menus as $slug => $name ) {
			$menus[] = array(
				'slug' => $slug,
				'name' => $name,
			);
		}

		return ! empty( $menus ) ? $menus : null;
	}

	/**
	 * Theme field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return \WP_Theme Theme object.
	 */
	public function theme( $value, $args, AppContext $context ) {
		$theme = wp_get_theme( $args['slug'] );

		return $theme->exists() ? $theme : null;
	}

	/**
	 * Themes field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return \WP_Theme Theme object.
	 */
	public function themes( $value, $args, AppContext $context ) {
		$themes = wp_get_themes();

		if ( isset( $args['first'] ) || isset( $args['after'] ) ) {
			$limit = isset( $args['first'] ) ? $args['first'] : count( $themes );
			$offset = isset( $args['after'] ) ? $args['after'] : 0;

			$themes = array_splice( $themes, $offset, $limit );
		}

		return ! empty( $themes ) ? $themes : null;
	}

	/**
	 * Plugin field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array Array of plugin data.
	 */
	public function plugin( $value, $args, AppContext $context ) {
		return $this->get_plugin( $args['slug'] );
	}

	/**
	 * Plugins field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array Array of plugin data.
	 */
	public function plugins( $value, $args, AppContext $context ) {
		$plugins = $this->get_plugins();

		if ( isset( $args['first'] ) || isset( $args['after'] ) ) {
			$limit = isset( $args['first'] ) ? $args['first'] : count( $plugins );
			$offset = isset( $args['after'] ) ? $args['after'] : 0;

			$plugins = array_splice( $plugins, $offset, $limit );
		}

		return ! empty( $plugins ) ? $plugins : null;
	}

	/**
	 * Post types field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array Array of plugin data.
	 */
	public function post_type( $value, $args, AppContext $context ) {
		$post_types = get_post_types( '', 'objects' );
		$post_type = array();

		foreach ( $post_types as $type ) {
			if ( $args['name'] === $type->name ) {
				$post_type = $type;
			}
		}

		return ! empty( $post_type ) ? $post_type : null;
	}

	/**
	 * Post types field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array Array of plugin data.
	 */
	public function post_types( $value, $args, AppContext $context ) {
		return get_post_types( array(), 'objects' );
	}

	/**
	 * Hello resolver.
	 *
	 * @return String Welcoming message.
	 */
	public function hello() {
		return 'Welcome to WP GraphQL, I hope that you will enjoy this adventure!';
	}

	/**
	 * Displays a single plugin.
	 *
	 * This function is currently not ideal, as the best way to grab plugin data
	 * currently requires require a file from wp-admin, which hasn't loaded yet.
	 *
	 * @param string $name Name of the plugin.
	 * @return array Array of plugin data.
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

	/**
	 * Returns a list of plugins.
	 *
	 * @return array Array of an array of plugin data.
	 */
	private function get_plugins() {
		$plugins = array();
		// File has not loaded.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// This is missing must use and drop in plugins.
		$plugins = apply_filters( 'all_plugins', get_plugins() );

		return $plugins;
	}
}
