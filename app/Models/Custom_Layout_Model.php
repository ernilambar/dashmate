<?php
/**
 * Custom_Layout_Model
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Models;

use WP_Error;

/**
 * Custom_Layout_Model class.
 *
 * @since 1.0.0
 */
class Custom_Layout_Model {

	/**
	 * Option key prefix for custom layouts.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_KEY_PREFIX = 'dashmate_dashboard_custom_';

	/**
	 * Get custom layout data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Custom layout key.
	 *
	 * @return array|WP_Error
	 */
	public static function get_data( string $key ) {
		$option_key = self::get_option_key( $key );
		$data       = get_option( $option_key, null );

		if ( null === $data || empty( $data ) ) {
			return new WP_Error( 'custom_layout_not_found', esc_html__( 'Custom layout not found: ', 'dashmate' ) . $key );
		}

		return self::prepare_data( (array) $data );
	}

	/**
	 * Save custom layout data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key  Custom layout key.
	 * @param array  $data Custom layout data.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function set_data( string $key, array $data ) {
		$option_key             = self::get_option_key( $key );
		$prepared_data          = self::prepare_data( $data );
		$prepared_data['_meta'] = [
			'created' => current_time( 'mysql' ),
			'updated' => current_time( 'mysql' ),
			'key'     => $key,
		];

		$result = update_option( $option_key, $prepared_data, false );

		if ( false === $result ) {
			return new WP_Error( 'save_failed', esc_html__( 'Failed to save custom layout data.', 'dashmate' ) );
		}

		return true;
	}

	/**
	 * Delete custom layout data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Custom layout key.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete_data( string $key ) {
		$option_key = self::get_option_key( $key );
		$result     = delete_option( $option_key );

		if ( false === $result ) {
			return new WP_Error( 'delete_failed', esc_html__( 'Failed to delete custom layout data.', 'dashmate' ) );
		}

		return true;
	}

	/**
	 * Check if custom layout data exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Custom layout key.
	 *
	 * @return bool True if custom layout data exists, false otherwise.
	 */
	public static function data_exists( string $key ): bool {
		$option_key = self::get_option_key( $key );
		return null !== get_option( $option_key, null );
	}

	/**
	 * Get all custom layout keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of custom layout keys.
	 */
	public static function get_all_keys(): array {
		global $wpdb;

		$option_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				self::OPTION_KEY_PREFIX . '%'
			)
		);

		$keys = [];
		foreach ( $option_keys as $option_key ) {
			$key = str_replace( self::OPTION_KEY_PREFIX, '', $option_key );
			if ( ! empty( $key ) ) {
				$keys[] = $key;
			}
		}

		return $keys;
	}

	/**
	 * Get all custom layouts with their data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of custom layouts with their data.
	 */
	public static function get_all_layouts(): array {
		$keys    = self::get_all_keys();
		$layouts = [];

		foreach ( $keys as $key ) {
			$data = self::get_data( $key );
			if ( ! is_wp_error( $data ) ) {
				$layouts[ $key ] = $data;
			}
		}

		return $layouts;
	}

	/**
	 * Get option key for custom layout.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Custom layout key.
	 *
	 * @return string Option key.
	 */
	private static function get_option_key( string $key ): string {
		return self::OPTION_KEY_PREFIX . sanitize_key( $key );
	}

	/**
	 * Prepare custom layout data with ensured structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to prepare.
	 *
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
}
