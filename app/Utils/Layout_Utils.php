<?php
/**
 * Layout_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

use WP_Error;

/**
 * Layout_Utils class.
 *
 * @since 1.0.0
 */
class Layout_Utils {

	/**
	 * Get layout data from JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path Path to the JSON layout file.
	 * @return array|WP_Error
	 */
	public static function get_layout_data( $file_path ) {
		if ( empty( $file_path ) ) {
			return new WP_Error( 'layout_path_empty', esc_html__( 'Layout file path is empty.', 'dashmate' ) );
		}

		if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
			return new WP_Error( 'layout_load_error', esc_html__( 'Layout file does not exist or is not readable: ', 'dashmate' ) . $file_path );
		}

		$file_content = file_get_contents( $file_path );

		if ( false === $file_content ) {
			return new WP_Error( 'layout_load_error', esc_html__( 'Failed to read layout file: ', 'dashmate' ) . $file_path );
		}

		$layout_data = JSON_Utils::decode_from_json( $file_content );

		if ( is_wp_error( $layout_data ) ) {
			return new WP_Error( 'layout_load_error', esc_html__( 'Failed to parse layout data from file: ', 'dashmate' ) . $file_path . ' - ' . $layout_data->get_error_message() );
		}

		/**
		 * Filter layout data before returning.
		 *
		 * @since 1.0.0
		 *
		 * @param array $layout_data Layout data array.
		 * @param string $file_path  Layout file path.
		 */
		return apply_filters( 'dashmate_layout_data', $layout_data, $file_path );
	}
}
