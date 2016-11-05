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
class NodeType extends BaseType {
	/**
	 * Object constructor.
	 *
	 * @param TypeSystem $types Current TypeSystem.
	 */
	public function __construct( TypeSystem $types ) {
		$this->definition = new InterfaceType([
			'name' => 'Node',
			'fields' => [
				'id' => $types->id()
			],
			'resolveType' => function ( $object ) use ( $types ) {
				return $this->resolveType( $object, $types );
			},
		]);
	}

	/**
	 * Type resolver used for introspection.
	 *
	 * @param mixed      $object What type the interface is using.
	 * @param TypeSystem $types  Current TypeSystem.
	 */
	public function resolveType( $object, TypeSystem $types ) {
		if ( $object instanceof User ) {
			return $types->user();
		} elseif ( $object instanceof Post ) {
			return $types->post();
		} elseif ( $object instanceof Comment ) {
			return $types->comment();
		} elseif ( $object instanceof Term ) {
			return $types->term();
		} elseif ( $object instanceof MenuItem ) {
			return $types->menu_item();
		} elseif ( $object instanceof Menu ) {
			return $types->menu();
		}
	}
}
