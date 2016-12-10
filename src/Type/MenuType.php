<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MenuType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Menu',
			'fields' => function() use ( $types ) {
				return array(
					'id'              => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'ID of the menu. Equivalent to WP_Term->term_id.', 'wp-graphql' ),
					),
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Display name of the menu. Equivalent to WP_Term->name.', 'wp-graphql' ),
					),
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The url friendly name of the menu. Equivalent to WP_Term->slug', 'wp-graphql' ),
					),
					'group'           => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Group of the menu. Groups are useful as secondary indexes for SQL. Equivalent to WP_Term->term_group.', 'wp-graphql' ),
					),
					'items'           => array(
						'type'        => $types->listOf( $types->menu_item() ),
						'description' => esc_html__( 'The nav menu items assigned to the menu.', 'wp-graphql' ),
					),
				);
			},
			'interfaces' => [
				$types->node(),
			],
			'description' => esc_html__( 'Menus are the containers for navigation items. Menus can be assigned to menu locations, which are typically registered by the active theme.', 'wp-graphql' ),
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					return $value->{$info->fieldName};
				}
			},
		]);
	}

	public function id( \WP_Term $menu, $args, AppContext $context) {
		return $menu->term_id;
	}

	public function group( \WP_Term $menu, $args, AppContext $context) {
		return $menu->term_group;
	}

	public function items( \WP_Term $menu, $args, AppContext $context) {
		return wp_get_nav_menu_items( $menu->name );
	}
}
