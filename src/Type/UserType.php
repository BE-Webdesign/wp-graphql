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
					'id'           => $types->id(),
					'capabilities' => $types->listOf( $types->string() ),
					'cap_key'      => $types->string(),
					'roles'        => $types->listOf( $types->string() ),
					'extra_capabilities' => $types->listOf( $types->string() ),
					'email'        => $types->string(),
					'first_name'   => $types->string(),
					'last_name'    => $types->string(),
					'description'  => $types->string(),
					'username'     => $types->string(),
					'name'         => $types->string(),
					'registered_date' => $types->string(),
					'nickname'     => $types->string(),
					'url'          => $types->string(),
					'slug'         => $types->string(),
					'locale'       => $types->string(),
				];
			},
			'interfaces' => [
				$types->node(),
			],
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
}
