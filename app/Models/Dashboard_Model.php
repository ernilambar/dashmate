<?php
/**
 * Dashboard_Model
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Models;

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
	 * Get dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Dashboard data with ensured structure.
	 */
	public static function get_data(): array {
		$data = get_option( self::OPTION_KEY, null );

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
		$prepared_data = self::prepare_data( $data );

		$result = update_option( self::OPTION_KEY, $prepared_data, false );

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
	 * Prepare dashboard data with ensured structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to prepare.
	 * @return array Prepared data with ensured structure.
	 */
	private static function prepare_data( array $data ): array {
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
