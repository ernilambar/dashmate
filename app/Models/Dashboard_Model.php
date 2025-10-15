<?php
/**
 * Dashboard_Model
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Models;

use Nilambar\Dashmate\Utils\Layout_Utils;
use WP_Error;

/**
 * Dashboard_Model class.
 *
 * @since 1.0.0
 */
class Dashboard_Model {

	/**
	 * Base option key for dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_KEY_BASE = 'dashmate_dashboard_data';

	/**
	 * Starter layout file paths by dashboard ID.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $starter_layouts = [];


	/**
	 * Get dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 * @return array Dashboard data with ensured structure.
	 */
	public static function get_data( string $dashboard_id ): array {
		$option_key = self::get_option_key( $dashboard_id );
		$data       = get_option( $option_key, null );

		// If no data exists, load the starter layout.
		if ( null === $data ) {
			$data = self::load_starter_layout( $dashboard_id );
		}

		$data = ( null === $data ) ? [] : $data;

		return self::prepare_data( $data );
	}

	/**
	 * Save dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data         Dashboard data.
	 * @param string $dashboard_id Dashboard ID.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function set_data( array $data, string $dashboard_id ) {
		$prepared_data  = self::prepare_data( $data );
		$sanitized_data = self::sanitize_dashboard_data( $prepared_data );
		$option_key     = self::get_option_key( $dashboard_id );

		$result = update_option( $option_key, $sanitized_data, false );

		if ( false === $result ) {
			return new WP_Error( 'save_failed', 'Failed to save dashboard data' );
		}

		return true;
	}

	/**
	 * Delete dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete_data( string $dashboard_id ) {
		$option_key = self::get_option_key( $dashboard_id );
		$result     = delete_option( $option_key );

		if ( false === $result ) {
			return new WP_Error( 'delete_failed', 'Failed to delete dashboard data' );
		}

		return true;
	}

	/**
	 * Check if dashboard data exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 * @return bool True if dashboard data exists, false otherwise.
	 */
	public static function data_exists( string $dashboard_id ): bool {
		$option_key = self::get_option_key( $dashboard_id );
		return null !== get_option( $option_key, null );
	}

	/**
	 * Load starter layout data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 * @return array|null Layout data or null if not found.
	 */
	private static function load_starter_layout( string $dashboard_id ) {
		// If no layout path is set for this dashboard, return null (will result in empty dashboard).
		if ( empty( self::$starter_layouts[ $dashboard_id ] ) ) {
			return null;
		}

		// Use Layout_Utils to get layout data from the JSON file.
		$layout_data = Layout_Utils::get_layout_data( self::$starter_layouts[ $dashboard_id ] );

		// Return null if there's an error.
		if ( is_wp_error( $layout_data ) ) {
			return null;
		}

		return $layout_data;
	}

	/**
	 * Set starter layout file path for a specific dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 * @param string $path         Path to the JSON layout file.
	 */
	public static function set_starter_layout( $dashboard_id, $path ) {
		self::$starter_layouts[ $dashboard_id ] = $path;
	}

	/**
	 * Get option key for dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dashboard_id Dashboard ID.
	 * @return string Option key.
	 */
	private static function get_option_key( string $dashboard_id ): string {
		return self::OPTION_KEY_BASE . '_' . sanitize_key( $dashboard_id );
	}

	/**
	 * Prepare dashboard data with ensured structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to prepare.
	 * @return array Prepared data with ensured structure.
	 */
	private static function prepare_data( array $data ): array {
		// Ensure columns array exists.
		if ( ! isset( $data['columns'] ) || ! is_array( $data['columns'] ) ) {
			$data['columns'] = [];
		}

		// Ensure each column has widgets array.
		foreach ( $data['columns'] as &$column ) {
			if ( ! isset( $column['widgets'] ) || ! is_array( $column['widgets'] ) ) {
				$column['widgets'] = [];
			}
		}

		return $data;
	}

	/**
	 * Sanitize dashboard data to prevent XSS and other security issues.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data to sanitize.
	 * @return array Sanitized dashboard data.
	 */
	private static function sanitize_dashboard_data( array $data ): array {
		// Sanitize column data.
		if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
			foreach ( $data['columns'] as &$column ) {
				// Sanitize column ID and title.
				if ( isset( $column['id'] ) ) {
					$column['id'] = sanitize_key( $column['id'] );
				}
				if ( isset( $column['title'] ) ) {
					$column['title'] = sanitize_text_field( $column['title'] );
				}

				// Sanitize widget data.
				if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
					foreach ( $column['widgets'] as &$widget ) {
						// Sanitize widget ID and title.
						if ( isset( $widget['id'] ) ) {
							$widget['id'] = sanitize_key( $widget['id'] );
						}
						if ( isset( $widget['title'] ) ) {
							$widget['title'] = sanitize_text_field( $widget['title'] );
						}
						if ( isset( $widget['description'] ) ) {
							$widget['description'] = sanitize_text_field( $widget['description'] );
						}
						if ( isset( $widget['icon'] ) ) {
							$widget['icon'] = sanitize_text_field( $widget['icon'] );
						}

						// Widget settings are already sanitized by the widget classes.
						// But we ensure the settings key exists and is an array.
						if ( isset( $widget['settings'] ) && ! is_array( $widget['settings'] ) ) {
							$widget['settings'] = [];
						}
					}
				}
			}
		}

		return $data;
	}
}
