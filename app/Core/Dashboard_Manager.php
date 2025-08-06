<?php
/**
 * Dashboard_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Core;

use WP_Error;

/**
 * Dashboard_Manager class.
 *
 * Single source of truth for dashboard data operations.
 *
 * @since 1.0.0
 */
class Dashboard_Manager {

	/**
	 * Option key for dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_KEY = 'dashmate_dashboard_data';

	/**
	 * Get dashboard data from WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Dashboard data.
	 */
	public static function get_dashboard_data(): array {
		$data = get_option( self::OPTION_KEY, null );

		// Return empty structure if no data exists (plugin not activated or data cleared).
		if ( null === $data ) {
			return self::get_empty_dashboard_structure();
		}

		// Ensure we always return the expected structure.
		$data = self::ensure_dashboard_structure( $data );

		return $data;
	}

	/**
	 * Save dashboard data to WordPress options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function save_dashboard_data( array $data ) {
		// Ensure the data has the correct structure before saving.
		$data = self::ensure_dashboard_structure( $data );

		$result = update_option( self::OPTION_KEY, $data, false );

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
	public static function delete_dashboard_data() {
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
	public static function dashboard_data_exists(): bool {
		$data = get_option( self::OPTION_KEY, null );
		return null !== $data;
	}

	/**
	 * Get empty dashboard structure.
	 *
	 * @since 1.0.0
	 *
	 * @return array Empty dashboard structure.
	 */
	public static function get_empty_dashboard_structure(): array {
		return [
			'layout'         => [
				'columns' => [],
			],
			'widgets'        => [],
			'column_widgets' => [],
		];
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
	private static function ensure_dashboard_structure( array $data ): array {
		// Ensure layout structure exists.
		if ( ! isset( $data['layout'] ) || ! is_array( $data['layout'] ) ) {
			$data['layout'] = [];
		}

		if ( ! isset( $data['layout']['columns'] ) || ! is_array( $data['layout']['columns'] ) ) {
			$data['layout']['columns'] = [];
		}

		// Ensure widgets array exists.
		if ( ! isset( $data['widgets'] ) || ! is_array( $data['widgets'] ) ) {
			$data['widgets'] = [];
		}

		// Ensure column_widgets array exists.
		if ( ! isset( $data['column_widgets'] ) || ! is_array( $data['column_widgets'] ) ) {
			$data['column_widgets'] = [];
		}

		return $data;
	}
}
