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
					'id' => $types->id(),
					'email' => $types->string(),
					'first_name' => $types->string(),
					'last_name' => $types->string(),
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
	public function first_name( \WP_User $user, $args, AppContext $context ) {
		return $user->first_name;
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
	public function last_name( \WP_User $user, $args, AppContext $context ) {
		return $user->last_name;
	}
}
