<?php
/**
 * Weekly_Tickets_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;
use Nilambar\Dashmate\Utils\Review_Utils;

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
		parent::__construct( $id, 'progress-circles', esc_html__( 'Weekly Tickets', 'dashmate' ) );
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
			'circlesNumber' => [
				'type'        => 'number',
				'label'       => 'Circles Number',
				'description' => 'Number of progress circles to display',
				'default'     => 4,
				'min'         => 1,
				'max'         => 8,
				'refresh' => true, // This field triggers data refetch
			],
			'hideCaption'   => [
				'type'        => 'checkbox',
				'label'       => 'Hide Caption',
				'description' => 'Hide captions below progress circles',
				'default'     => false,
				'refresh' => false, // This field is handled by React
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
		$circles_number = absint( $settings['circlesNumber'] ?? 4 );

		$circles = Review_Utils::prepare_review_stats( $settings, $circles_number );

		return [
			'items' => $circles,
		];
	}
}
