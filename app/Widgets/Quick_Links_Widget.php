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
	 * Initialize the widget.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->type = 'quick-links';
		$this->name = 'Quick Links Widget';
		$this->description = 'Display a list of external links';
		$this->icon = 'admin-links';
		$this->settings_schema = [
			'hideIcon'    => [
				'type'        => 'checkbox',
				'label'       => 'Hide Icons',
				'description' => 'Hide icons from quick links',
				'default'     => false,
			],
			'showTitle'   => [
				'type'        => 'checkbox',
				'label'       => 'Show Widget Title',
				'description' => 'Display the widget title',
				'default'     => true,
			],
			'linkStyle'   => [
				'type'        => 'select',
				'label'       => 'Link Style',
				'description' => 'Choose the display style for links',
				'options'     => [
					[
						'value' => 'list',
						'label' => 'List Style',
					],
					[
						'value' => 'grid',
						'label' => 'Grid Style',
					],
					[
						'value' => 'compact',
						'label' => 'Compact Style',
					],
				],
				'default'     => 'list',
			],
			'customTitle' => [
				'type'        => 'text',
				'label'       => 'Custom Title',
				'description' => 'Override the default widget title',
				'default'     => '',
			],
			'filterLinks' => [
				'type'        => 'select',
				'label'       => 'Show Specific Links',
				'description' => 'Choose which links to display',
				'options'     => [
					[
						'value' => 'all',
						'label' => 'All Links',
					],
					[
						'value' => 'content',
						'label' => 'Content Only (Posts, Pages, Media)',
					],
					[
						'value' => 'admin',
						'label' => 'Admin Only (Users, Settings, Tools)',
					],
					[
						'value' => 'custom',
						'label' => 'Custom Selection',
					],
				],
				'default'     => 'all',
			],
		];
		$this->output_schema = [
			'links' => [
				'type'     => 'array',
				'required' => true,
				'description' => 'Array of link objects',
				'items' => [
					'title' => [
						'type'     => 'string',
						'required' => true,
						'description' => 'Link title',
					],
					'url' => [
						'type'     => 'string',
						'required' => true,
						'description' => 'Link URL',
					],
					'icon' => [
						'type'     => 'string',
						'required' => false,
						'description' => 'CSS class for the icon',
					],
				],
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

		// Apply widget-specific customizations based on settings.
		$title = 'Quick Links';
		if ( ! empty( $settings['customTitle'] ) ) {
			$title = $settings['customTitle'];
		}

		return [
			'title' => $title, // This will be used instead of the default
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
				'title' => 'Dashboard',
				'url'   => admin_url(),
				'icon'  => 'dashicons-admin-home',
			],
			[
				'title' => 'Posts',
				'url'   => admin_url( 'edit.php' ),
				'icon'  => 'dashicons-admin-post',
			],
			[
				'title' => 'Pages',
				'url'   => admin_url( 'edit.php?post_type=page' ),
				'icon'  => 'dashicons-admin-page',
			],
			[
				'title' => 'Media',
				'url'   => admin_url( 'upload.php' ),
				'icon'  => 'dashicons-admin-media',
			],
			[
				'title' => 'Comments',
				'url'   => admin_url( 'edit-comments.php' ),
				'icon'  => 'dashicons-admin-comments',
			],
			[
				'title' => 'Appearance',
				'url'   => admin_url( 'themes.php' ),
				'icon'  => 'dashicons-admin-appearance',
			],
			[
				'title' => 'Plugins',
				'url'   => admin_url( 'plugins.php' ),
				'icon'  => 'dashicons-admin-plugins',
			],
			[
				'title' => 'Users',
				'url'   => admin_url( 'users.php' ),
				'icon'  => 'dashicons-admin-users',
			],
			[
				'title' => 'Tools',
				'url'   => admin_url( 'tools.php' ),
				'icon'  => 'dashicons-admin-tools',
			],
			[
				'title' => 'Settings',
				'url'   => admin_url( 'options-general.php' ),
				'icon'  => 'dashicons-admin-generic',
			],
		];

		// Filter links based on settings if needed.
		if ( ! empty( $settings['filterLinks'] ) && $settings['filterLinks'] !== 'all' ) {
			switch ( $settings['filterLinks'] ) {
				case 'content':
					$allowed_titles = [ 'Posts', 'Pages', 'Media' ];
					break;
				case 'admin':
					$allowed_titles = [ 'Users', 'Settings', 'Tools', 'Appearance', 'Plugins' ];
					break;
				default:
					$allowed_titles = [];
			}

			if ( ! empty( $allowed_titles ) ) {
				$links = array_filter(
					$links,
					function ( $link ) use ( $allowed_titles ) {
						return in_array( $link['title'], $allowed_titles, true );
					}
				);
			}
		}

		return $links;
	}
}
