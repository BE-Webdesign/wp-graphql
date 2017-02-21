<?php
namespace WPGraphQL\Type;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use WPGraphQL\Types;

class TermObjectQueryArgsType extends InputObjectType {

	public function __construct() {

		$config = [
			'name' => 'termArgs',
			'fields' => function() {
				$fields = [
					'taxonomy' => [
						'type' => Types::list_of( Types::string() ),
						'description' => __( 'Array of taxonomy names, to which results should be limited.', 'wp-graphql' ),
					],
					'objectIds' => [
						'type' => Types::list_of( Types::int() ),
						'description' => __( 'Array of object IDs. Results will be limited to terms associated with these objects.', 'wp-graphql' ),
					],
					'orderby' => [
						'type' => new EnumType( [
							'name' => 'termsOrderby',
							'values' => [
								[
									'name' => 'NAME',
									'value' => 'name',
								],
								[
									'name' => 'SLUG',
									'value' => 'slug',
								],
								[
									'name' => 'TERM_GROUP',
									'value' => 'term_group',
								],
								[
									'name' => 'TERM_ID',
									'value' => 'term_id',
								],
								[
									'name' => 'ID',
									'value' => 'id',
								],
								[
									'name' => 'DESCRIPTION',
									'value' => 'description',
								],
								[
									'name' => 'COUNT',
									'value' => 'count',
								],
							],
						] ),
						'description' => __( 'Field(s) to order terms by. Defaults to \'name\'.', 'wp-graphql' ),
					],
					'hideEmpty' => [
						'type' => Types::boolean(),
						'description' => __( 'Whether to hide terms not assigned to any posts. Accepts true or false. Default true', 'wp-graphql' ),
					],
					'include' => [
						'type' => Types::list_of( Types::int() ),
						'description' => __( 'Array of term ids to include. Default empty array.', 'wp-graphql' ),
					],
					'exclude' => [
						'type' => Types::list_of( Types::int() ),
						'description' => __( 'Array of term ids to exclude. If $include is non-empty, $exclude is ignored. Default empty array.', 'wp-graphql' ),
					],
					'excludeTree' => [
						'type' => Types::list_of( Types::int() ),
						'description' => __( 'Array of term ids to exclude along with all of their descendant terms. If $include is non-empty, $exclude_tree is ignored. Default empty array.', 'wp-graphql' ),
					],
					'name' => [
						'type' => Types::list_of( Types::string() ),
						'description' => __( 'Array of names to return term(s) for. Default empty.', 'wp-graphql' ),
					],
					'slug' => [
						'type' => Types::list_of( Types::string() ),
						'description' => __( 'Array of slugs to return term(s) for. Default empty.', 'wp-graphql' ),
					],
					'termTaxonomId' => [
						'type' => Types::list_of( Types::int() ),
						'description' => __( 'Array of term taxonomy IDs, to match when querying terms.', 'wp-graphql' ),
					],
					'hierarchical' => [
						'type' => Types::boolean(),
						'description' => __( 'Whether to include terms that have non-empty descendants (even if $hide_empty is set to true). Default true.', 'wp-graphql' ),
					],
					'search' => [
						'type' => Types::string(),
						'description' => __( 'Search criteria to match terms. Will be SQL-formatted with wildcards before and after. Default empty.', 'wp-graphql' ),
					],
					'nameLike' => [
						'type' => Types::string(),
						'description' => __( 'Retrieve terms with criteria by which a term is LIKE `$name__like`. Default empty.', 'wp-graphql' ),
					],
					'descriptionLike' => [
						'type' => Types::string(),
						'description' => __( 'Retrieve terms where the description is LIKE `$description__like`. Default empty.', 'wp-graphql' ),
					],
					'padCounts' => [
						'type' => Types::boolean(),
						'description' => __( 'Whether to pad the quantity of a term\'s children in the quantity of each term\'s "count" object variable. Default false.', 'wp-graphql' ),
					],
					'childOf' => [
						'type' => Types::int(),
						'description' => __( 'Term ID to retrieve child terms of. If multiple taxonomies are passed, $child_of is ignored. Default 0.', 'wp-graphql' ),
					],
					'parent' => [
						'type' => Types::int(),
						'description' => __( 'Parent term ID to retrieve direct-child terms of. Default empty.', 'wp-graphql' ),
					],
					'childless' => [
						'type' => Types::boolean(),
						'description' => __( 'True to limit results to terms that have no children. This parameter has no effect on non-hierarchical taxonomies. Default false.', 'wp-graphql' ),
					],
					'cacheDomain' => [
						'type' => Types::string(),
						'description' => __( 'Unique cache key to be produced when this query is stored in an object cache. Default is \'core\'.', 'wp-graphql' ),
					],
					'updateTermMetaCache' => [
						'type' => Types::boolean(),
						'description' => __( 'Whether to prime meta caches for matched terms. Default true.', 'wp-graphql' ),
					],
				];

				/**
				 * Pass the fields through a filter
				 *
				 * @param array $fields
				 *
				 * @since 0.0.5
				 */
				$fields = apply_filters( 'graphql_term_object_query_args_type_fields', $fields );

				/**
				 * Sort the fields alphabetically by key. This makes reading through docs much easier
				 * @since 0.0.2
				 */
				ksort( $fields );

				return $fields;
			},
		];

		parent::__construct( $config );

	}

}