<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MenuItemType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'MenuItem',
			'fields' => function() use ( $types ) {
				return array(
					'id'              => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'ID of the nav menu item.', 'wp-graphql' ),
					),
					'title'           => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Title of the nav menu item. This is what is displayed visually in a menu as text.', 'wp-graphql' ),
					),
					'type'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The type relating the object being displayed in the type.', 'wp-graphql' ),
					),
					'object_id'       => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'The ID of the object the menu item relates to.', 'wp-graphql' ),
					),
					'object'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The serialized object that the menu item represents.', 'wp-graphql' ),
					),
					'target'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Target attribute for the link.', 'wp-graphql' ),
					),
					'xfn'             => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Link relationship.', 'wp-graphql' ),
					),
					'url'             => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URL for the nav menu item.', 'wp-graphql' ),
					),
				);
			},
			'interfaces' => [
				$types->node(),
			],
			'description' => esc_html__( 'Navigation menu items are the individual items assigned to a menu. These are rendered as the links in a navigation menu.', 'wp-graphql' ),
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					return $value->{$info->fieldName};
				}
			},
		]);
	}

	public function id( \WP_Post $menu_item, $args, AppContext $context) {
		return $menu_item->ID;
	}

	public function title( \WP_Post $menu_item, $args, AppContext $context) {
		$title = $menu_item->post_title;

		if ( empty( $title ) ) {
			$post_id = get_post_meta( $menu_item->ID, '_menu_item_object_id', true );
			$title = get_the_title( $post_id );
		}

		return $title;
	}

	public function type( \WP_Post $menu_item, $args, AppContext $context) {
		return get_post_meta( $menu_item->ID, '_menu_item_type', true );
	}

	public function object_id( \WP_Post $menu_item, $args, AppContext $context) {
		return get_post_meta( $menu_item->ID, '_menu_item_object_id', true );
	}

	public function object( \WP_Post $menu_item, $args, AppContext $context) {
		return get_post_meta( $menu_item->ID, '_menu_item_object', true );
	}

	public function target( \WP_Post $menu_item, $args, AppContext $context) {
		return get_post_meta( $menu_item->ID, '_menu_item_target', true );
	}

	public function xfn( \WP_Post $menu_item, $args, AppContext $context) {
		return get_post_meta( $menu_item->ID, '_menu_item_xfn', true );
	}

	public function url( \WP_Post $menu_item, $args, AppContext $context) {
		$url = get_post_meta( $menu_item->ID, '_menu_item_url', true );

		if ( empty( $url ) ) {
			$post_id = get_post_meta( $menu_item->ID, '_menu_item_object_id', true );
			$url = get_permalink( $post_id );
		}

		return $url;
	}
}
