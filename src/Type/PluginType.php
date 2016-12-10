<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class PluginType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Plugin',
			'fields' => function() use ( $types ) {
				return array(
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Display name of the plugin.', 'wp-graphql' ),
					),
					'plugin_uri'      => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URI for the plugin website. This is useful for directing users for support requests etc.', 'wp-graphql' ),
					),
					'description'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Description of the plugin.', 'wp-graphql' ),
					),
					'author'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Name of the plugin author(s), may also be a company name.', 'wp-graphql' ),
					),
					'author_uri'      => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URI for the related author(s)/company website.', 'wp-graphql' ),
					),
					'version'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Current version of the plugin.', 'wp-graphql' ),
					),
				);
			},
			'description' => esc_html__( 'Plugins are pieces of software used to extend the functionlity of WordPress.', 'wp-graphql' ),
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				if ( method_exists( $this, $info->fieldName ) ) {
					return $this->{$info->fieldName}( $value, $args, $context, $info );
				} else {
					return $value->{$info->fieldName};
				}
			},
		]);
	}

	public function name( array $plugin, $args, AppContext $context) {
		return $plugin['Name'];
	}

	public function plugin_uri( array $plugin, $args, AppContext $context) {
		return $plugin['PluginURI'];
	}

	public function description( array $plugin, $args, AppContext $context) {
		return $plugin['Description'];
	}

	public function author( array $plugin, $args, AppContext $context) {
		return $plugin['Author'];
	}

	public function author_uri( array $plugin, $args, AppContext $context) {
		return $plugin['AuthorURI'];
	}

	public function version( array $plugin, $args, AppContext $context) {
		return $plugin['Version'];
	}
}
