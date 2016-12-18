<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Object type for users.
 */
class UserType extends BaseType {
	/**
	 * Builds a user type.
	 *
	 * @param TypeSystem $types Number of types.
	 */
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'User',
			'fields' => function() use ( $types ) {
				return [
					'id'              => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'This field is the id of the user. The id of the user matches WP_User->ID field and the value in the ID column for the `wp_users` table in SQL.', 'wp-graphql' ),
					),
					'capabilities'    => array(
						'type'        => $types->listOf( $types->string() ),
						'description' => esc_html__( 'Returns the list of individually assigned capabilities a user has. This is equivalent to the array keys of WP_User->allcaps, where the capability is set to true.', 'wp-graphql' ),
					),
					'cap_key'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'User metadata option name. Usually it will be `wp_capabilities`.', 'wp-graphql' ),
					),
					'roles'           => array(
						'type'        => $types->listOf( $types->string() ),
						'description' => esc_html__( 'A list of roles that the user has. Roles can be used for querying for certain types of users, but should not be used in permissions checks.', 'wp-graphql' ),
					),
					'extra_capabilities' => array(
						'type'        => $types->listOf( $types->string() ),
						'description' => esc_html__( 'A complete list of capabilities including capabilities inherited from a role. This is equivalent to the array keys of WP_User->allcaps.', 'wp-graphql' ),
					),
					'email'           => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Email of the user. This is equivalent to the WP_User->user_email property.', 'wp-graphql' ),
					),
					'first_name'      => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'First name of the user. This is equivalent to the WP_User->user_first_name property.', 'wp-graphql' ),
					),
					'last_name'       => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Last name of the user. This is equivalent to the WP_User->user_last_name property.', 'wp-graphql' ),
					),
					'description'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Description of the user.', 'wp-graphql' ),
					),
					'username'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Username for the user. This field is equivalent to WP_User->user_login.', 'wp-graphql' ),
					),
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Display name of the user. This is equivalent to the WP_User->dispaly_name property.', 'wp-graphql' ),
					),
					'registered_date' => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The date the user registered or was created. The field follows a full ISO8601 date string format.', 'wp-graphql' ),
					),
					'nickname'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Nickname of the user.', 'wp-graphql' ),
					),
					'url'             => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'A website url that is associated with the user.', 'wp-graphql' ),
					),
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The slug for the user. This field is equivalent to WP_User->user_nicename', 'wp-graphql' ),
					),
					'locale'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The preferred language locale set for the user. Value derived from get_user_locale().', 'wp-graphql' ),
					),
					'avatar'          => array(
						'type'        => $types->avatar(),
						'description' => esc_html__( 'Avatar object for user. The avatar object can be retrieved in different sizes by specifying the size argument.', 'wp-graphql' ),
						'args'        => array(
							'size'    => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The size attribute of the avatar field can be used to fetch avatars of different sizes. The value corresponds to the dimension in pixels to fetch. The default is 96 pixels.', 'wp-graphql' ),
								'defaultValue' => 96,
							),
						),
					),
					'posts'           => array(
						'type'        => $types->listOf( $types->post() ),
						'description' => esc_html__( 'A collection of posts assigned to the user.', 'wp-graphql' ),
						'args' => [
							// Limit and after are equivalent to per_page and offset.
							'first' => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The number of posts by this user to query for. First is pretty much the same as LIMIT in SQL, or a `per_page` parameter in pagination.', 'wp-graphql' ),
								'defaultValue' => 10,
							),
							'after' => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The offset for the query.', 'wp-graphql' ),
								'defaultValue' => 0,
							),
						],
					),
					'comments'        => array(
						'type'        => $types->listOf( $types->comment() ),
						'description' => esc_html__( 'A collection of comments assigned to the user.', 'wp-graphql' ),
						'args' => [
							// Limit and after are equivalent to per_page and offset.
							'first' => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The number of comments by this user to query for. First is pretty much the same as LIMIT in SQL, or a `per_page` parameter in pagination.', 'wp-graphql' ),
								'defaultValue' => 10,
							),
							'after' => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The offset for the query.', 'wp-graphql' ),
								'defaultValue' => 0,
							),
						],
					),
				];
			},
			'interfaces' => [
				$types->node(),
			],
			'description' => esc_html__( 'The User type is internally represented by a WP_User object. Some of the fields are aliases for properties of the WP_User object.', 'wp-graphql' ),
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					return $value->{$info->fieldName};
				}
			},
		]);
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function id( \WP_User $user, $args, AppContext $context ) {
		return $user->ID;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function capabilities( \WP_User $user, $args, AppContext $context ) {
		// Filters list for capabilities the user has.
		return array_keys( array_filter( $user->allcaps, function( $cap ) {
			return true === $cap;
		} ) );
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function extra_capabilities( \WP_User $user, $args, AppContext $context ) {
		return array_keys( $user->allcaps );
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function email( \WP_User $user, $args, AppContext $context ) {
		return $user->user_email;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function username( \WP_User $user, $args, AppContext $context ) {
		return $user->user_login;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function name( \WP_User $user, $args, AppContext $context ) {
		return $user->display_name;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function url( \WP_User $user, $args, AppContext $context ) {
		return $user->user_url;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function slug( \WP_User $user, $args, AppContext $context ) {
		return $user->user_nicename;
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function locale( \WP_User $user, $args, AppContext $context ) {
		return get_user_locale( $user );
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function registered_date( \WP_User $user, $args, AppContext $context ) {
		return date( 'c', strtotime( $user->user_registered ) );
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function avatar( \WP_User $user, $args, AppContext $context ) {
		return get_avatar_data( $user->ID, array( 'size', $args['size'] ) );
	}

	/**
	 * Posts field resolver.
	 *
	 * Returns a collection of posts by the author.
	 *
	 * @param \WP_User   $user    User for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return string
	 */
	public function posts( \WP_User $user, $args, AppContext $context ) {
		$query_args = array(
			'author'        => $user->ID,
			'no_found_rows' => true,
		);

		if ( isset( $args['first'] ) ) {
			$query_args['posts_per_page'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$posts_query = new \WP_Query();
		$posts = $posts_query->query( $query_args );
		return ! empty( $posts ) ? $posts : null;
	}

	/**
	 * Comments field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array List of WP_Comment objects.
	 */
	public function comments( \WP_User $user, $args, AppContext $context ) {
		$query_args = array(
			'user_id' => $user->ID,
		);

		if ( isset( $args['first'] ) ) {
			$query_args['number'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$comments_query = new \WP_Comment_Query();
		$comments = $comments_query->query( $query_args );
		return ! empty( $comments ) ? $comments : null;
	}
}
