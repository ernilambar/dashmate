<?php
/**
 * Widget_Registry
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Registry class.
 *
 * @since 1.0.0
 */
class Widget_Registry {

	/**
	 * Initialize widget registry.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		self::register_default_widget_types();
	}

	/**
	 * Register default widget types.
	 *
	 * @since 1.0.0
	 */
	private static function register_default_widget_types() {
		// Register Quick Links Widget.
		Widget_Type_Manager::register_widget_type(
			'quick-links',
			[
				'name'            => 'Quick Links Widget',
				'description'     => 'Display a list of external links',
				'icon'            => 'admin-links',
				'settings_schema' => [
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
				],
			],
			[ __CLASS__, 'get_quick_links_content' ]
		);

		// Register HTML Widget.
		Widget_Type_Manager::register_widget_type(
			'html',
			[
				'name'            => 'HTML Widget',
				'description'     => 'Display custom HTML content',
				'icon'            => 'editor-code',
				'settings_schema' => [
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
				],
			],
			[ __CLASS__, 'get_html_content' ]
		);

		// Register Icon Box Widget.
		Widget_Type_Manager::register_widget_type(
			'iconbox',
			[
				'name'            => 'Icon Box Widget',
				'description'     => 'Display icon with title and subtitle',
				'icon'            => 'admin-appearance',
				'settings_schema' => [
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
				],
			],
			[ __CLASS__, 'get_iconbox_content' ]
		);

		// Register Progress Circle Widget.
		Widget_Type_Manager::register_widget_type(
			'progress-circle',
			[
				'name'            => 'Progress Circle Widget',
				'description'     => 'Display percentage in a circular progress indicator',
				'icon'            => 'chart-pie',
				'settings_schema' => [
					'percentage' => [
						'type'    => 'number',
						'label'   => 'Percentage',
						'min'     => 0,
						'max'     => 100,
						'default' => 75,
					],
					'label'      => [
						'type'        => 'text',
						'label'       => 'Label',
						'description' => 'Text to display inside the circle',
						'default'     => '75%',
					],
					'caption'    => [
						'type'        => 'text',
						'label'       => 'Caption',
						'description' => 'Caption text below the circle',
						'default'     => 'Completion Rate',
					],
					'color'      => [
						'type'    => 'select',
						'label'   => 'Color',
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
						],
						'default' => 'blue',
					],
				],
			],
			[ __CLASS__, 'get_progress_circle_content' ]
		);
	}

	/**
	 * Get quick links content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public static function get_quick_links_content( $widget_id, $settings ) {
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

		// Apply widget-specific customizations based on settings.
		$title = 'Quick Links';
		if ( ! empty( $settings['customTitle'] ) ) {
			$title = $settings['customTitle'];
		}

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

		return [
			'title' => $title,
			'links' => $links,
		];
	}

	/**
	 * Get HTML content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public static function get_html_content( $widget_id, $settings ) {
		return [
			'html_content' => $settings['html_content'] ?? '<p>Sample HTML content from API</p>',
		];
	}

	/**
	 * Get iconbox content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public static function get_iconbox_content( $widget_id, $settings ) {
		return [
			'icon'     => $settings['icon'] ?? 'dashicons-admin-users',
			'title'    => $settings['title'] ?? 'Sample Icon Box',
			'subtitle' => $settings['subtitle'] ?? 'This content comes from API',
		];
	}

	/**
	 * Get progress circle content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public static function get_progress_circle_content( $widget_id, $settings ) {
		return [
			'percentage' => $settings['percentage'] ?? 75,
			'label'      => $settings['label'] ?? '75%',
			'caption'    => $settings['caption'] ?? 'Sample Progress',
		];
	}
}
