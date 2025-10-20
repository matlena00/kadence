<?php
/**
 * Handles setting up a base for all subscribers.
 *
 * @package KadenceWP\ReCaptcha\StellarWP\Telemetry\Contracts
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\ReCaptcha\StellarWP\Telemetry\Contracts;

use KadenceWP\ReCaptcha\StellarWP\ContainerContract\ContainerInterface;

/**
 * Class Abstract_Subscriber
 *
 * @package KadenceWP\ReCaptcha\StellarWP\Telemetry\Contracts
 */
abstract class Abstract_Subscriber implements Subscriber_Interface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor for the class.
	 *
	 * @param ContainerInterface $container The container.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}
}
