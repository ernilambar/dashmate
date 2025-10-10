<?php
/**
 * Dashboard_Model
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Models;

use Nilambar\Dashmate\Layout_Manager;
use WP_Error;

/**
 * Dashboard_Model class.
 *
 * @since 1.0.0
 */
class Dashboard_Model {

	/**
	 * Option key for dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_KEY = 'dashmate_dashboard_data';

	/**
	 * Starter layout for the main dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $starter_layout = 'default';

	/**
	 * Get dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Dashboard data with ensured structure.
	 */
	public static function get_data(): array {
		$data = get_option( self::OPTION_KEY, null );

		// If no data exists, load the starter layout.
		if ( null === $data ) {
			$data = self::load_starter_layout();
		}

		$data = ( null === $data ) ? [] : $data;

		return self::prepare_data( $data );
	}

	/**
	 * Save dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function set_data( array $data ) {
		$prepared_data  = self::prepare_data( $data );
		$sanitized_data = self::sanitize_dashboard_data( $prepared_data );

		$result = update_option( self::OPTION_KEY, $sanitized_data, false );

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
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete_data() {
		$result = delete_option( self::OPTION_KEY );

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
	 * @return bool True if dashboard data exists, false otherwise.
	 */
	public static function data_exists(): bool {
		return null !== get_option( self::OPTION_KEY, null );
	}

	/**
	 * Load starter layout data.
	 *
	 * @since 1.0.0
	 *
	 * @return array|null Layout data or null if not found.
	 */
	private static function load_starter_layout() {
		// Get the starter layout from the dashboard page instance.
		$starter_layout = self::get_starter_layout_from_dashboard_page();

		// Use Layout_Manager to get layout data (supports filters and validation).
		$layout_data = Layout_Manager::get_layout_data( $starter_layout );

		// Return null if there's an error.
		if ( is_wp_error( $layout_data ) ) {
			return null;
		}

		return $layout_data;
	}

	/**
	 * Get starter layout from dashboard page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Starter layout name.
	 */
	private static function get_starter_layout_from_dashboard_page() {
		return self::$starter_layout;
	}

	/**
	 * Set starter layout for the main dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @param string $layout Layout name.
	 */
	public static function set_starter_layout( $layout ) {
		self::$starter_layout = $layout;
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
