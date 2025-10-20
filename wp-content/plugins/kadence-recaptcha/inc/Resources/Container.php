<?php
/**
 * A local container mapping di52 methods to ContainerInterface.
 *
 * @since 0.1.0
 *
 * @package KadenceWP\Insights
 */

namespace KadenceWP\ReCaptcha;

use KadenceWP\ReCaptcha\StellarWP\ContainerContract\ContainerInterface;
use KadenceWP\ReCaptcha\lucatume\DI52\Container as DI52Container;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A local container implementation.
 *
 * @since 0.1.0
 *
 * @package KadenceWP\Insights
 *
 * @method mixed getVar(string $key, mixed|null $default = null)
 * @method void setVar(string $key, mixed $value)
 * @method void register(string $serviceProviderClass, string ...$alias)
 * @method self when(string $class)
 * @method self needs(string $id)
 * @method void give(mixed $implementation)
 * @method void callback(string|class-string|object $id, string $method)
 */
class Container implements ContainerInterface {
	/**
	 * @since 0.1.0
	 *
	 * @var DI52Container
	 */
	protected $container;

	/**
	 * Container constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param object $container The container to use.
	 */
	public function __construct( $container = null ) {
		$this->container = $container ?: new DI52Container();
	}

	/**
	 * {@inheritdoc}
	 */
	public function bind( string $id, $implementation = null, array $after_build_methods = null ) {
		$this->container->bind( $id, $implementation, $after_build_methods );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( string $id ) {
		return $this->container->get( $id );
	}

	/**
	 * @since 0.1.0
	 *
	 * @return DI52Container
	 */
	public function get_container() {
		return $this->container;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( string $id ) {
		return $this->container->has( $id );
	}

	/**
	 * {@inheritdoc}
	 */
	public function singleton( string $id, $implementation = null, array $after_build_methods = null ) {
		$this->container->singleton( $id, $implementation, $after_build_methods );
	}

	/**
	 * Defer all other calls to the container object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name The name of the method to call.
	 * @param array  $args The arguments to pass to the method.
	 */
	public function __call( $name, $args ) {
		return $this->container->{$name}( ...$args );
	}
}
