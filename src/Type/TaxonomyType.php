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
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The display name of the taxonomy. This field is equivalent to WP_Taxonomy->label', 'wp-graphql' ),
					),
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The url friendly name of the taxonomy. This field is equivalent to WP_Taxonomy->name', 'wp-graphql' ),
					),
					'description'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Description of the taxonomy. This field is equivalent to WP_Taxonomy->description', 'wp-graphql' ),
					),
					'show_cloud'      => array(
						'type'        => $types->boolean(),
						'description' => esc_html__( 'Whether to show the taxonomy as part of a tag cloud widget. This field is equivalent to WP_Taxonomy->show_tagcloud', 'wp-graphql' ),
					),
					'hierarchical'    => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Whether the taxonomy is hierarchical. This field is equivalent to WP_Taxonomy->hierarchical', 'wp-graphql' ),
					),
				);
			},
			'description' => esc_html__( 'Taxonomies are groups for which content types can be grouped under. Taxonomies are comprised of terms. Content is assigned to terms which belong to a specific taxonomy. Internally this field maps to WP_Taxonomy.', 'wp-graphql' ),
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
