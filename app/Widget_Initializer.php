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
	 * Create default widgets if none exist.
	 *
	 * @since 1.0.0
	 */
	public static function create_default_widgets() {
		$dashboard_data = self::get_dashboard_data();

		if ( is_wp_error( $dashboard_data ) ) {
			return;
		}

		// Check if columns exist.
		$existing_columns = $dashboard_data['layout']['columns'] ?? [];
		if ( empty( $existing_columns ) ) {
			// Create three columns if none exist.
			$default_layout = Layout_Manager::get_default_layout();

			if ( is_wp_error( $default_layout ) ) {
				error_log( 'Dashmate: Failed to get default layout: ' . $default_layout->get_error_message() );
				return;
			}

			$dashboard_data['layout']['columns'] = $default_layout['layout']['columns'];
			$dashboard_data['column_widgets']    = $default_layout['column_widgets'];

			$result = self::save_dashboard_data( $dashboard_data );
			if ( is_wp_error( $result ) ) {
				error_log( 'Dashmate: Failed to create default columns: ' . $result->get_error_message() );
			}
		}

		// Check if column_widgets exists.
		$existing_column_widgets = $dashboard_data['column_widgets'] ?? [];
		if ( empty( $existing_column_widgets ) ) {
			// Create column_widgets mapping based on existing widgets.
			$existing_widgets = $dashboard_data['widgets'] ?? [];
			$column_widgets   = [
				'col-1' => [],
				'col-2' => [],
				'col-3' => [],
			];

			foreach ( $existing_widgets as $widget ) {
				if ( isset( $widget['column_id'] ) && isset( $widget['id'] ) ) {
					$column_id = $widget['column_id'];
					if ( ! isset( $column_widgets[ $column_id ] ) ) {
						$column_widgets[ $column_id ] = [];
					}
					$column_widgets[ $column_id ][] = $widget['id'];
				}
			}

			$dashboard_data['column_widgets'] = $column_widgets;

			$result = self::save_dashboard_data( $dashboard_data );
			if ( is_wp_error( $result ) ) {
				error_log( 'Dashmate: Failed to create column_widgets: ' . $result->get_error_message() );
			}
		}

		// Check if widgets already exist.
		$existing_widgets = $dashboard_data['widgets'] ?? [];
		if ( ! empty( $existing_widgets ) ) {
			return; // Widgets already exist, don't overwrite them.
		}

		// Create default widgets only if none exist.
		$default_widgets        = Layout_Manager::get_default_widgets();
		$default_column_widgets = Layout_Manager::get_default_column_widgets();

		if ( is_wp_error( $default_widgets ) ) {
			error_log( 'Dashmate: Failed to get default widgets: ' . $default_widgets->get_error_message() );
			return;
		}

		if ( is_wp_error( $default_column_widgets ) ) {
			error_log( 'Dashmate: Failed to get default column widgets: ' . $default_column_widgets->get_error_message() );
			return;
		}

		$dashboard_data['widgets']        = $default_widgets;
		$dashboard_data['column_widgets'] = $default_column_widgets;

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
					'columns' => [],
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
