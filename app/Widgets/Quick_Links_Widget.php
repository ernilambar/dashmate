<?php
/**
 * Quick_Links_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Quick_Links_Widget class.
 *
 * @since 1.0.0
 */
class Quick_Links_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'links', esc_html__( 'Quick Links Widget', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = 'Display quick access links';
		$this->icon        = 'admin-links';

		$this->settings_schema = [
			'hide_icon'     => [
				'type'        => 'checkbox',
				'label'       => 'Hide Icons',
				'description' => 'Hide link icons',
				'default'     => false,
				'refresh'     => false,
			],
			'display_style' => [
				'type'        => 'buttonset',
				'label'       => 'Link Style',
				'description' => 'Choose how links are displayed',
				'default'     => 'list',
				'refresh'     => false,
				'choices'     => [
					[
						'value' => 'list',
						'label' => esc_html__( 'List', 'dashmate' ),
					],
					[
						'value' => 'grid',
						'label' => esc_html__( 'Grid', 'dashmate' ),
					],
				],
			],
		];

		$this->output_schema = [
			'links' => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Array of link objects with display settings',
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

		$links = $this->get_links( $settings );

		return [
			'links' => $links,
		];
	}

	/**
	 * Get links based on settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_links( $settings ) {
		$links = [
			[
				'title' => esc_html__( 'Home Page', 'dashmate' ),
				'url'   => home_url( '/' ),
				'icon'  => 'dashicons-admin-site',
			],
			[
				'title' => esc_html__( 'Administration', 'dashmate' ),
				'url'   => admin_url( '/' ),
				'icon'  => 'dashicons-admin-home',
			],
		];

		$links = apply_filters( 'dashmate_quick_links', $links );

		return array_values( $links );
	}
}
