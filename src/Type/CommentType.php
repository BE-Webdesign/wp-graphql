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
					'id'              => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'The id field for comments matches the comment id. This field is equivalent to WP_Comment->comment_ID and the value matching the `comment_ID` column in SQL.', 'wp-graphql' ),
					),
					'post'            => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'The post field for comments matches the post id the comment is assigned to. This field is equivalent to WP_Comment->comment_post_ID and the value matching the `comment_post_ID` column in SQL.', 'wp-graphql' ),
					),
					'author'          => array(
						'type'        => $types->user(),
						'description' => esc_html__( 'The post field for comments matches the post id the comment is assigned to. This field is equivalent to WP_Comment->comment_post_ID and the value matching the `comment_post_ID` column in SQL.', 'wp-graphql' ),
					),
					'author_ip'       => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'IP address for the author. This field is equivalent to WP_Comment->comment_author_IP and the value matching the `comment_author_IP` column in SQL.', 'wp-graphql' ),
					),
					'date'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Date the comment was posted in local time. This field is equivalent to WP_Comment->date and the value matching the `date` column in SQL.', 'wp-graphql' ),
					),
					'date_gmt'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Date the comment was posted in GMT. This field is equivalent to WP_Comment->date_gmt and the value matching the `date_gmt` column in SQL.', 'wp-graphql' ),
					),
					'content'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Content of the comment. This field is equivalent to WP_Comment->comment_content and the value matching the `comment_content` column in SQL.', 'wp-graphql' ),
					),
					'karma'           => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'Karma value for the comment. This field is equivalent to WP_Comment->comment_karma and the value matching the `comment_karma` column in SQL.', 'wp-graphql' ),
					),
					'approved'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The approval status of the comment. This field is equivalent to WP_Comment->comment_approved and the value matching the `comment_approved` column in SQL.', 'wp-graphql' ),
					),
					'agent'           => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'User agent used to post the comment. This field is equivalent to WP_Comment->comment_agent and the value matching the `comment_agent` column in SQL.', 'wp-graphql' ),
					),
					'type'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Type of comment. This field is equivalent to WP_Comment->comment_type and the value matching the `comment_type` column in SQL.', 'wp-graphql' ),
					),
					'parent'          => array(
						'type'        => $types->comment(),
						'description' => esc_html__( 'Parent comment of current comment. This field is equivalent to the WP_Comment instance matching the WP_Comment->comment_parent ID.', 'wp-graphql' ),
					),
					'children' => [
						'type' => $types->listOf( $types->comment() ),
						'description' => 'Returns comments based on collection args',
						'args' => [
							// Limit and after are equivalent to per_page and offset.
							'first' => array(
								'type'        => $types->int(),
								'description' => esc_html__( 'The number of comment children to query for. First is pretty much the same as LIMIT in SQL, or a `per_page` parameter in pagination.', 'wp-graphql' ),
							),
							'after' => array(
								'type'        => $types->int(),
								'description' => esc_html__( 'The offset for the query.', 'wp-graphql' ),
							),
						],
					],
				);
			},
			'interfaces' => [
				$types->node(),
			],
			'description' => esc_html__( 'Comments are used to provide user created context to a post or other comments. The comment type internally matches a WP_Comment.', 'wp-graphql' ),
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
		return new \WP_User( $comment->user_id );
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
