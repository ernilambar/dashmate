<?php
/**
 * Widget_Initializer
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Initializer class.
 *
 * @since 1.0.0
 */
class Widget_Initializer {

	/**
	 * Initialize the widget system.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Initialize widget registry.
		Widget_Registry::init();

		// Create default widget instances if they don't exist.
		self::create_default_widgets();
	}

	/**
	 * Create default widget instances.
	 *
	 * @since 1.0.0
	 */
	private static function create_default_widgets() {
		// Check if dashboard already has widgets.
		$dashboard_data = self::get_dashboard_data();

		if ( ! is_wp_error( $dashboard_data ) && ! empty( $dashboard_data['columns'] ) ) {
			// Check if any column has widgets.
			$has_widgets = false;
			foreach ( $dashboard_data['columns'] as $column ) {
				if ( ! empty( $column['widgets'] ) ) {
					$has_widgets = true;
					break;
				}
			}

			if ( $has_widgets ) {
				// Widgets already exist, don't create defaults.
				return;
			}
		}

		// Create default dashboard structure if it doesn't exist.
		if ( is_wp_error( $dashboard_data ) || empty( $dashboard_data['columns'] ) ) {
			$dashboard_data = [
				'columns' => [
					[
						'id'      => 'col-1',
						'title'   => 'Main Column',
						'width'   => 'full',
						'widgets' => [],
					],
					[
						'id'      => 'col-2',
						'title'   => 'Sidebar',
						'width'   => 'sidebar',
						'widgets' => [],
					],
				],
			];
		}

		// Create widget instances.
		$widgets = self::create_widget_instances();

		// Add widgets to columns.
		$widget_index = 0;
		foreach ( $widgets as $widget ) {
			$column_index = $widget_index % 2; // Distribute between 2 columns.
			$dashboard_data['columns'][ $column_index ]['widgets'][] = $widget;
			++$widget_index;
		}

		// Save dashboard data.
		self::save_dashboard_data( $dashboard_data );
	}

	/**
	 * Create widget instances for each type.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private static function create_widget_instances() {
		$widgets = [];

		// Quick Links Widget
		$widgets[] = Widget_Manager::create_widget(
			'quick-links',
			[
				'customTitle' => 'Quick Access',
				'filterLinks' => 'content',
				'hideIcon'    => false,
				'showTitle'   => true,
				'linkStyle'   => 'list',
			]
		);

		// HTML Widget 1
		$widgets[] = Widget_Manager::create_widget(
			'html',
			[
				'html_content'  => '<h3>Welcome to Dashmate!</h3><p>This is a custom HTML widget. You can add any HTML content here.</p><ul><li>Feature 1</li><li>Feature 2</li><li>Feature 3</li></ul>',
				'allow_scripts' => false,
			]
		);

		// HTML Widget 2
		$widgets[] = Widget_Manager::create_widget(
			'html',
			[
				'html_content'  => '<div style="background: #f0f0f0; padding: 15px; border-radius: 5px;"><h4>📊 Dashboard Stats</h4><p><strong>Posts:</strong> 25<br><strong>Pages:</strong> 8<br><strong>Comments:</strong> 12</p></div>',
				'allow_scripts' => false,
			]
		);

		// Icon Box Widget
		$widgets[] = Widget_Manager::create_widget(
			'iconbox',
			[
				'icon'     => 'dashicons-admin-users',
				'title'    => 'User Management',
				'subtitle' => 'Manage your site users',
				'color'    => 'blue',
			]
		);

		// Progress Circle Widget
		$widgets[] = Widget_Manager::create_widget(
			'progress-circle',
			[
				'percentage' => 85,
				'label'      => '85%',
				'caption'    => 'Site Completion',
				'color'      => 'green',
			]
		);

		return $widgets;
	}

	/**
	 * Get dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	private static function get_dashboard_data() {
		$file_path = DASHMATE_DIR . '/data/dashboard.json';

		if ( ! file_exists( $file_path ) ) {
			return new \WP_Error( 'file_not_found', 'Dashboard file not found' );
		}

		$content = file_get_contents( $file_path );
		if ( false === $content ) {
			return new \WP_Error( 'file_read_error', 'Could not read dashboard file' );
		}

		$data = json_decode( $content, true );
		if ( null === $data ) {
			return new \WP_Error( 'json_decode_error', 'Invalid JSON in dashboard file' );
		}

		return $data;
	}

	/**
	 * Save dashboard data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Dashboard data.
	 *
	 * @return bool|WP_Error
	 */
	private static function save_dashboard_data( $data ) {
		$file_path = DASHMATE_DIR . '/data/dashboard.json';

		$json = json_encode( $data, JSON_PRETTY_PRINT );
		if ( false === $json ) {
			return new \WP_Error( 'json_encode_error', 'Could not encode dashboard data' );
		}

		$result = file_put_contents( $file_path, $json );
		if ( false === $result ) {
			return new \WP_Error( 'file_write_error', 'Could not write dashboard file' );
		}

		return true;
	}
}
