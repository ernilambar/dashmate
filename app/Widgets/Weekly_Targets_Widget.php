<?php
/**
 * Weekly_Targets_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Weekly_Targets_Widget class.
 *
 * @since 1.0.0
 */
class Weekly_Targets_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'progress-circles', esc_html__( 'Weekly Targets', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = 'Display progress circles for weekly ticket statistics';
		$this->icon        = 'feedback';

		$this->settings_schema = [
			'circles_number' => [
				'type'        => 'number',
				'label'       => 'Circles Number',
				'description' => 'Number of progress circles to display',
				'default'     => 4,
				'min'         => 1,
				'max'         => 8,
				'refresh'     => true,
			],
			'hide_caption'   => [
				'type'        => 'checkbox',
				'label'       => 'Hide Caption',
				'description' => 'Hide captions below progress circles',
				'default'     => false,
				'refresh'     => false,
			],

		];

		$this->output_schema = [
			'items' => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Array of circle objects',
			],
		];
	}

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public function get_content( $widget_id = null, $settings = [] ) {
		$settings = $this->merge_settings_with_defaults( $settings );

		$demo_circles = [
			[
				'percentage' => 85,
				'value'      => '85%',
				'caption'    => 'Weekly Tickets',
			],
			[
				'percentage' => 72,
				'value'      => '72%',
				'caption'    => 'Bug Fixes',
			],
			[
				'percentage' => 93,
				'value'      => '93%',
				'caption'    => 'Feature Requests',
			],
			[
				'percentage' => 68,
				'value'      => '68%',
				'caption'    => 'Support Tickets',
			],
			[
				'percentage' => 91,
				'value'      => '91%',
				'caption'    => 'Code Reviews',
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
