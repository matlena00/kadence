<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\StellarWP\Uplink\Contracts;

use KadenceWP\ReCaptcha\StellarWP\ContainerContract\ContainerInterface;
use KadenceWP\ReCaptcha\StellarWP\Uplink\Config;

abstract class Abstract_Provider implements Provider_Interface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor for the class.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct( $container = null ) {
		$this->container = $container ?: Config::get_container();
	}

}
