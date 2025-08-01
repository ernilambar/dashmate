<?php
/**
 * PHP_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

/**
 * PHP_Utils class.
 *
 * @since 1.0.0
 */
class PHP_Utils {

	/**
	 * Checks if URL is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL.
	 * @return bool true if the URL is valid, otherwise false.
	 */
	public static function is_valid_url( $url ) {
		return filter_var( $url, FILTER_VALIDATE_URL ) === $url && str_starts_with( $url, 'http' );
	}

	/**
	 * Returns time ago string from time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $time Time.
	 * @return string Time ago string.
	 */
	public static function get_time_ago( int $time ): string {
		$output = '';

		$time_difference = time() - $time;

		if ( $time_difference < 1 ) {
			$output = '1 sec';
		} else {
			$condition = [
				12 * 30 * 24 * 60 * 60 => 'yr',
				30 * 24 * 60 * 60      => 'mth',
				24 * 60 * 60           => 'day',
				60 * 60                => 'hr',
				60                     => 'min',
				1                      => 'sec',
			];

			foreach ( $condition as $secs => $str ) {
				$d = $time_difference / $secs;

				if ( $d >= 1 ) {
					$t = round( $d );

					$output = $t . ' ' . $str . ( $t > 1 ? 's' : '' );
					break;
				}
			}
		}

		return $output;
	}
}
