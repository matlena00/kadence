<?php
/**
 * @license GPL-2.0-only
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\Container\Contracts;

use KadenceWP\KadenceBlocks\Adbar\Dot;
use KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\Container\ContainerAdapter;

/**
 * Providers should extend this abstract in order to have
 * access to the container instance to register their bindings.
 */
abstract class Provider implements Providable
{
	/**
	 * @readonly
	 */
	protected Container $container;
	/**
	 * @readonly
	 */
	protected Dot $config;
	/**
	 * Whether this service provider will be a deferred one or not.
	 */
	protected bool $deferred = false;

	public function __construct(Container $container, Dot $config) {
		/** @var Container|ContainerAdapter $container */
		$this->container = $container;
		/** @var Dot<array-key, mixed> */
		$this->config = $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDeferred(): bool {
		return $this->deferred;
	}

	/**
	 * {@inheritDoc}
	 */
	public function provides(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot(): void {
	}
}
