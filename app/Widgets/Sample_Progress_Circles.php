<?php
/**
 * Sample_Progress_Circles
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sample_Progress_Circles class.
 *
 * @since 1.0.0
 */
class Sample_Progress_Circles extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'progress-circles', esc_html__( 'Sample Progress Circles', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display sample progress circles.', 'dashmate' );
		$this->icon        = 'pie_chart';

		$this->settings_schema = [
			'circles_number' => [
				'type'        => 'number',
				'label'       => esc_html__( 'Circles Number', 'dashmate' ),
				'description' => esc_html__( 'Number of progress circles to display.', 'dashmate' ),
				'default'     => 4,
				'min'         => 1,
				'max'         => 8,
				'refresh'     => true,
			],
			'hide_caption'   => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Hide Caption', 'dashmate' ),
				'description' => esc_html__( 'Hide captions below progress circles.', 'dashmate' ),
				'default'     => false,
				'refresh'     => false,
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

		$demo_circles = [
			[
				'percentage' => 75,
				'value'      => '75%',
				'caption'    => esc_html__( 'Task Completion', 'dashmate' ),
			],
			[
				'percentage' => 60,
				'value'      => '60%',
				'caption'    => esc_html__( 'Project Progress', 'dashmate' ),
			],
			[
				'percentage' => 90,
				'value'      => '90%',
				'caption'    => esc_html__( 'Code Quality', 'dashmate' ),
			],
			[
				'percentage' => 45,
				'value'      => '45%',
				'caption'    => esc_html__( 'Documentation', 'dashmate' ),
			],
			[
				'percentage' => 80,
				'value'      => '80%',
				'caption'    => esc_html__( 'Testing Coverage', 'dashmate' ),
			],
			[
				'percentage' => 95,
				'value'      => '95%',
				'caption'    => esc_html__( 'Performance', 'dashmate' ),
			],
			[
				'percentage' => 70,
				'value'      => '70%',
				'caption'    => esc_html__( 'User Satisfaction', 'dashmate' ),
			],
			[
				'percentage' => 85,
				'value'      => '85%',
				'caption'    => esc_html__( 'System Uptime', 'dashmate' ),
			],
		];

		$circles = [];

		$circles_number = $settings['circles_number'] ?? 4;
		$hide_caption   = $settings['hide_caption'] ?? false;

		$available_circles = array_slice( $demo_circles, 0, $circles_number );

		foreach ( $available_circles as $circle ) {
			$processed_circle = [
				'percentage' => $circle['percentage'],
				'value'      => $circle['value'],
				'caption'    => $circle['caption'],
			];

			$circles[] = $processed_circle;
		}

		return [
			'items' => $circles,
		];
	}
}
