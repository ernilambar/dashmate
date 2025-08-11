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
	 * Get enhanced dashboard data with titles and filters applied.
	 *
	 * @since 1.0.0
	 *
	 * @return array Enhanced dashboard data.
	 */
	public static function get_enhanced_dashboard_data(): array {
		$data = self::get_dashboard_data();

		// Get widget information to include titles.
		$widget_types = Widget_Dispatcher::get_widget_types_for_frontend();

		// Enhance widget data with titles and other information.
		if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
			foreach ( $data['columns'] as &$column ) {
				if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
					foreach ( $column['widgets'] as &$widget ) {
						if ( isset( $widget['id'] ) && isset( $widget_types[ $widget['id'] ] ) ) {
							$widget_info           = $widget_types[ $widget['id'] ];
							$widget['title']       = $widget_info['name'] ?? $widget_info['title'] ?? $widget['id'];
							$widget['description'] = $widget_info['description'] ?? '';
							$widget['icon']        = $widget_info['icon'] ?? 'settings';
						} else {
							// Fallback for widgets not found in registry.
							$widget['title']       = $widget['id'];
							$widget['description'] = '';
							$widget['icon']        = 'settings';
						}
					}
				}
			}
		}

		// Apply dashboard data filter to the entire dashboard data.
		$data = apply_filters( 'dashmate_dashboard_data', $data );

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
