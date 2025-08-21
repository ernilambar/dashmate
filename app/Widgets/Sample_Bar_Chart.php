<?php
/**
 * Sample_Bar_Chart
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sample_Bar_Chart class.
 *
 * @since 1.0.0
 */
class Sample_Bar_Chart extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'bar-chart', esc_html__( 'Sample Bar Chart', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display sample bar chart data.', 'dashmate' );
		$this->icon        = 'bar_chart';

		$this->settings_schema = [
			'bars_number' => [
				'type'        => 'number',
				'label'       => esc_html__( 'Bars Number', 'dashmate' ),
				'description' => esc_html__( 'Number of bars to display.', 'dashmate' ),
				'default'     => 6,
				'min'         => 3,
				'max'         => 12,
				'refresh'     => true,
			],
			'hide_labels' => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Hide Labels', 'dashmate' ),
				'description' => esc_html__( 'Hide labels below bars.', 'dashmate' ),
				'default'     => false,
				'refresh'     => false,
			],
			'show_values' => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Values', 'dashmate' ),
				'description' => esc_html__( 'Show values on top of bars.', 'dashmate' ),
				'default'     => true,
				'refresh'     => false,
			],
		];

		$this->output_schema = [
			'items' => [
				'type'        => 'array',
				'required'    => true,
				'description' => esc_html__( 'Array of bar objects.', 'dashmate' ),
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

		$demo_bars = [
			[
				'value'  => 85,
				'label'  => esc_html__( 'Jan', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 92,
				'label'  => esc_html__( 'Feb', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 78,
				'label'  => esc_html__( 'Mar', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 95,
				'label'  => esc_html__( 'Apr', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 88,
				'label'  => esc_html__( 'May', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 76,
				'label'  => esc_html__( 'Jun', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 89,
				'label'  => esc_html__( 'Jul', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 94,
				'label'  => esc_html__( 'Aug', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 82,
				'label'  => esc_html__( 'Sep', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 91,
				'label'  => esc_html__( 'Oct', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 87,
				'label'  => esc_html__( 'Nov', 'dashmate' ),
				'color'  => '#6facde',
			],
			[
				'value'  => 93,
				'label'  => esc_html__( 'Dec', 'dashmate' ),
				'color'  => '#6facde',
			],
		];

		$bars = [];

		$bars_number = $settings['bars_number'] ?? 6;

		$available_bars = array_slice( $demo_bars, 0, $bars_number );

		foreach ( $available_bars as $bar ) {
			$processed_bar = [
				'value' => $bar['value'],
				'label' => $bar['label'],
				'color' => $bar['color'],
			];

			$bars[] = $processed_bar;
		}

		return [
			'items' => $bars,
		];
	}
}
