<?php
/**
 * Sample_Links
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sample_Links class.
 *
 * @since 1.0.0
 */
class Sample_Links extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'links', esc_html__( 'Sample Links', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display sample links with various styles', 'dashmate' );
		$this->icon        = 'dataset_linked';

		$this->settings_schema = [
			'hide_icon'     => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Hide Icons', 'dashmate' ),
				'description' => esc_html__( 'Hide link icons', 'dashmate' ),
				'default'     => false,
				'refresh'     => false,
			],
			'display_style' => [
				'type'        => 'buttonset',
				'label'       => esc_html__( 'Link Style', 'dashmate' ),
				'description' => esc_html__( 'Choose how links are displayed', 'dashmate' ),
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
				'description' => esc_html__( 'Array of link objects with display settings', 'dashmate' ),
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

		$links = $this->get_links( $settings );

		return [
			'links' => $links,
		];
	}

	/**
	 * Get links.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 * @return array Array of links.
	 */
	private function get_links( array $settings ): array {
		$links = [
			[
				'title' => esc_html__( 'WordPress.org', 'dashmate' ),
				'url'   => 'https://wordpress.org',
				'icon'  => 'language',
			],
			[
				'title' => esc_html__( 'WordPress Codex', 'dashmate' ),
				'url'   => 'https://codex.wordpress.org',
				'icon'  => 'menu_book',
			],
			[
				'title' => esc_html__( 'WordPress Support', 'dashmate' ),
				'url'   => 'https://wordpress.org/support',
				'icon'  => 'support_agent',
			],
			[
				'title' => esc_html__( 'WordPress Themes', 'dashmate' ),
				'url'   => 'https://wordpress.org/themes',
				'icon'  => 'palette',
			],
			[
				'title' => esc_html__( 'WordPress Plugins', 'dashmate' ),
				'url'   => 'https://wordpress.org/plugins',
				'icon'  => 'extension',
			],
		];

		return array_values( $links );
	}
}
