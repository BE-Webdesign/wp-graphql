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
					'id'              => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'ID for the term. The id field is equivalent to WP_Term->term_id or the `term_id` field in SQL.', 'wp-graphql' ),
					),
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Name of the term. Name is essentially the display name for the term, usually capitalized and plural. The name field is equivalent to WP_Term->name or the `term_name` field in SQL.', 'wp-graphql' ),
					),
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Name of the term. Slug is the more url friendly name of the term. The name field is equivalent to WP_Term->slug or the `term_slug` field in SQL.', 'wp-graphql' ),
					),
					'group'           => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Group for the term. Groups of terms can be used for performance enhancements while querying they act like a secondary index in SQL. The group field is equivalent to WP_Term->term_group or the `term_group` field in SQL.', 'wp-graphql' ),
					),
					'taxonomy_id'     => array(
						'type'        => $types->id(),
						'description' => esc_html__( "The id of the term's taxonomy. This field is equivalent to WP_Term->taxonomy_id", 'wp-graphql' ),
					),
					'taxonomy'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( "The display name of the term's taxonomy. This field is equivalent to WP_Term->taxonomy", 'wp-graphql' ),
					),
					'description'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The description for the term. This field is equivalent to WP_Term->description', 'wp-graphql' ),
					),
					'parent'          => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'The id for the parent term. Terms can be hierarchically organized in one to many relationships. This field is equivalent to WP_Term->parent', 'wp-graphql' ),
					),
					'count'           => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'The count of items assigned to the term. This field is equivalent to WP_Term->count', 'wp-graphql' ),
					),
					'children'        => array(
						'type'        => $types->listOf( $types->term() ),
						'description' => esc_html__( 'The count of items assigned to the term. This field is equivalent to WP_Term->count', 'wp-graphql' ),
						'args'        => array(
							'first'            => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The first number of items to fetch for the collection.', 'wp-graphql' ),
								// WordPress internally uses 0 to fetch all; this is a bad idea.
								'defaultValue' => 0,
							),
							'after'            => array(
								'type'         => $types->int(),
								'description'  => esc_html__( 'The offset for fetching the collection.', 'wp-graphql' ),
								'defaultValue' => 0,
							),
						),
					),
				);
			},
			'interfaces' => [
				$types->node(),
			],
			'description' => esc_html__( 'Terms are used to name particular groups within a taxonomy. WordPress ships with categories and tags as default taxonomies. Any individual category in the category taxonomy will be a term. This type internally mirrors the WP_Term object.', 'wp-graphql' ),
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					return $value->{$info->fieldName};
				}
			},
		]);
	}

	public function id( \WP_Term $term, $args, AppContext $context ) {
		return $term->term_id;
	}

	public function group( \WP_Term $term, $args, AppContext $context ) {
		return $term->term_group;
	}

	public function children( \WP_Term $term, $args, AppContext $context ) {
		$terms = get_terms( array(
			'taxonomy'   => $term->taxonomy,
			'parent'     => $term->term_id,
			'number'     => $args['first'],
			'offset'     => $args['after'],
			'hide_empty' => false,
		) );

		if ( is_wp_error( $terms ) ) {
			return null;
		}

		return $terms;
	}
}
