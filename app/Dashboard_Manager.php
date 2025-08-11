<?php
/**
 * Dashboard_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use Nilambar\Dashmate\Models\Dashboard_Model;
use WP_Error;

/**
 * Dashboard_Manager class.
 *
 * Service layer for dashboard operations. Handles business logic
 * while delegating data operations to Dashboard_Model.
 *
 * @since 1.0.0
 */
class Dashboard_Manager {

	/**
	 * Get dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Dashboard data.
	 */
	public static function get_dashboard_data(): array {
		return self::get_filtered_dashboard_data();
	}

	/**
	 * Get raw dashboard data without filtering.
	 *
	 * @since 1.0.0
	 *
	 * @return array Dashboard data.
	 */
	public static function get_raw_dashboard_data(): array {
		return Dashboard_Model::get_data();
	}

	/**
	 * Get filtered dashboard data with disabled widgets removed.
	 *
	 * @since 1.0.0
	 *
	 * @return array Filtered dashboard data.
	 */
	public static function get_filtered_dashboard_data(): array {
		$data = self::get_raw_dashboard_data();

		// Get disabled widgets from centralized method.
		$disabled_widgets = Widget_Manager::get_disabled_widgets();

		if ( empty( $disabled_widgets ) ) {
			return $data;
		}

		// Filter out disabled widgets from columns.
		if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
			foreach ( $data['columns'] as &$column ) {
				if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
					$column['widgets'] = array_values(
						array_filter(
							$column['widgets'],
							function ( $widget ) use ( $disabled_widgets ) {
								return ! isset( $widget['id'] ) || ! in_array( $widget['id'], $disabled_widgets, true );
							}
						)
					);
				}
			}
		}

		return $data;
	}

	/**
	 * Save dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function save_dashboard_data( array $data ) {
		return Dashboard_Model::set_data( $data );
	}

	/**
	 * Delete dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete_dashboard_data() {
		return Dashboard_Model::delete_data();
	}

	/**
	 * Check if dashboard data exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if dashboard data exists, false otherwise.
	 */
	public static function dashboard_data_exists(): bool {
		return Dashboard_Model::data_exists();
	}

	/**
	 * Get empty dashboard structure.
	 *
	 * @since 1.0.0
	 *
	 * @return array Empty dashboard structure.
	 */
	public static function get_empty_dashboard_structure(): array {
		return Dashboard_Model::get_data(); // This will return properly structured data.
	}

	/**
	 * Ensure dashboard data has the correct structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 *
	 * @return array Dashboard data with ensured structure.
	 */
	public static function ensure_dashboard_structure( array $data ): array {
		return Dashboard_Model::get_data(); // This will return properly structured data.
	}
}
