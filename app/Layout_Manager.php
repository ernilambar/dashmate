<?php
/**
 * Layout_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use WP_Error;

/**
 * Layout_Manager class.
 *
 * @since 1.0.0
 */
class Layout_Manager {

	/**
	 * Get registered layouts.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_layouts() {
		$layouts = [
			'default' => [
				'title' => esc_html__( 'Default', 'dashmate' ),
				'path'  => DASHMATE_DIR . '/layouts/default.json',
			],
		];

		/**
		 * Filter the registered layouts.
		 *
		 * @since 1.0.0
		 *
		 * @param array $layouts Array of registered layouts.
		 */
		return apply_filters( 'dashmate_layouts', $layouts );
	}

	/**
	 * Get layout by slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Layout slug.
	 *
	 * @return array|WP_Error
	 */
	public static function get_layout( $slug ) {
		$layouts = self::get_layouts();

		if ( ! isset( $layouts[ $slug ] ) ) {
			return new WP_Error( 'layout_not_found', esc_html__( 'Layout not found: ', 'dashmate' ) . $slug );
		}

		return $layouts[ $slug ];
	}

	/**
	 * Check if layout exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Layout slug.
	 *
	 * @return bool
	 */
	public static function layout_exists( $slug ) {
		$layouts = self::get_layouts();

		return isset( $layouts[ $slug ] );
	}

	/**
	 * Get default layout file path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_layout_file_path() {
		// Get default layout from Layout_Manager.
		$default_layout = self::get_layout( 'default' );

		if ( is_wp_error( $default_layout ) ) {
			// Fallback to hardcoded path if default layout not found.
			return DASHMATE_DIR . '/layouts/default.json';
		}

		return $default_layout['path'];
	}

	/**
	 * Get layout data by slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Layout slug.
	 *
	 * @return array|WP_Error
	 */
	public static function get_layout_data( $slug ) {
		$layout = self::get_layout( $slug );

		if ( is_wp_error( $layout ) ) {
			return $layout;
		}

		if ( ! file_exists( $layout['path'] ) || ! is_readable( $layout['path'] ) ) {
			return new WP_Error( 'layout_load_error', esc_html__( 'Layout file does not exist or is not readable: ', 'dashmate' ) . $layout['path'] );
		}

		$file_content = file_get_contents( $layout['path'] );

		if ( false === $file_content ) {
			return new WP_Error( 'layout_load_error', esc_html__( 'Failed to read layout file: ', 'dashmate' ) . $layout['path'] );
		}

		$layout_data = JSON_Utils::decode_from_json( $file_content );

		if ( is_wp_error( $layout_data ) ) {
			return new WP_Error( 'layout_load_error', esc_html__( 'Failed to parse layout data from file: ', 'dashmate' ) . $layout['path'] . ' - ' . $layout_data->get_error_message() );
		}

		return $layout_data;
	}

	/**
	 * Get default layout structure.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	public static function get_default_layout() {
		return self::get_layout_data( 'default' );
	}

	/**
	 * Get widgets from layout.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Layout slug.
	 *
	 * @return array|WP_Error
	 */
	public static function get_layout_widgets( $slug ) {
		$layout_data = self::get_layout_data( $slug );

		if ( is_wp_error( $layout_data ) ) {
			return $layout_data;
		}

		return $layout_data['widgets'] ?? [];
	}

	/**
	 * Get column widgets mapping from layout.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Layout slug.
	 *
	 * @return array|WP_Error
	 */
	public static function get_layout_column_widgets( $slug ) {
		$layout_data = self::get_layout_data( $slug );

		if ( is_wp_error( $layout_data ) ) {
			return $layout_data;
		}

		return $layout_data['column_widgets'] ?? [];
	}

	/**
	 * Get default widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	public static function get_default_widgets() {
		return self::get_layout_widgets( 'default' );
	}

	/**
	 * Get default column widgets mapping.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	public static function get_default_column_widgets() {
		return self::get_layout_column_widgets( 'default' );
	}
}
