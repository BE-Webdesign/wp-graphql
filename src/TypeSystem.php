<?php
namespace BEForever\WPGraphQL;

use BEForever\WPGraphQL\Type\CommentType;
use BEForever\WPGraphQL\Type\TermType;
use BEForever\WPGraphQL\Type\Enum\ContentFormatEnum;
use BEForever\WPGraphQL\Type\Enum\ImageSizeEnumType;
use BEForever\WPGraphQL\Type\Field\HtmlField;
use BEForever\WPGraphQL\Type\MentionType;
use BEForever\WPGraphQL\Type\NodeType;
use BEForever\WPGraphQL\Type\QueryType;
use BEForever\WPGraphQL\Type\Scalar\EmailType;
use BEForever\WPGraphQL\Type\StoryType;
use BEForever\WPGraphQL\Type\Scalar\UrlType;
use BEForever\WPGraphQL\Type\UserType;
use BEForever\WPGraphQL\Type\ImageType;
use BEForever\WPGraphQL\Type\PostType;
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
	 * Comment object type.
	 */
	private $term;

	/**
	 * User object type.
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
		return $this->term ?: ( $this->query = new TermType( $this ) );
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
		return $this->node ?: ($this->node = new NodeType($this));
	}

	// Let's add internal types as well for consistent experience

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
