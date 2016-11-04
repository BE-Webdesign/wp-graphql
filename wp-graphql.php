<?php
/**
 * Plugin Name:     WP GraphQL
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A GraphQL API for WordPress
 * Author:          BEForever - Edwin Cromley
 * Author URI:      YOUR SITE HERE
 * Text Domain:     wp-graphql
 * Domain Path:     /languages
 * Version:         0.0.0
 *
 * @package         WP_GraphQL
 */

/**
 * This plugin borrows heavily from the WP REST API and Webonyx's GraphQL PHP,
 * port. WP GraphQL copies some of the amazing work done on those projects. This
 * project is an attempt to create a GraphQL API for WordPress. The goal is to
 * provide a better API experience via this plugin. One of the longer term goals
 * is to backport the WebOnyx library into a PHP 5.2 compatible library and port
 * this to PHP 5.2 as well so everyone can enjoy GraphQL.
 *
 * The plugin architecture around notifying users of failure is also taken from
 * Gary Pendergast, aka @pento, from one of his blog posts.
 */

namespace BEForever\WPGraphQL;
use \BEForever\WPGraphQL\TypeSystem;
use \BEForever\WPGraphQL\AppContext;
use \BEForever\WPGraphQL\Data\DataSource;
use \GraphQL\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Type\Definition\Config;
use \GraphQL\Error\FormattedError;

// Exit if plugin is directly accessed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Initializing the GraphQL API hook styling taken from REST API project.
register_activation_hook( __FILE__, '\BEForever\WPGraphQL\graphql_activation_check' );
add_action( 'init',          '\BEForever\WPGraphQL\graphql_api_init' );
add_action( 'parse_request', '\BEForever\WPGraphQL\graphql_api_loaded' );

graphql_setup();

require_once WP_GRAPHQL__PLUGIN_DIR . '/vendor/autoload.php';

/**
 * Registers rewrite rules for the API.
 *
 * @since 4.4.0
 *
 * @see graphql_api_register_rewrites()
 * @global WP $wp Current WordPress environment instance.
 */
function graphql_api_init() {
	graphql_api_register_rewrites();

	global $wp;
	$wp->add_query_var( 'graphql_path' );
}

/**
 * Adds REST rewrite rules.
 *
 * @since 4.4.0
 *
 * @see add_rewrite_rule()
 */
function graphql_api_register_rewrites() {
	add_rewrite_rule( '^' . get_graphql_url_path() . '/?$', 'index.php?graphql_path=/', 'top' );
	add_rewrite_rule( '^' . get_graphql_url_path() . '/(.*)?', 'index.php?graphql_path=/$matches[1]', 'top' );
}

/**
 * Fires when a request is parsed by WordPress and matches the GraphQL endpoint.
 *
 * @global WP             $wp             Current WordPress environment instance.
 * @global WP_REST_Server $wp_graphql_server ResponseHandler instance (usually WP_REST_Server).
 */
function graphql_api_loaded() {
	if ( empty( $GLOBALS['wp']->query_vars['graphql_path'] ) ) {
		return;
	}

	/**
	 * Whether this is a GRAPHQL Request.
	 *
	 * @var bool
	 */
	define( 'GRAPHQL_REQUEST', true );

	/**
	 * Serve request and echo response.
	 */
	$response = serve_graphql_request();
	echo wp_json_encode( $response );
	header( 'Content-Type: application/json; charset=UTF-8', true, 200 );

	// We're done.
	die();
}

/**
 * Retrieves the URL path for the GraphQL endpoint.
 *
 * @return string Prefix.
 */
function get_graphql_url_path() {
	/**
	 * Makes the GraphQL endpoint changeable.
	 *
	 * @param string $path Pathname for graphql. DO NOT use leading slash.
	 */
	return apply_filters( 'graphql_url', 'graphql' );
}

/**
 * Sets up necessary checks for plugin activation.
 */
function graphql_setup() {
	// Define constants.
	define_graphql_constants();

	// Used to check version of WP to make sure it is greater than 4.4!
	add_action( 'admin_init', '\BEForever\WPGraphQL\graphql_check_compatibilty' );

	// If you are using an unsupported version of wordpress then don't do anything.
	if ( ! graphql_is_wp_compatible( WP_GRAPHQL_MINIMUM_WP_VERSION ) || ! graphql_is_php_compatible( WP_GRAPHQL_MINIMUM_PHP_VERSION ) ) {
		return;
	}
}

/**
 * Checks whether the installation is compatible with required PHP and WP versions.
 */
function graphql_is_compatible() {
	return ( graphql_is_wp_compatible() && graphql_is_php_compatible() );
}

/**
 * This function runs an activation check to make sure plugin runs correctly.
 *
 * This is typically triggered via WP-CLI activation.
 */
