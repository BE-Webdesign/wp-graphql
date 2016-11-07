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
		$this->definition = new ObjectType([
			'name' => 'Query',
			'fields' => [
				'post' => [
					'type' => $types->post(),
					'description' => 'Returns post by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				],
				'user' => [
					'type' => $types->user(),
					'description' => 'Returns user by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				],
				'comment' => [
					'type' => $types->comment(),
					'description' => 'Returns comment by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				],
				'term' => array(
					'type' => $types->term(),
					'description' => 'Returns term by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				),
				'taxonomy' => array(
					'type' => $types->taxonomy(),
					'description' => 'Returns taxonomy by name',
					'args' => [
						'name' => $types->nonNull( $types->string() ),
					],
				),
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
						'id' => $types->nonNull( $types->id() ),
					],
				),
				'menu_location' => array(
					'type' => $types->menu_location(),
					'description' => 'Returns menu location by name',
					'args' => [
						'slug' => $types->nonNull( $types->string() ),
					],
				),
				'theme' => array(
					'type' => $types->theme(),
					'description' => 'Returns theme by name',
					'args' => [
						'slug' => $types->nonNull( $types->string() ),
					],
				),
				'plugin' => array(
					'type' => $types->plugin(),
					'description' => 'Returns plugin by name',
					'args' => [
						'slug' => $types->nonNull( $types->string() ),
					],
				),
				'hello' => Type::string(),
			],
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
	 * Term field resolver.
	 *
	 * Note that user is a field within the user type.
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
	 * Term field resolver.
	 *
	 * Note that user is a field within the user type.
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
	 * Menu item field resolver.
	 *
	 * Note that user is a field within the user type.
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
	 * Note that user is a field within the user type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Term Term object. Menus are terms.
	 */
	public function menu( $value, $args, AppContext $context ) {
		$menu = get_term( $args['id'] );

		// If it is a nav menu item return it otherwise null.
		return 'nav_menu' === $menu->taxonomy ? $menu : null;
	}

	/**
	 * Menu location field resolver.
	 *
	 * Note that user is a field within the user type.
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
		return isset( $menus[ $name ] ) ? array( $name => $menus[ $name ] ) : null;
	}

	/**
	 * Theme field resolver.
	 *
	 * Note that user is a field within the user type.
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
	 * Plugin field resolver.
	 *
	 * Note that user is a field within the user type.
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
