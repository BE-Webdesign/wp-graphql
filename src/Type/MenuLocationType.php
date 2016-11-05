<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MenuLocationType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'MenuLocation',
			'fields' => function() use ( $types ) {
				return array(
					'name' => $types->string(),
					'slug' => $types->string(),
				);
			},
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					return $value->{$info->fieldName};
				}
			},
		]);
	}

	public function slug( array $menu_location, $args, AppContext $context) {
		// There should only be one key and it should be the name of the menu.
		return key( $menu_location );
	}

	public function name( array $menu_location, $args, AppContext $context) {
		// There should only be one value. It will be the name.
		return current( $menu_location );
	}
}
