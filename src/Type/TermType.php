<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class TermType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Term',
			'fields' => function() use ( $types ) {
				return array(
					'id'           => $types->id(),
					'name'         => $types->string(),
					'slug'         => $types->string(),
					'group'        => $types->string(),
					'taxonomy_id'  => $types->id(),
					'taxonomy'     => $types->string(),
					'description'  => $types->string(),
					'parent'       => $types->id(),
					'count'        => $types->int(),
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

	public function id( \WP_Term $term, $args, AppContext $context) {
		return $term->term_id;
	}

	public function group( \WP_Term $term, $args, AppContext $context) {
		return $term->term_group;
	}
}
