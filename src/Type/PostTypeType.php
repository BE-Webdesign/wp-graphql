<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class PostTypeType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'PostType',
			'fields' => function() use ( $types ) {
				return array(
					'name' => $types->string(),
					'label' => $types->string(),
					//'labels' => $types->post_type_labels(),
					'description' => $types->string(),
					'public' => $types->boolean(),
					'hierarchical' => $types->boolean(),
					'exclude_from_search' => $types->boolean(),
					'publicly_queryable' => $types->boolean(),
					'show_ui' => $types->boolean(),
					'show_in_menu' => $types->boolean(),
					'show_in_nav_menus' => $types->boolean(),
					'show_in_admin_bar' => $types->boolean(),
					'menu_position' => $types->int(),
					'menu_icon' => $types->string(),
					'taxonomies' => $types->listOf( $types->string() ),
					'has_archive' => $types->boolean(),
					'can_export' => $types->boolean(),
					'delete_with_user' => $types->boolean(),
					'show_in_rest' => $types->boolean(),
					'rest_base' => $types->string(),
					'rest_controller_class' => $types->string(),
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
}
