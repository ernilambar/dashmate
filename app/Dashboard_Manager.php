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
			// Convert widgets object to array for frontend consistency.
			if ( isset( $data['widgets'] ) && is_array( $data['widgets'] ) ) {
				$data['widgets'] = array_values( $data['widgets'] );
			}
			return $data;
		}

		// Handle widgets as object and convert to array for filtering.
		if ( isset( $data['widgets'] ) && is_array( $data['widgets'] ) ) {
			// Convert object to array if needed.
			$widgets_array = array_values( $data['widgets'] );

			// Filter out disabled widgets.
			$filtered_widgets = array_filter(
				$widgets_array,
				function ( $widget ) use ( $disabled_widgets ) {
					return ! isset( $widget['id'] ) || ! in_array( $widget['id'], $disabled_widgets, true );
				}
			);

			// Convert back to array for frontend.
			$data['widgets'] = array_values( $filtered_widgets );
		}

		// Create a list of active widget IDs for reference.
		$active_widget_ids = [];
		if ( isset( $data['widgets'] ) && is_array( $data['widgets'] ) ) {
			foreach ( $data['widgets'] as $widget ) {
				if ( isset( $widget['id'] ) ) {
					$active_widget_ids[] = $widget['id'];
				}
			}
		}

		// Filter out disabled widgets from column_widgets and ensure consistency.
		if ( isset( $data['column_widgets'] ) && is_array( $data['column_widgets'] ) ) {
			foreach ( $data['column_widgets'] as $column_id => &$widget_ids ) {
				if ( is_array( $widget_ids ) ) {
					// Filter out disabled widgets and ensure they exist in the widgets array.
					$widget_ids = array_values(
						array_filter(
							$widget_ids,
							function ( $widget_id ) use ( $disabled_widgets, $active_widget_ids ) {
								// Remove if widget is disabled or doesn't exist in widgets array.
								return ! in_array( $widget_id, $disabled_widgets, true ) &&
										in_array( $widget_id, $active_widget_ids, true );
							}
						)
					);
				} else {
					// Ensure it's always an array.
					$widget_ids = [];
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
