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
				return array(
					'id'             => $types->id(),
					'author'         => $types->user(),
					'date'           => $types->string(),
					'date_gmt'       => $types->string(),
					'content'        => $types->string(),
					'title'          => $types->string(),
					'excerpt'        => $types->string(),
					'post_status'    => $types->string(),
					'comment_status' => $types->string(),
					'ping_status'    => $types->string(),
					'slug'           => $types->string(),
					'to_ping'        => $types->string(),
					'pinged'         => $types->string(),
					'modified'       => $types->string(),
					'modified_gmt'   => $types->string(),
					'parent'         => $types->id(),
					'guid'           => $types->string(),
					'menu_order'     => $types->string(),
					'type'           => $types->string(),
					'mime_type'      => $types->string(),
					'comment_count'  => $types->int(),
					'comments' => [
						'type' => $types->listOf( $types->comment() ),
						'args' => [
							// Limit and after are equivalent to per_page and offset.
							'first' => $types->int(),
							'after' => $types->int(),
						],
					],
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

	public function id( \WP_Post $post, $args, AppContext $context ) {
		return $post->ID;
	}

	public function parent( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_parent;
	}

	public function author( \WP_Post $post, $args, AppContext $context ) {
		return get_user_by( 'id', $post->post_author );
	}

	public function guid( \WP_Post $post, $args, AppContext $context ) {
		return $post->guid;
	}

	public function title( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_title;
	}

	public function content( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_content;
	}

	public function excerpt( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_excerpt;
	}

	public function date( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_date;
	}

	public function date_gmt( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_date_gmt;
	}

	public function modified( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_modified;
	}

	public function modified_gmt( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_modified_gmt;
	}

	public function slug( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_name;
	}

	public function mime_type( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_mime_type;
	}

	public function type( \WP_Post $post, $args, AppContext $context ) {
		return $post->post_type;
	}

	/**
	 * Comments field resolver.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return array List of WP_Comment objects.
	 */
	public function comments( \WP_Post $post, $args, AppContext $context ) {
		$query_args = array(
			'post_id' => $post->ID,
		);

		if ( isset( $args['first'] ) ) {
			$query_args['number'] = $args['first'];
		}

		if ( isset( $args['after'] ) ) {
			$query_args['offset'] = $args['after'];
		}

		$comments_query = new \WP_Comment_Query( $query_args );
		$comments = $comments_query->get_comments();

		return ! empty( $comments ) ? $comments : null;
	}
}
