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
					'id'           => $types->id(),
					'title'        => $types->id(),
					'type'         => $types->string(),
					'object_id'    => $types->string(),
					'object'       => $types->string(),
					'target'       => $types->string(),
					'xfn'          => $types->string(),
					'url'          => $types->string(),
				);
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
