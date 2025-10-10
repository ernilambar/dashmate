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
	 * @param string $app_slug App slug for multi-dashboard support.
	 * @return array Dashboard data.
	 */
	public static function get_dashboard_data( $app_slug = 'default' ): array {
		$data = self::get_raw_dashboard_data( $app_slug );

		// Apply dashboard data filter to allow filtering of widgets and other data.
		$data = apply_filters( 'dashmate_dashboard_data', $data );

		return $data;
	}

	/**
	 * Get raw dashboard data without filtering.
	 *
	 * @since 1.0.0
	 *
	 * @param string $app_slug App slug for multi-dashboard support.
	 * @return array Dashboard data.
	 */
	public static function get_raw_dashboard_data( $app_slug = 'default' ): array {
		return Dashboard_Model::get_data( $app_slug );
	}

	/**
	 * Get enhanced dashboard data with titles and filters applied.
	 *
	 * @since 1.0.0
	 *
	 * @param string $app_slug App slug for multi-dashboard support.
	 * @return array Enhanced dashboard data.
	 */
	public static function get_enhanced_dashboard_data( $app_slug = 'default' ): array {
		$data = self::get_raw_dashboard_data( $app_slug );

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
	 * @param array  $data     Dashboard data.
	 * @param string $app_slug App slug for multi-dashboard support.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function save_dashboard_data( array $data, $app_slug = 'default' ) {
		return Dashboard_Model::set_data( $data, $app_slug );
	}

	/**
	 * Delete dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $app_slug App slug for multi-dashboard support.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete_dashboard_data( $app_slug = 'default' ) {
		return Dashboard_Model::delete_data( $app_slug );
	}

	/**
	 * Check if dashboard data exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $app_slug App slug for multi-dashboard support.
	 * @return bool True if dashboard data exists, false otherwise.
	 */
	public static function dashboard_data_exists( $app_slug = 'default' ): bool {
		return Dashboard_Model::data_exists( $app_slug );
	}

	/**
	 * Get empty dashboard structure.
	 *
	 * @since 1.0.0
	 *
	 * @param string $app_slug App slug for multi-dashboard support.
	 * @return array Empty dashboard structure.
	 */
	public static function get_empty_dashboard_structure( $app_slug = 'default' ): array {
		return Dashboard_Model::get_data( $app_slug ); // This will return properly structured data.
	}

	/**
	 * Ensure dashboard data has the correct structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data     Dashboard data.
	 * @param string $app_slug App slug for multi-dashboard support.
	 *
	 * @return array Dashboard data with ensured structure.
	 */
	public static function ensure_dashboard_structure( array $data, $app_slug = 'default' ): array {
		return Dashboard_Model::get_data( $app_slug ); // This will return properly structured data.
	}
}
