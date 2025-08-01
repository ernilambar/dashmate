<?php
/**
 * Option
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Core;

/**
 * Option class.
 *
 * @since 1.0.0
 */
class Option {

	/**
	 * Return plugin option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	public static function get( string $key ) {
		$default_options = self::get_defaults();

		if ( empty( $key ) ) {
			return;
		}

		$current_options = (array) get_option( 'dashmate_options' );
		$current_options = wp_parse_args( $current_options, $default_options );

		$value = null;

		if ( array_key_exists( $key, $current_options ) ) {
			$value = $current_options[ $key ];
		}

		return $value;
	}

	/**
	 * Return default options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default options.
	 */
	public static function get_defaults(): array {
		return apply_filters(
			'dashmate_option_defaults',
			[
				'max_columns' => '3',
			]
		);
	}

	/**
	 * Return default value of given key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Default option value.
	 */
	public static function defaults( string $key ) {
		$value = null;

		$defaults = self::get_defaults();

		if ( ! empty( $key ) && array_key_exists( $key, $defaults ) ) {
			$value = $defaults[ $key ];
		}

		return $value;
	}
}
