<?php
namespace BEForever\WPGraphQL\Type;

use BEForever\WPGraphQL\AppContext;
use BEForever\WPGraphQL\TypeSystem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * Basic query class is an object type.
 */
class QueryType extends BaseType {
	/**
	 * Object constructor.
	 */
	public function __construct( TypeSystem $types ) {
		$this->definition = new ObjectType([
			'name' => 'Query',
			'fields' => [
				'post' => [
					'type' => $types->post(),
					'description' => 'Returns post by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				],
				'user' => [
					'type' => $types->user(),
					'description' => 'Returns user by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				],
				'comment' => [
					'type' => $types->comment(),
					'description' => 'Returns comment by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				],
				'term' => array(
					'type' => $types->term(),
					'description' => 'Returns term by id',
					'args' => [
						'id' => $types->nonNull( $types->id() ),
					],
				),
				'hello' => Type::string(),
			],
			'resolveField' => function( $value, $args, $context, ResolveInfo $info ) {
				return $this->{$info->fieldName}( $value, $args, $context, $info );
			},
		]);
	}

	/**
	 * Post field resolver.
	 *
	 * Note that post is a field within the query type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Post Post object.
	 */
	public function post( $value, $args, AppContext $context ) {
		return get_post( $args['id'] );
	}

	/**
	 * Comment field resolver.
	 *
	 * Note that comment is a field within the query type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Comment Comment object.
	 */
	public function comment( $value, $args, AppContext $context ) {
		return get_comment( $args['id'] );
	}

	/**
	 * User field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_User User object.
	 */
	public function user( $value, $args, AppContext $context ) {
		return get_user_by( 'id', $args['id'] );
	}

	/**
	 * Term field resolver.
	 *
	 * Note that user is a field within the user type.
	 *
	 * @param mixed      $value   Value for the resolver.
	 * @param array      $args    List of arguments for this resolver.
	 * @param AppContext $context Context object for the Application.
	 * @return WP_Term Term object.
	 */
	public function term( $value, $args, AppContext $context ) {
		return get_term( $args['id'] );
	}

	/**
	 * Hello resolver.
	 *
	 * @return String Welcoming message.
	 */
	public function hello() {
		return 'Welcome to WP GraphQL, I hope that you will enjoy this adventure!';
	}
}
