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
		parent::__construct( $id, 'links', 'Quick Links Widget' );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = 'Display quick access links to WordPress admin pages';
		$this->icon        = 'admin-links';

		$this->settings_schema = [
			'hideIcon'  => [
				'type'        => 'checkbox',
				'label'       => 'Hide Icons',
				'description' => 'Hide link icons',
				'default'     => false,
			],
			'linkStyle' => [
				'type'    => 'select',
				'label'   => 'Link Style',
				'description' => 'Choose how links are displayed',
				'options' => [
					[
						'value' => 'list',
						'label' => 'List',
					],
					[
						'value' => 'grid',
						'label' => 'Grid',
					],
				],
				'default' => 'list',
			],
		];

		$this->output_schema = [
			'links' => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Array of link objects',
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

		// Get links based on settings.
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
		// Define admin menu items with their icons and capabilities
		$admin_menu_items = [
			'themes.php'          => [
				'title' => 'Appearance',
				'icon'  => 'dashicons-admin-appearance',
				'cap'   => 'switch_themes',
			],
			'plugins.php'         => [
				'title' => 'Plugins',
				'icon'  => 'dashicons-admin-plugins',
				'cap'   => 'activate_plugins',
			],
			'tools.php'           => [
				'title' => 'Tools',
				'icon'  => 'dashicons-admin-tools',
				'cap'   => 'manage_options',
			],
			'options-general.php' => [
				'title' => 'Settings',
				'icon'  => 'dashicons-admin-settings',
				'cap'   => 'manage_options',
			],
		];

		$all_links = [];

		// Check if we're in an API context (no user logged in)
		$is_api_request = defined( 'REST_REQUEST' ) && REST_REQUEST;

		// Build links based on user capabilities or provide all links for API requests
		foreach ( $admin_menu_items as $page => $item ) {
			if ( $is_api_request || current_user_can( $item['cap'] ) ) {
				$all_links[] = [
					'title' => $item['title'],
					'url'   => admin_url( $page ),
					'icon'  => $item['icon'],
				];
			}
		}

		/**
		 * Filter the quick links.
		 *
		 * This filter allows other plugins to add or modify quick links.
		 *
		 * @since 1.0.0
		 *
		 * @param array $links Array of quick links.
		 */
		$all_links = apply_filters( 'dashmate_quick_links', $all_links );

		// Hide icons if setting is enabled.
		$hide_icon = $settings['hideIcon'] ?? false;
		if ( $hide_icon ) {
			foreach ( $all_links as &$link ) {
				unset( $link['icon'] );
			}
		}

		// Add link style to the output so frontend can apply appropriate CSS.
		$link_style = $settings['linkStyle'] ?? 'list';

		return [
			'links'     => array_values( $all_links ),
			'linkStyle' => $link_style,
			'hideIcon'  => $hide_icon,
		];
	}
}
