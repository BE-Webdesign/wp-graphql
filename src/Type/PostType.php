<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class PostType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Post',
			'fields' => function() use ( $types ) {
				return [
					'id'           => $types->id(),
					'guid'         => $types->string(),
					'title'        => $types->string(),
					'content'      => $types->string(),
					'excerpt'      => $types->string(),
					'date'         => $types->string(),
					'date_gmt'     => $types->string(),
					'modified'     => $types->string(),
					'modified_gmt' => $types->string(),
					'slug'         => $types->string(),
					'type'         => $types->string(),
					'comment_status' => $types->string(),
					'ping_status'  => $types->string(),
					'format'       => $types->string(),
				];
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

	public function id( \WP_Post $post, $args, AppContext $context) {
		return $post->ID;
	}

	public function guid( \WP_Post $post, $args, AppContext $context) {
		return $post->guid;
	}

	public function title( \WP_Post $post, $args, AppContext $context) {
		return $post->post_title;
	}

	public function content( \WP_Post $post, $args, AppContext $context) {
		return $post->post_content;
	}

	public function excerpt( \WP_Post $post, $args, AppContext $context) {
		return $post->post_excerpt;
	}

	public function date( \WP_Post $post, $args, AppContext $context) {
		return $post->post_date;
	}

	public function date_gmt( \WP_Post $post, $args, AppContext $context) {
		return $post->post_date_gmt;
	}

	public function modified( \WP_Post $post, $args, AppContext $context) {
		return $post->post_modified;
	}

	public function modified_gmt( \WP_Post $post, $args, AppContext $context) {
		return $post->post_modified_gmt;
	}

	public function slug( \WP_Post $post, $args, AppContext $context) {
		return $post->post_name;
	}

	public function type( \WP_Post $post, $args, AppContext $context) {
		return $post->post_type;
	}
}
