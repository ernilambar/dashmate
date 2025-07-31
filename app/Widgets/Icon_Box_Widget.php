<?php
/**
 * Icon_Box_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Icon_Box_Widget class.
 *
 * @since 1.0.0
 */
class Icon_Box_Widget extends Abstract_Widget {

	/**
	 * Initialize the widget.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->type = 'iconbox';
		$this->name = 'Icon Box Widget';
		$this->description = 'Display icon with title and subtitle';
		$this->icon = 'admin-appearance';
		$this->settings_schema = [
			'icon'     => [
				'type'        => 'text',
				'label'       => 'Icon Class',
				'description' => 'CSS class for the icon (e.g., dashicons-admin-users)',
				'default'     => 'dashicons-admin-users',
			],
			'title'    => [
				'type'        => 'text',
				'label'       => 'Title',
				'description' => 'Main title text',
				'default'     => 'Sample Title',
			],
			'subtitle' => [
				'type'        => 'text',
				'label'       => 'Subtitle',
				'description' => 'Subtitle text',
				'default'     => 'Sample subtitle',
			],
			'color'    => [
				'type'    => 'select',
				'label'   => 'Color Theme',
				'options' => [
					[
						'value' => 'blue',
						'label' => 'Blue',
					],
					[
						'value' => 'green',
						'label' => 'Green',
					],
					[
						'value' => 'orange',
						'label' => 'Orange',
					],
					[
						'value' => 'red',
						'label' => 'Red',
					],
					[
						'value' => 'purple',
						'label' => 'Purple',
					],
				],
				'default' => 'blue',
			],
		];
		$this->output_schema = [
			'subtitle' => [
				'type'     => 'string',
				'required' => true,
				'description' => 'Subtitle text',
			],
			'color' => [
				'type'     => 'string',
				'required' => true,
				'description' => 'Color theme for the widget',
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
		// Merge settings with defaults.
		$settings = $this->merge_settings_with_defaults( $settings );

		return [
			'subtitle' => $settings['subtitle'] ?? 'This content comes from API',
			'color'    => $settings['color'] ?? 'blue',
		];
	}
}
