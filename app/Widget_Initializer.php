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
		// Initialize the new widget system.
		Widget_Registry::init();

		// Create default widget instances if they don't exist.
		self::create_default_widgets();
	}

	/**
	 * Create default widgets.
	 *
	 * @since 1.0.0
	 */
	private static function create_default_widgets() {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return;
		}

		// Check if widgets already exist.
		$existing_widgets = $dashboard_data['widgets'] ?? [];
		if ( ! empty( $existing_widgets ) ) {
			return; // Widgets already exist, don't overwrite them.
		}

		// Create default widgets only if none exist.
		$default_widgets = [
			[
				'id'        => 'welcome-html-1',
				'column_id' => 'col-1',
				'position'  => 1,
				'settings'  => [
					'allow_scripts' => false,
				],
			],
			[
				'id'        => 'quick-links-1',
				'column_id' => 'col-2',
				'position'  => 1,
				'settings'  => [
					'hideIcon'  => false,
					'linkStyle' => 'list',
				],
			],
		];

		$dashboard_data['widgets'] = $default_widgets;

		$result = self::save_dashboard_data( $dashboard_data );
		if ( is_wp_error( $result ) ) {
			error_log( 'Dashmate: Failed to create default widgets: ' . $result->get_error_message() );
		}
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

		// HTML Widget
		$widgets[] = [
			'id'       => 'html-1',
			'type'     => 'html',
			'settings' => [
				'allow_scripts' => false,
			],
		];

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
		$data = get_option( 'dashmate_dashboard_data', null );
		if ( null === $data ) {
			return [
				'layout'  => [
					'columns' => [
						[
							'id'    => 'col-1',
							'order' => 1,
							'width' => '50%',
						],
						[
							'id'    => 'col-2',
							'order' => 2,
							'width' => '50%',
						],
					],
				],
				'widgets' => [],
			];
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
		$result = update_option( 'dashmate_dashboard_data', $data, false );
		if ( false === $result ) {
			return new \WP_Error( 'option_update_error', 'Could not save dashboard data to options' );
		}
		return true;
	}
}
