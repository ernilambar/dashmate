<?php
/**
 * HTML_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * HTML_Widget class.
 *
 * @since 1.0.0
 */
class HTML_Widget extends Abstract_Widget {

	/**
	 * Initialize the widget.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->type = 'html';
		$this->name = 'HTML Widget';
		$this->description = 'Display custom HTML content';
		$this->icon = 'editor-code';
		$this->settings_schema = [
			'html_content'  => [
				'type'        => 'textarea',
				'label'       => 'HTML Content',
				'description' => 'Enter HTML content to display',
				'default'     => '<p>Enter your HTML content here...</p>',
			],
			'allow_scripts' => [
				'type'        => 'checkbox',
				'label'       => 'Allow Scripts',
				'description' => 'Allow JavaScript execution',
				'default'     => false,
			],
		];
		$this->output_schema = [
			'html_content' => [
				'type'     => 'string',
				'required' => true,
				'description' => 'HTML content to render',
			],
			'allow_scripts' => [
				'type'     => 'boolean',
				'required' => true,
				'description' => 'Whether scripts are allowed',
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
			'html_content' => $settings['html_content'] ?? '<p>No HTML content provided</p>',
			'allow_scripts' => $settings['allow_scripts'] ?? false,
		];
	}
}
