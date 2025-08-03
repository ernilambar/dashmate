<?php
/**
 * Welcome_HTML_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Welcome_HTML_Widget class.
 *
 * @since 1.0.0
 */
class Welcome_HTML_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'html', esc_html__( 'Welcome Widget', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = 'Display welcome message';
		$this->icon        = 'editor-code';

		$this->settings_schema = [];

		$this->output_schema = [
			'html_content' => [
				'type'        => 'string',
				'required'    => true,
				'description' => 'HTML content to render',
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

		return [
			'html_content' => '<h3>Welcome to Dashmate!</h3><p>This is a custom HTML widget. You can add any HTML content here.</p>',
		];
	}
}
