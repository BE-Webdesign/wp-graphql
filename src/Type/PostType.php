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
					'id'              => array(
						'type'        => $types->id(),
						'description' => esc_html__( 'The id field matches the WP_Post->ID field.', 'wp-graphql' ),
					),
					'author'          => array(
						'type'        => $types->user(),
						'description' => esc_html__( "The author field will return a queryable User type matching the post's author.", 'wp-graphql' ),
					),
					'date'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Post publishing date.', 'wp-graphql' ),
					),
					'date_gmt'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The publishing date set in GMT.', 'wp-graphql' ),
					),
					'content'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The content of the post. This is currently just the raw content. An amendment to support rendered content needs to be made.', 'wp-graphql' ),
					),
					'title'           => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The title of the post. This is currently just the raw title. An amendment to support rendered title needs to be made.', 'wp-graphql' ),
					),
					'excerpt'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The excerpt of the post. This is currently just the raw excerpt. An amendment to support rendered excerpts needs to be made.', 'wp-graphql' ),
					),
					'post_status'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The current status of the post. ( published, draft, etc. ) This should be changed to an enum type supporting valid stati.', 'wp-graphql' ),
					),
					'comment_status'  => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Whether the comments are open or closed for this particular post. Needs investigating.', 'wp-graphql' ),
					),
					'ping_status'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Whether the pings are open or closed for this particular post. Needs investigating.', 'wp-graphql' ),
					),
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The uri slug for the post. This is equivalent to the WP_Post->post_name field and the post_name column in the database for the `wp_posts` table.', 'wp-graphql' ),
					),
					'to_ping'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URLs queued to be pinged.', 'wp-graphql' ),
					),
					'pinged'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URLs that have been pinged.', 'wp-graphql' ),
					),
					'modified'        => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The local modified time for a post. If a post was recently updated the modified field will change to match the corresponding time.', 'wp-graphql' ),
					),
					'modified_gmt'    => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The GMT modified time for a post. If a post was recently updated the modified field will change to match the corresponding time in GMT.', 'wp-graphql' ),
					),
					'parent'          => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'The ID of the corresponding post parent. This is only typically used with hierarchical content types. This field is equivalent to the value of WP_Post->post_parent and the post_parent column in the `wp_posts` database table.', 'wp-graphql' ),
					),
					'guid'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The global unique identifier for this post. This currently matches the value stored in WP_Post->guid and the guid column in the `wp_posts` database table.', 'wp-graphql' ),
					),
					'menu_order'      => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'A field used for ordering posts. This is typically used with nav menu items or for special ordering of hierarchical content types.', 'wp-graphql' ),
					),
					'type'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'This field tells what kind of content type the object is. In WordPress different post types are used to denote different types of content. This field is equivalent to the value of WP_Post->post_type and the post_type column in the `wp_posts` database table.', 'wp-graphql' ),
					),
					'mime_type'       => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'If the post is an attachment or a media file, this field will carry the corresponding MIME type. This field is equivalent to the value of WP_Post->post_mime_type and the post_mime_type column in the `wp_posts` database table.', 'wp-graphql' ),
					),
					'comment_count'  => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'The number of comments. Even though WP GraphQL denotes this field as an integer, in WordPress this field should be saved as a numeric string for compatability.', 'wp-graphql' ),
					),
					'comments' => [
						'type' => $types->listOf( $types->comment() ),
						'description' => 'Returns comments for post based on collection args',
						'args' => [
							// Limit and after are equivalent to per_page and offset.
							'first' => $types->int(),
							'after' => $types->int(),
						],
					],
				);
			},
			'description' => esc_html__( 'The post object for WordPress. Internally this is an instance of WP_Post. Posts are the main default content type of WordPress. They are the heart and soul of a blog.', 'wp-graphql' ),
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
		return new \WP_User( $post->post_author );
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
