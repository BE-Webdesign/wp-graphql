<?php
/**
 * BaseType class file.
 *
 * @package WP_GraphQL/src/Type/
 */

namespace BEForever\WPGraphQL\Type;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\DefinitionContainer;

/**
 * Enables avoidance of extending GraphQL classes directly.
 */
abstract class BaseType implements DefinitionContainer {
	/**
	 * Type definition
	 *
	 * @var Type
	 */
	protected $definition;

	/**
	 * Function to get definition. getDefinition() is defined in DefinitionContainer
	 *
	 * @return Type
	 */
	public function getDefinition() {
		return $this->definition;
	}
}
