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
					'name'         => $types->string(),
					'plugin_uri'   => $types->string(),
					'description'  => $types->string(),
					'author'       => $types->string(),
					'author_uri'   => $types->string(),
					'version'      => $types->string(),
				);
			},
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
