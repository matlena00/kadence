<?php
/**
 * Kadence Blocks Pro Helper Functions
 */

/**
 * Hex to RGBA
 *
 * @param string $hex string hex code.
 * @param number $alpha alpha number.
 */
function kadence_blocks_pro_hex2rgba( $hex, $alpha ) {
	if ( empty( $hex ) ) {
		return '';
	}
	if ( 'transparent' === $hex ) {
		return $hex;
	}
	$hex = str_replace( '#', '', $hex );
	if ( strlen( $hex ) == 3 ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}
	$rgba = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
	return $rgba;
}
