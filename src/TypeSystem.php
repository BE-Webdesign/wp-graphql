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
use BEForever\WPGraphQL\Type\NodeType;
use BEForever\WPGraphQL\Type\QueryType;
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
	 * Query object type.
	 */
	private $query;

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
	 * @return QueryType
	 */
	public function query() {
		return $this->query ?: ( $this->query = new QueryType( $this ) );
	}

	// Interface types
	private $node;

	/**
	 * @return NodeType
	 */
	public function node() {
		return $this->node ?: ( $this->node = new NodeType( $this ) );
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
