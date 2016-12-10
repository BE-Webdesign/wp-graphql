<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Object type for users.
 */
class AvatarType extends BaseType {
	/**
	 * Builds a user type.
	 *
	 * @param TypeSystem $types Number of types.
	 */
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Avatar',
			'fields' => function() use ( $types ) {
				return [
					'size'            => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'The size of the avatar in pixels. A value of 96 will match a 96px x 96px gravatar image.', 'wp-graphql' ),
					),
					'height'          => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'Height of the avatar image.', 'wp-graphql' ),
					),
					'width'           => array(
						'type'        => $types->int(),
						'description' => esc_html__( 'Width of the avatar image.', 'wp-graphql' ),
					),
					'default'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( "URL for the default image or a default type. Accepts '404' (return a 404 instead of a default image), 'retro' (8bit), 'monsterid' (monster), 'wavatar' (cartoon face), 'indenticon' (the 'quilt'), 'mystery', 'mm', or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF), or 'gravatar_default' (the Gravatar logo).", 'wp-graphql' ),
					),
					'force_default'   => array(
						'type'        => $types->boolean(),
						'description' => esc_html__( 'Whether to always show the default image, never the Gravatar.', 'wp-graphql' ),
					),
					'rating'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( "What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are judged in that order.", 'wp-graphql' ),
					),
					'scheme'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Type of url scheme to use. Typically HTTP vs. HTTPS.', 'wp-graphql' ),
					),
					//'processed_args' => $types->string(),
					'extra_attr'      => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'HTML attributes to insert in the IMG element. Is not sanitized.', 'wp-graphql' ),
					),
					'found_avatar'    => array(
						'type'        => $types->boolean(),
						'description' => esc_html__( 'Whether the avatar was successfully found.', 'wp-graphql' ),
					),
					'url'             => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URL for the gravatar image source.', 'wp-graphql' ),
					),
				];
			},
			'description' => esc_html__( 'Avatars are profile images for users. WordPress by default uses the Gravatar service to host and fetch avatars from.', 'wp-graphql' ),
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					if ( isset( $value[ $info->fieldName ] ) ) {
						return $value[ $info->fieldName ];
					}

					return $value->{$info->fieldName};
				}
			},
		]);
	}
}
