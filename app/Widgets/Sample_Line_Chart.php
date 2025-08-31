<?php
/**
 * Sample_Line_Chart
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sample_Line_Chart class.
 *
 * @since 1.0.0
 */
class Sample_Line_Chart extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'line-chart', esc_html__( 'Sample Line Chart', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display sample line chart data.', 'dashmate' );
		$this->icon        = 'line-chart';

		$this->settings_schema = [
			'points_number' => [
				'type'        => 'number',
				'label'       => esc_html__( 'Points Number', 'dashmate' ),
				'description' => esc_html__( 'Number of data points to display.', 'dashmate' ),
				'default'     => 12,
				'min'         => 3,
				'max'         => 100,
				'refresh'     => true,
			],
		];
	}

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	public function get_content( array $settings = [] ): array {
		$settings = $this->merge_settings_with_defaults( $settings );

		$demo_points = [
			[
				'value' => 85,
				'label' => esc_html__( 'Jan', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 92,
				'label' => esc_html__( 'Feb', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 78,
				'label' => esc_html__( 'Mar', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 95,
				'label' => esc_html__( 'Apr', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 88,
				'label' => esc_html__( 'May', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 176,
				'label' => esc_html__( 'Jun', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 89,
				'label' => esc_html__( 'Jul', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 94,
				'label' => esc_html__( 'Aug', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 82,
				'label' => esc_html__( 'Sep', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 91,
				'label' => esc_html__( 'Oct', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 87,
				'label' => esc_html__( 'Nov', 'dashmate' ),
				'color' => '#6facde',
			],
			[
				'value' => 93,
				'label' => esc_html__( 'Dec', 'dashmate' ),
				'color' => '#6facde',
			],
		];

		$points = [];

		$points_number = $settings['points_number'] ?? 12;

		$available_points = array_slice( $demo_points, 0, $points_number );

		foreach ( $available_points as $point ) {
			$processed_point = [
				'value' => $point['value'],
				'label' => $point['label'],
				'color' => $point['color'],
			];

			$points[] = $processed_point;
		}

		return [
			'items'          => $points,
			'chart_settings' => [
				'chart_title'    => esc_html__( 'Monthly Performance', 'dashmate' ),
				'chart_subtitle' => esc_html__( 'Data trends over the year', 'dashmate' ),
				'x_axis_label'   => esc_html__( 'Months', 'dashmate' ),
				'y_axis_label'   => esc_html__( 'Values', 'dashmate' ),
			],
		];
	}
}
