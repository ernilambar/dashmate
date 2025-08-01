<?php
/**
 * Weekly_Tickets_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Weekly_Tickets_Widget class.
 *
 * @since 1.0.0
 */
class Weekly_Tickets_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'progress-circles', 'Progress Widget' );

		$this->description = 'Display progress circles';
		$this->icon        = 'feedback';
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

		$circles = [
			[
				'percentage' => 70,
				'value'      => 70,
				'caption'    => 'W:43',
				'color'      => 'blue',
			],
			[
				'percentage' => 45,
				'value'      => 45,
				'caption'    => 'W:44',
				'color'      => 'green',
			],
			[
				'percentage' => 85,
				'value'      => 85,
				'caption'    => 'W:45',
				'color'      => 'orange',
			],
		];

		return [
			'items' => $circles,
		];
	}
}
