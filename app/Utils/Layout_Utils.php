<?php
/**
 * Layout_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

/**
 * Layout_Utils class.
 *
 * @since 1.0.0
 */
class Layout_Utils {

	/**
	 * Option key for dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_KEY = 'dashmate_dashboard_data';

	/**
	 * Save layout to JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to save to.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function save_layout_to_file( string $file_path ) {
		$dashboard_data = get_option( self::OPTION_KEY );

		if ( false === $dashboard_data ) {
			return new WP_Error( 'no_dashboard_data', 'No dashboard data found' );
		}

		return JSON_Utils::save_json_to_file( $file_path, $dashboard_data );
	}

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

		$result = update_option( self::OPTION_KEY, $dashboard_data );

		if ( false === $result ) {
			return new WP_Error( 'update_option_failed', 'Failed to update dashboard data' );
		}

		return true;
	}

	/**
	 * Get current layout data.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|WP_Error Layout data or WP_Error if not found.
	 */
	public static function get_layout_data() {
		$data = get_option( self::OPTION_KEY );

		if ( false === $data ) {
			return new WP_Error( 'no_layout_data', 'No layout data found' );
		}

		return $data;
	}

	/**
	 * Check if layout data exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if layout data exists, false otherwise.
	 */
	public static function layout_exists(): bool {
		$data = get_option( self::OPTION_KEY );
		return false !== $data;
	}

	/**
	 * Get layout as JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @return string|WP_Error JSON string or WP_Error on failure.
	 */
	public static function get_layout_json() {
		$dashboard_data = get_option( self::OPTION_KEY );

		if ( false === $dashboard_data ) {
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

		$result = update_option( self::OPTION_KEY, $dashboard_data );

		if ( false === $result ) {
			return new WP_Error( 'update_option_failed', 'Failed to update dashboard data' );
		}

		return true;
	}

	/**
	 * Delete layout data.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete_layout_data() {
		$result = delete_option( self::OPTION_KEY );

		if ( false === $result ) {
			return new WP_Error( 'delete_option_failed', 'Failed to delete layout data' );
		}

		return true;
	}

	/**
	 * Get layout data as array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Layout data as array.
	 */
	public static function get_layout_array(): array {
		$data = get_option( self::OPTION_KEY );

		if ( false === $data ) {
			return [];
		}

		return is_array( $data ) ? $data : [];
	}
}
