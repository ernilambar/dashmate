<?php
/**
 * Layout_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use Nilambar\Dashmate\Utils\JSON_Utils;
use Nilambar\Dashmate\Utils\Layout_Utils;
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
	 * @return array|WP_Error
	 */
	public static function get_layouts() {
		$layouts = [
			'current' => [
				'title' => esc_html__( 'Current Layout', 'dashmate' ),
				'type'  => 'options',
			],
			'default' => [
				'title' => esc_html__( 'Default', 'dashmate' ),
				'path'  => DASHMATE_DIR . '/layouts/default.json',
				'type'  => 'file',
			],
		];

		/**
		 * Filter the registered layouts.
		 *
		 * @since 1.0.0
		 *
		 * @param array $layouts Array of registered layouts.
		 */
		$layouts = apply_filters( 'dashmate_layouts', $layouts );

		foreach ( $layouts as $key => $layout ) {
			// Automatically add id field based on the array key.
			$layouts[ $key ]['id'] = $key;

			// Skip path validation for options-based layouts.
			if ( 'options' === ( $layout['type'] ?? 'file' ) ) {
				continue;
			}

			if ( ! array_key_exists( 'path', $layout ) ) {
				return new WP_Error( 'layout_path_missing', esc_html__( 'Layout file path not provided.', 'dashmate' ) );
			}

			if ( ! file_exists( $layout['path'] ) ) {
				return new WP_Error( 'layout_file_not_found', esc_html__( 'Layout file does not exist: ', 'dashmate' ) . $layout['path'] );
			}
		}

		return $layouts;
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

		if ( is_wp_error( $layouts ) ) {
			return $layouts;
		}

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

		if ( is_wp_error( $layouts ) ) {
			return false;
		}

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

		// Handle options-based layouts.
		if ( 'options' === ( $layout['type'] ?? 'file' ) ) {
			return self::get_current_layout_data();
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

		/**
		 * Filter layout data before returning.
		 *
		 * Allows others to modify layout data before it's returned.
		 *
		 * @since 1.0.0
		 *
		 * @param array $layout_data Layout data array.
		 * @param string $slug       Layout slug.
		 */
		return apply_filters( 'dashmate_layout_data', $layout_data, $slug );
	}

	/**
	 * Get current layout data from options.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	public static function get_current_layout_data() {
		$layout_data = Layout_Utils::get_layout_data();

		if ( is_wp_error( $layout_data ) ) {
			return $layout_data;
		}

		// Ensure we return the expected structure.
		if ( ! is_array( $layout_data ) ) {
			return new WP_Error( 'invalid_layout_data', esc_html__( 'Invalid layout data structure.', 'dashmate' ) );
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
