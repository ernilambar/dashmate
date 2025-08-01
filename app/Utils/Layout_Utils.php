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
	 * Export layout to YAML file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to export to.
	 * @return bool True on success, false on failure.
	 */
	public static function export_to_file( string $file_path ): bool {
		$dashboard_data = get_option( self::OPTION_KEY );

		if ( false === $dashboard_data ) {
			return false;
		}

		return YML_Utils::save_to_file( $file_path, $dashboard_data );
	}

	/**
	 * Import layout from YAML file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path to import from.
	 * @return bool True on success, false on failure.
	 */
	public static function import_from_file( string $file_path ): bool {
		$dashboard_data = YML_Utils::load_from_file( $file_path );

		if ( null === $dashboard_data ) {
			return false;
		}

		$result = update_option( self::OPTION_KEY, $dashboard_data );

		return false !== $result;
	}

	/**
	 * Get current layout data.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Layout data or false if not found.
	 */
	public static function get_layout_data() {
		return get_option( self::OPTION_KEY );
	}

	/**
	 * Check if layout data exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if layout data exists, false otherwise.
	 */
	public static function has_layout_data(): bool {
		$data = get_option( self::OPTION_KEY );
		return false !== $data;
	}

	/**
	 * Export layout to YAML string.
	 *
	 * @since 1.0.0
	 *
	 * @return string|false YAML string or false on failure.
	 */
	public static function export_to_string() {
		$dashboard_data = get_option( self::OPTION_KEY );

		if ( false === $dashboard_data ) {
			return false;
		}

		return YML_Utils::to_yaml( $dashboard_data );
	}

	/**
	 * Import layout from YAML string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $yaml_string YAML string to import.
	 * @return bool True on success, false on failure.
	 */
	public static function import_from_string( string $yaml_string ): bool {
		$dashboard_data = YML_Utils::from_yaml( $yaml_string );

		if ( null === $dashboard_data ) {
			return false;
		}

		$result = update_option( self::OPTION_KEY, $dashboard_data );

		return false !== $result;
	}

	/**
	 * Delete layout data.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public static function delete_layout_data(): bool {
		return delete_option( self::OPTION_KEY );
	}

	/**
	 * Get layout data as array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Layout data as array.
	 */
	public static function get_layout_data_as_array(): array {
		$data = get_option( self::OPTION_KEY );

		if ( false === $data ) {
			return [];
		}

		return is_array( $data ) ? $data : [];
	}
}
