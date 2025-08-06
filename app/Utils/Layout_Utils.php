<?php
/**
 * Layout_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

use Nilambar\Dashmate\Models\Dashboard_Model;
use WP_Error;

/**
 * Layout_Utils class.
 *
 * @since 1.0.0
 */
class Layout_Utils {

	/**
	 * Set layout from JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to set layout from.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function set_layout_from_file( string $file_path ) {
		$dashboard_data = JSON_Utils::parse_file( $file_path );

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		return Dashboard_Model::set_data( $dashboard_data );
	}

	/**
	 * Get layout as JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @return string|WP_Error JSON string or WP_Error on failure.
	 */
	public static function get_layout_json() {
		$dashboard_data = Dashboard_Model::get_data();

		if ( empty( $dashboard_data ) ) {
			return new WP_Error( 'no_dashboard_data', 'No dashboard data found' );
		}

		return JSON_Utils::encode_to_json( $dashboard_data );
	}

	/**
	 * Set layout from JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $json_string JSON string to set layout from.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function set_layout_from_json( string $json_string ) {
		$dashboard_data = JSON_Utils::decode_from_json( $json_string );

		if ( is_wp_error( $dashboard_data ) ) {
			return $dashboard_data;
		}

		return Dashboard_Model::set_data( $dashboard_data );
	}
}
