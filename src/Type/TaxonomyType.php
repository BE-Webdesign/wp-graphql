<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class TaxonomyType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Taxonomy',
			'fields' => function() use ( $types ) {
				return array(
					'name'         => $types->string(),
					'slug'         => $types->string(),
					'description'  => $types->string(),
					'show_cloud'   => $types->boolean(),
					'hierarchical' => $types->boolean(),
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

	public function name( $taxonomy, $args, AppContext $context) {
		if ( ! is_null( $taxonomy ) ) {
			return $taxonomy->label;
		} else {
			return null;
		}
	}

	public function slug( $taxonomy, $args, AppContext $context) {
		if ( ! is_null( $taxonomy ) ) {
			// Name property of the taxonomy is closer to the slug.
			return $taxonomy->name;
		} else {
			return null;
		}
	}

	public function description( $taxonomy, $args, AppContext $context) {
		if ( ! is_null( $taxonomy ) ) {
			return $taxonomy->description;
		} else {
			return null;
		}
	}

	public function show_cloud( $taxonomy, $args, AppContext $context) {
		if ( ! is_null( $taxonomy ) ) {
			return $taxonomy->show_tagcloud;
		} else {
			return null;
		}
	}

	public function hierarchical( $taxonomy, $args, AppContext $context) {
		if ( ! is_null( $taxonomy ) ) {
			return $taxonomy->hierarchical;
		} else {
			return null;
		}
	}
}
