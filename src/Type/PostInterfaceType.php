<?php
/**
 * BaseType class file.
 *
 * @package WP_GraphQL/src/Type/
 */
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\Data\Story;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\Data\Image;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\InterfaceType;

/**
 * A node type represents some node on our binary tree.
 *
 * It is an interface type that can be used for Users, Posts, and any resource
 * that needs an ID field.
 */
class PostInterfaceType extends BaseType {
	/**
	 * Object constructor.
	 *
	 * @param TypeSystem $types Current TypeSystem.
	 */
	public function __construct( TypeSystem $types ) {
		$this->definition = new InterfaceType( array(
			'name'   => 'PostInterface',
			'fields' => array(
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
			),
			'resolveType' => function ( $object ) use ( $types ) {
				return $this->resolveType( $object, $types );
			},
		) );
	}

	/**
	 * Type resolver used for introspection.
	 *
	 * @param mixed      $object What type the interface is using.
	 * @param TypeSystem $types  Current TypeSystem.
	 */
	public function resolveType( $object, TypeSystem $types ) {
		return $types->post_object( $object->post_type );
	}
}
