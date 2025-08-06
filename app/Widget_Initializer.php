<?php
/**
 * Widget_Initializer
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

use Nilambar\Dashmate\Core\Dashboard_Manager;

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
	}

	/**
	 * Get default layout file path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_layout_file_path() {
		return Layout_Manager::get_default_layout_file_path();
	}

	/**
	 * Create default dashboard layout and widgets on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public static function create_default_dashboard_on_activation() {
		// Only create defaults if no dashboard data exists at all.
		if ( Dashboard_Manager::dashboard_data_exists() ) {
			return; // Dashboard data already exists, don't overwrite.
		}

		// Create the default dashboard layout and widgets.
		self::create_default_dashboard_data();
	}

	/**
	 * Create default dashboard data.
	 *
	 * @since 1.0.0
	 */
	private static function create_default_dashboard_data() {
		// Get the default layout data.
		$default_layout = Layout_Manager::get_default_layout();

		if ( is_wp_error( $default_layout ) ) {
			error_log( 'Dashmate: Failed to get default layout: ' . $default_layout->get_error_message() );
			return;
		}

		// Save the default layout data using the centralized service.
		$result = Dashboard_Manager::save_dashboard_data( $default_layout );

		if ( is_wp_error( $result ) ) {
			error_log( 'Dashmate: Failed to create default dashboard data: ' . $result->get_error_message() );
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
		return Dashboard_Manager::get_dashboard_data();
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
		return Dashboard_Manager::save_dashboard_data( $data );
	}
}
