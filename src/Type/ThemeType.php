<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\Data\User;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class ThemeType extends BaseType {
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Theme',
			'fields' => function() use ( $types ) {
				return array(
					'slug'         => $types->string(),
					'name'         => $types->string(),
					'screenshot'   => $types->string(),
					'theme_uri'    => $types->string(),
					'description'  => $types->string(),
					'author'       => $types->string(),
					'author_uri'   => $types->string(),
					'tags'         => $types->listOf( $types->string() ),
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

	public function slug( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get_stylesheet();
	}

	public function name( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'Name' );
	}

	public function screenshot( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get_screenshot();
	}

	public function theme_uri( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'ThemeURI' );
	}

	public function description( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'Description' );
	}

	public function author( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'Author' );
	}

	public function author_uri( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'AuthorURI' );
	}

	public function tags( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'Tags' );
	}

	public function version( \WP_Theme $theme, $args, AppContext $context) {
		return $theme->get( 'Version' );
	}
}
