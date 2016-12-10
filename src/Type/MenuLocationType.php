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
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Display name of the registered menu location.', 'wp-graphql' ),
					),
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The URL friendly identified for the registered menu location.', 'wp-graphql' ),
					),
					'active_menu'     => array(
						'type'        => $types->menu(),
						'description' => esc_html__( 'The active menu assigned to this registered menu locations.', 'wp-graphql' ),
					),
				);
			},
			'description' => esc_html__( 'Menu locations are typically registered by the active theme. They may include social menus, or the primary menu, or custom menu widgets.', 'wp-graphql' ),
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
