<?php
/**
 * @license GPL-2.0
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\Clauses;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

/**
 * @since 1.0.0
 */
class RawSQL {
	/**
	 * @var string
	 */
	public $sql;

	/**
	 * @param  string  $sql
	 * @param  array<int,mixed>|string|null  $args
	 */
	public function __construct( $sql, $args = null ) {
		$this->sql = $args ? DB::prepare( $sql, $args ) : $sql;
	}
}
