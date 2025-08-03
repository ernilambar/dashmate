<?php
/**
 * Layout_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

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
				'path'  => DASHMATE_DIR . '/layouts/default.yml',
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
	 * @return array|null
	 */
	public static function get_layout( $slug ) {
		$layouts = self::get_layouts();

		return isset( $layouts[ $slug ] ) ? $layouts[ $slug ] : null;
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
}