function graphql_activation_check() {
	if ( ! graphql_is_compatible() ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'WP GraphQL requires WordPress 4.6 or higher and PHP 5.4 or higher!', 'wp-graphql' ) );
	}

	flush_rewrite_rules();
}

/**
 * Checks whether the WordPress install meets the plugin requirements.
 *
 * This runs on plugin activation, which can happen via the admin or via CLI.
 */
function graphql_check_compatibilty() {
	if ( ! graphql_is_compatible() ) {
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action( 'admin_notices', '\BEForever\WPGraphQL\graphql_disabled_wp_notice' );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
}

/**
 * Echos an error notification.
 */
function graphql_disabled_wp_notice() {
	echo '<div class="error"><p>', esc_html__( 'WP GraphQL requires WordPress 4.6 or higher!', 'wp-graphql' ), '</p></div>';
}

/**
 * Echos an error notification.
 */
function graphql_disabled_php_notice() {
	echo '<div class="error"><p>', esc_html__( 'WP GraphQL requires PHP 5.4 or higher!', 'wp-graphql' ), '</p></div>';
}

/**
 * Checks whether the current WP Version is compatible.
 *
 * @return boolean True if compatible false if not.
 */
function graphql_is_wp_compatible() {
	if ( version_compare( $GLOBALS['wp_version'], WP_GRAPHQL_MINIMUM_WP_VERSION, '<' ) ) {
		return false;
	}

	return true;
}

/**
 * Checks whether the current PHP Version is compatible.
 *
 * @return boolean True if compatible false if not.
 */
function graphql_is_php_compatible() {
	if ( version_compare( phpversion(), WP_GRAPHQL_MINIMUM_PHP_VERSION, '<' ) ) {
		return false;
	}

	return true;
}

/**
 * Define Constants
 */
function define_graphql_constants() {
	graphql_define( 'WP_GRAPHQL_VERSION', '0.0.0' );
	graphql_define( 'WP_GRAPHQL_MINIMUM_WP_VERSION', '4.6' );
	graphql_define( 'WP_GRAPHQL_MINIMUM_PHP_VERSION', '5.4' );
	graphql_define( 'WP_GRAPHQL__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	graphql_define( 'WP_GRAPHQL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Defines a constant that has not already been defined.
 *
 * Consider changing this as it might be better to have a fatal error incase
 * there are conflicting constants.
 *
 * @param string $constant_name Name of the constant to be defined.
 * @param mixed  $value         Value for the constant.
 */
function graphql_define( $constant_name, $value ) {
	if ( ! defined( $constant_name ) ) {
		define( $constant_name, $value );
	}
}

/**
 * Does a GraphQL request.
 *
 * @return mixed The response data.
 */
function serve_graphql_request() {
	if ( ! empty( $_GET['debug'] ) ) {
		/**
		 * Enable additional validation of type configs
		 * (disabled by default because it is costly)
		 */
		Config::enableValidation();

		/**
		 * Catch custom errors ( to report them in query results if debugging is enabled )
		 */
		$php_errors = [];
		set_error_handler( function( $severity, $message, $file, $line ) use ( &$php_errors ) {
			$php_errors[] = new ErrorException( $message, 0, $severity, $file, $line );
		} );
	}

	try {
		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Set currently authenticated user to be the viewer in our context.
		$app_context->viewer = wp_get_current_user();

		$app_context->root_url = 'http://local.wordpress.dev/graphql';
		$app_context->request = $_REQUEST;

		// Parse incoming query and variables.
		if ( isset( $_SERVER['CONTENT_TYPE'] ) && false !== strpos( $_SERVER['CONTENT_TYPE'], 'application/json' ) ) {
			$raw = file_get_contents( 'php://input' ) ?: '';
			$data = json_decode( $raw, true );
		} else {
			$data = $_REQUEST;
		}

		// Add query data and variable defaults.
		$data += [ 'query' => null, 'variables' => null ];

		// If an empty query is present for now display the hello message.
		if ( null === $data['query'] ) {
			$data['query'] = '
				{hello}
			';
		}

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		// Add any reported PHP errors to result.
		if ( ! empty( $_GET['debug'] ) && ! empty( $php_errors ) ) {
			$result['extensions']['phpErrors'] = array_map(
				[ 'GraphQL\Error\FormattedError', 'createFromPHPError' ],
				$php_errors
			);
		}

		$http_status = 200;
	} catch ( \Exception $error ) {
		$http_status = 500;
		if ( ! empty( $_GET['debug'] ) ) {
			$result['extensions']['exception'] = FormattedError::createFromException( $error );
		} else {
			$result['errors'] = [ FormattedError::create( 'Unexpected Error' ) ];
		}
	}

	return $result;
}
