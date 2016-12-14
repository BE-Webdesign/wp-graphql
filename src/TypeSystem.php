<?php
namespace BEForever\WPGraphQL;

use BEForever\WPGraphQL\Type\PostType;
use BEForever\WPGraphQL\Type\UserType;
use BEForever\WPGraphQL\Type\CommentType;
use BEForever\WPGraphQL\Type\TermType;
use BEForever\WPGraphQL\Type\TaxonomyType;
use BEForever\WPGraphQL\Type\MenuItemType;
use BEForever\WPGraphQL\Type\MenuType;
use BEForever\WPGraphQL\Type\MenuLocationType;
use BEForever\WPGraphQL\Type\ThemeType;
use BEForever\WPGraphQL\Type\PluginType;
use BEForever\WPGraphQL\Type\PostTypeType;
use BEForever\WPGraphQL\Type\PostInterfaceType;
use BEForever\WPGraphQL\Type\PostObjectType;
use BEForever\WPGraphQL\Type\NodeType;
use BEForever\WPGraphQL\Type\QueryType;
use BEForever\WPGraphQL\Type\AvatarType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\DefinitionContainer;

/**
 * Class TypeSystem
 *
 * Acts as a registry and factory for your types.
 *
 * Can be
 *
 * @package BEForever\WPGraphQL
 */
class TypeSystem {
	/**
	 * Post object type.
	 */
	private $post;

	/**
	 * User object type.
	 */
	private $user;

	/**
	 * Comment object type.
	 */
	private $comment;

	/**
	 * Term object type.
	 */
	private $term;

	/**
	 * Taxonomy object type.
	 */
	private $taxonomy;

	/**
	 * Menu item object type.
	 */
	private $menu_item;

	/**
	 * Menu object type.
	 */
	private $menu;

	/**
	 * Menu location object type.
	 */
	private $menu_location;

	/**
	 * Theme object type.
	 */
	private $theme;

	/**
	 * Plugin object type.
	 */
	private $plugin;

	/**
	 * Post type object type.
	 */
	private $post_type;

	/**
	 * Avatar type object type.
	 */
	private $avatar;

	/**
	 * Query object type.
	 */
	private $query;

	/**
	 * WordPress configuration.
	 */
	public $wp_config;

	/**
	 * Object constructor, used to load some inherit configuration of WP.
	 *
	 * @param array $wp_config Array of configuration data for WordPress.
	 */
	public function __construct( $wp_config = array() ) {
		$this->wp_config = $wp_config;

		if ( isset( $wp_config['post_types'] ) && is_array( $wp_config['post_types'] ) ) {
			foreach ( $wp_config['post_types'] as $post_type ) {
				$this->{$post_type} = new PostObjectType( $this, $post_type );
			}
		}
	}

	/**
	 * @return PostType
	 */
	public function post() {
		return $this->post ?: ( $this->post = new PostType( $this ) );
	}

	/**
	 * @return UserType
	 */
	public function user() {
		return $this->user ?: ( $this->user = new UserType( $this ) );
	}

	/**
	 * @return CommentType
	 */
	public function comment() {
		return $this->comment ?: ( $this->comment = new CommentType( $this ) );
	}

	/**
	 * @return TermType
	 */
	public function term() {
		return $this->term ?: ( $this->term = new TermType( $this ) );
	}

	/**
	 * @return TaxonomyType
	 */
	public function taxonomy() {
		return $this->taxonomy ?: ( $this->taxonomy = new TaxonomyType( $this ) );
	}

	/**
	 * @return MenuItemType
	 */
	public function menu_item() {
		return $this->menu_item ?: ( $this->menu_item = new MenuItemType( $this ) );
	}

	/**
	 * @return MenuType
	 */
	public function menu() {
		return $this->menu ?: ( $this->menu = new MenuType( $this ) );
	}

	/**
	 * @return MenuLocationType
	 */
	public function menu_location() {
		return $this->menu_location ?: ( $this->menu_location = new MenuLocationType( $this ) );
	}

	/**
	 * @return ThemeType
	 */
	public function theme() {
		return $this->theme ?: ( $this->theme = new ThemeType( $this ) );
	}

	/**
	 * @return PluginType
	 */
	public function plugin() {
		return $this->plugin ?: ( $this->plugin = new PluginType( $this ) );
	}

	/**
	 * @return PostTypeType
	 */
	public function post_type() {
		return $this->post_type ?: ( $this->post_type = new PostTypeType( $this ) );
	}

	/**
	 * @return AvatarType
	 */
	public function avatar() {
		return $this->avatar ?: ( $this->avatar = new AvatarType( $this ) );
	}

	/**
	 * @return QueryType
	 */
	public function query() {
		return $this->query ?: ( $this->query = new QueryType( $this ) );
	}

	/**
	 * @return PostObjectType
	 */
	public function post_object( $post_type ) {
		return $this->{$post_type} ?: ( $this->{$post_type} = new PostObjectType( $this, $post_type ) );
	}


	// Interface types
	private $node;
	private $post_interface;

	/**
	 * @return NodeType
	 */
	public function node() {
		return $this->node ?: ( $this->node = new NodeType( $this ) );
	}

	public function post_interface() {
		return $this->post_interface ?: ( $this->post_interface = new PostInterfaceType( $this ) );
	}

	// Add basic scalar types.
	public function boolean() {
		return Type::boolean();
	}

	/**
	 * @return \GraphQL\Type\Definition\FloatType
	 */
	public function float() {
		return Type::float();
	}

	/**
	 * @return \GraphQL\Type\Definition\IDType
	 */
	public function id() {
		return Type::id();
	}

	/**
	 * @return \GraphQL\Type\Definition\IntType
	 */
	public function int() {
		return Type::int();
	}

	/**
	 * @return \GraphQL\Type\Definition\StringType
	 */
	public function string() {
		return Type::string();
	}

	/**
	 * @param Type|DefinitionContainer $type
	 * @return ListOfType
	 */
	public function listOf( $type ) {
		return new ListOfType( $type );
	}

	/**
	 * @param Type|DefinitionContainer $type
	 * @return NonNull
	 */
	public function nonNull( $type ) {
		return new NonNull( $type );
	}
}
