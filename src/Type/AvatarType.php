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
class AvatarType extends BaseType {
	/**
	 * Builds a user type.
	 *
	 * @param TypeSystem $types Number of types.
	 */
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Avatar',
			'fields' => function() use ( $types ) {
				return [
					'size'          => $types->int(),
					'height'        => $types->int(),
					'width'         => $types->int(),
					'default'       => $types->string(),
					'force_default' => $types->boolean(),
					'rating'        => $types->string(),
					'scheme'        => $types->string(),
					//'processed_args' => $types->string(),
					'extra_attr'    => $types->string(),
					'found_avatar'  => $types->boolean(),
					'url'           => $types->string(),
				];
			},
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					if ( isset( $value[ $info->fieldName ] ) ) {
						return $value[ $info->fieldName ];
					}

					return $value->{$info->fieldName};
				}
			},
		]);
	}
}
