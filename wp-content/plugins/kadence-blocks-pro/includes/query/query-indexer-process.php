<?php
/**
 * Query Indexer Process
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Generic.Commenting.DocComment.MissingShort

/**
 * Query Indexer Process Class
 */
class Kadence_Blocks_Pro_Query_Indexer_Process extends KadenceWP_Kadence_Blocks_Pro_WP_Background_Process {

	/**
	 * @var string
	 */
	protected $prefix = 'kadence-blocks-pro';

	/**
	 * @var string
	 */
	protected $action = 'facet-indexer';

	/**
	 * Perform task with queued item.
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $facet Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $facet ) {
		require_once __DIR__ . '/query-indexer.php';
		$indexer = new Kadence_Blocks_Pro_Query_Indexer( $this );

		$indexer->log_action( 'Processing facet: ' . $facet['hash'] );

		return $indexer->process_objects( $facet );
	}

	/**
	 * Time Exceeded
	 **/
	public function time_exceeded_public() {
		return $this->time_exceeded();
	}

	/**
	 * Memory Exceeded
	 **/
	public function memory_exceeded_public() {
		return $this->memory_exceeded();
	}

	/**
	 * Entire queue completed processing.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		if ( defined( 'KB_DEBUG' ) && KB_DEBUG ) {
			error_log( 'Queue completed' );//phpcs:ignore
		}
	}
}
