<?php
/**
 * Review_Utils
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Utils;

/**
 * Review_Utils class.
 *
 * @since 1.0.0
 */
class Review_Utils {

	/**
	 * Returns review details from JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array with weeks as keys and total ticket counts as values.
	 */
	private static function get_review_details(): array {
		$output = [];

		// Check if the reviews file exists.
		if ( ! defined( 'DASHMATE_REVIEWS_FILE' ) || ! file_exists( DASHMATE_REVIEWS_FILE ) ) {
			return $output;
		}

		// Read and decode the JSON file.
		$json_content = file_get_contents( DASHMATE_REVIEWS_FILE );
		if ( false === $json_content ) {
			return $output;
		}

		$reviews_data = json_decode( $json_content, true );
		if ( null === $reviews_data || ! is_array( $reviews_data ) ) {
			return $output;
		}

		// Transform ticket-to-week mapping to week-to-total-tickets mapping.
		foreach ( $reviews_data as $ticket_id => $week_number ) {
			if ( ! isset( $output[ $week_number ] ) ) {
				$output[ $week_number ] = 0;
			}
			++$output[ $week_number ];
		}

		ksort( $output );

		return $output;
	}

	/**
	 * Prepares review details for React app.
	 *
	 * @since 1.0.0
	 * @param array $settings Settings.
	 * @return array An array of review details formatted for React app.
	 */
	public static function prepare_review_stats( $settings ): array {
		$output = [];

		$week_totals   = self::get_review_details();
		$target_number = 60;

		// Get circlesNumber setting, default to 4 if not set.
		$circles_number = isset( $settings['circlesNumber'] ) ? absint( $settings['circlesNumber'] ) : 4;

		// Transform week totals into the format expected by React app.
		foreach ( $week_totals as $week => $total_tickets ) {
			$percent = intval( ( $total_tickets * 100 ) / $target_number );

			$output[] = [
				'percentage' => min( 100, $percent ),
				'value'      => $total_tickets,
				'caption'    => "W: {$week}",
			];
		}

		// Return only the last $circles_number items from the array.
		$output = array_slice( $output, -$circles_number );

		return $output;
	}
}
