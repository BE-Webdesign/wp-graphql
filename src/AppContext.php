<?php
namespace BEForever\WPGraphQL;

use BEForever\WPGraphQL\Data\DataSource;
use BEForever\WPGraphQL\Data\User;
use GraphQL\Utils;

/**
 * Context object used to define the context of the GraphQL Request.
 *
 * This is passed into \GraphQL\GraphQL::execute() as the third argument.
 */
class AppContext {
	/**
	 * The root url for the request.
	 *
	 * @var string
	 */
	public $root_url;

	/**
	 * Should be set to the authenticated user.
	 *
	 * Currently set to wp_get_current_user() in serve_graphql_request()
	 *
	 * @var User
	 */
	public $viewer;

	/**
	 * Request data matching this context.
	 *
	 * @var \mixed
	 */
	public $request;
}
