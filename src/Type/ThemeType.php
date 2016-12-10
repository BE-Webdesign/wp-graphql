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
					'slug'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The theme slug is used to internally match themes. Theme slugs can have subdirectories like: my-theme/sub-theme. This field is equivalent to WP_Theme->get_stylesheet().', 'wp-graphql' ),
					),
					'name'            => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Display name of the theme. This field is equivalent to WP_Theme->get( "Name" ).', 'wp-graphql' ),
					),
					'screenshot'      => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The URL of the screenshot for the theme. The screenshot is intended to give an overview of what the theme looks like. This field is equivalent to WP_Theme->get_screenshot().', 'wp-graphql' ),
					),
					'theme_uri'       => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'A URI if the theme has a website associated with it. The Theme URI is handy for directing users to a theme site for support etc. This field is equivalent to WP_Theme->get( "ThemeURI" ).', 'wp-graphql' ),
					),
					'description'     => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The description of the theme. This field is equivalent to WP_Theme->get( "Description" ).', 'wp-graphql' ),
					),
					'author'          => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'Name of the theme author(s), could also be a company name. This field is equivalent to WP_Theme->get( "Author" ).', 'wp-graphql' ),
					),
					'author_uri'      => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'URI for the author/company website. This field is equivalent to WP_Theme->get( "AuthorURI" ).', 'wp-graphql' ),
					),
					'tags'            => array(
						'type'        => $types->listOf( $types->string() ),
						'description' => esc_html__( 'URI for the author/company website. This field is equivalent to WP_Theme->get( "Tags" ).', 'wp-graphql' ),
					),
					'version'         => array(
						'type'        => $types->string(),
						'description' => esc_html__( 'The current version of the theme. This field is equivalent to WP_Theme->get( "Version" ).', 'wp-graphql' ),
					),
				);
			},
			'description' => esc_html__( 'Themes are responsible for the presentational aspect of a WordPress installation. Internally this type resolves to a WP_Theme object.', 'wp-graphql' ),
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
