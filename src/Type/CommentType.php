<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class CommentType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Comment',
			'fields' => function() use ( $types ) {
				return array(
					'id'           => $types->id(),
					'post'         => $types->id(),
					'author'       => $types->user(),
					'author_ip'    => $types->string(),
					'date'         => $types->string(),
					'date_gmt'     => $types->string(),
					'content'      => $types->string(),
					'karma'        => $types->int(),
					'approved'     => $types->string(),
					'agent'        => $types->string(),
					'type'         => $types->string(),
					'parent'       => $types->comment(),
					'user_id'      => $types->id(),
					'children' => [
						'type' => $types->listOf( $types->comment() ),
						'description' => 'Returns comments based on collection args',
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

	// Testing to see if it will resolve based on interface.
	public function id( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_ID;
	}

	public function post( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_post_ID;
	}

	/**
	 * This for whatever reason is the author name not id for the author.
	 */
	public function author( \WP_Comment $comment, $args, AppContext $context) {
		return new \WP_User( $comment->comment_author );
	}

	public function author_ip( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_author_IP;
	}

	public function content( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_content;
	}

	public function karma( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_karma;
	}

	public function approved( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_approved;
	}

	public function agent( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_agent;
	}

	public function type( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->comment_type;
	}

	public function parent( \WP_Comment $comment, $args, AppContext $context) {
		return get_comment( $comment->comment_parent );
	}

	/**
	 * ID of comment author.
	 */
	public function user_id( \WP_Comment $comment, $args, AppContext $context) {
		return $comment->user_id;
	}

	/**
	 * Comments field resolver.
	 *
	 * @param \WP_Comment $comment   Value for the resolver.
	 * @param array       $args    List of arguments for this resolver.
	 * @param AppContext  $context Context object for the Application.
	 * @return array List of WP_Comment objects.
	 */
	public function children( \WP_Comment $comment, $args, AppContext $context ) {
		$query_args = array(
			'parent' => $comment->comment_ID,
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
