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
					'active_menu' => $types->menu(),
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

	public function slug( array $menu_location, $args, AppContext $context ) {
		return $menu_location['slug'];
	}

	public function name( array $menu_location, $args, AppContext $context ) {
		return $menu_location['name'];
	}

	public function active_menu( array $menu_location, $args, AppContext $context ) {
		$locations = get_nav_menu_locations();

		$menu = null;

		// Returns the ID of the menu item currently active in this location.
		if ( isset( $locations[ $menu_location['slug'] ] ) ) {
			$menu = get_term( $locations[ $menu_location['slug'] ] );
		}

		return $menu;
	}
}
